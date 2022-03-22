<?php

namespace App\Controller\GestionContrat;

use App\Entity\Contrat\Contrat;
use App\Entity\User;
use App\Entity\UserJuridique;
use App\Form\Contrat\Juridique\Agent\AfficherType;
use App\Form\Contrat\Juridique\Agent\TraitementType;
use App\Form\Contrat\Juridique\Manager\AttributionType;
use App\Form\Contrat\Juridique\Manager\ConsultationAttribueeType;
use App\Form\Contrat\Manager\ContratManagerViewType;
use App\Repository\Contrat\ContratRepository;
use App\Service\Contrat\Juridique\Agent\TraiterDemandeContrat;
use App\Service\Contrat\Juridique\GetMesDemandes;
use App\Service\Contrat\Juridique\Manager\AttribuerDemandeAgent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @Route("/app/contrats/juridique")
 * @IsGranted("ROLE_JURIDIQUE")
 */
class JuridiqueController extends AbstractController
{
    /**
     * @var ContratRepository
     */
    private $contratRepository;
    /**
     * @var AttribuerDemandeAgent
     */
    private $attribuerDemandeAgentService;

    public function __construct(ContratRepository $contratRepository, AttribuerDemandeAgent $attribuerDemandeAgentService)
    {
        $this->contratRepository = $contratRepository;
        $this->attribuerDemandeAgentService = $attribuerDemandeAgentService;
    }

    /**
     * @Route("/", name="app_gestion_contrat_juridique")
     */
    public function index(): Response
    {
        $response = $this->redirectToRoute("app_gestion_contrat_juridique_home_agent");
        if($this->isGranted('ROLE_USER_BOSS_JURIDIQUE')){
            $response = $this->redirectToRoute("app_gestion_contrat_juridique_manager_listing", [
                'statutDemande' => 'demande_non_attribuee'
            ]);
        }
        return $response;
    }

    /**
     * Listing des demandes non attribuées
     * @IsGranted("ROLE_USER_BOSS_JURIDIQUE")
     * @Route("/manager/list/{statutDemande}", name="app_gestion_contrat_juridique_manager_listing", methods={"GET"})
     */
    public function manager_dmd_non_attribuee(string $statutDemande): Response
    {
        $lib = [
            'demande_non_attribuee' => [
                'title' => 'Attribuer les demandes aux agents',
                'chemin' => 'app_gestion_contrat_juridique_manager_attribution'
            ],
            'demande_attribuee' => [
                'title' => 'Consulter les demandes en cours de traitement',
                'chemin' => 'app_gestion_contrat_juridique_manager_consultation_attribution'
            ],
            'demande_traitees' => [
                'title' => 'Consulter les demandes traitées',
                'chemin' => 'app_gestion_contrat_juridique_manager_consultation_attribution'
            ],
        ];

        if($statutDemande == 'demande_traitees'){
            $contrats = array_merge(
                $this->contratRepository->getContratParStatut('demande_rejetee'),
                $this->contratRepository->getContratParStatut('demande_validee')
            );
        }else{
            $contrats = $this->contratRepository->getContratParStatut($statutDemande);
        }


        return $this->render('gestion_contrat/juridique/index.html.twig', [
            'demandes' => $contrats,
            'lib' => $lib[$statutDemande]['title'] ?? "",
            'chemin' => $lib[$statutDemande]['chemin'] ?? "",
        ]);
    }

    /**
     * Attribution demande
     * @IsGranted("ROLE_USER_BOSS_JURIDIQUE")
     * @Route("/manager/attribution/{id}", name="app_gestion_contrat_juridique_manager_attribution", methods={"GET", "POST"})
     */
    public function manager_attribution(Request $request, Contrat $contrat, EntityManagerInterface $manager, WorkflowInterface $demandeContratStateMachine): Response
    {
        if($demandeContratStateMachine->can($contrat, 'attribuer')){
            $form = $this->createForm(AttributionType::class, $contrat);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Contrat $contratReq **/
                $contratReq = $form->getData();
                $this->attribuerDemandeAgentService->call($contratReq);
                $this->addFlash(
                    'success',
                    "
                    La demande a bien été attribuée à l'agent 
                    {$contrat->getUserJuridique()->getUser()->concatenateNames()}.
                    "
                );
                return $this->redirectToRoute('app_gestion_contrat_juridique', [], Response::HTTP_SEE_OTHER);
            }


            return $this->render('gestion_contrat/juridique/manager/non_attribue.html.twig', array_merge(
                [
                    'form' => $form->createView(),
                    'contrat' => $contrat
                ]
            ));
        }
        $this->addFlash(
            'danger',
            "Erreur interne !"
        );
        return $this->redirectToRoute('app_gestion_contrat_juridique');
    }

    /**
     * @IsGranted("ROLE_USER_BOSS_JURIDIQUE")
     * @Route("/manager/consulter-attribuer/{id}", name="app_gestion_contrat_juridique_manager_consultation_attribution", methods={"GET"})
     */
    public function consulter_attributation(Request $request, Contrat $contrat){
        $form = $this->createForm(ConsultationAttribueeType::class, $contrat);
        return $this->render('gestion_contrat/juridique/manager/en_cours.html.twig', array_merge(
            [
                'form' => $form->createView(),
                'contrat' => $contrat
            ]
        ));
    }

    /* ------------------------------------------------------------------------------------------------------------------------------ */

    /**
     * @Route("/agent-juridique/{statut}", name="app_gestion_contrat_juridique_home_agent", methods={"GET"})
     */
    public function consulerDemandes(GetMesDemandes $getMesDemandesService, string $statut = ""){
        $contrats = $getMesDemandesService->call($statut);

        return $this->render('gestion_contrat/juridique/index_agent.html.twig', [
            'demandes' => $contrats,
            'lib' => "Traitement des demandes attribuées",
            'chemin' => "app_gestion_contrat_juridique_traitement_agent",
        ]);
    }

    /**
     * @Route("/agent-juridique/traitement/{id}", name="app_gestion_contrat_juridique_traitement_agent", methods={"GET", "POST"})
     */
    public function traitementDemandes(Request $request, Contrat $contrat, TraiterDemandeContrat $traiterDemandeContratService){
        //Si l'utilisateur n'est pas celui affecté à la demande retour en arrière
        /* @var User $user */
        $user = $this->getUser();
        if($contrat->getUserJuridique()->getUser() != $user){
            return $this->redirectToRoute("app_gestion_contrat_juridique_home_agent");
        }
        //Si la demande n'est pas attribuee, retour !
        else if($contrat->getCurrentState() !== "demande_attribuee"){
            return $this->redirectToRoute("app_gestion_contrat_juridique_afficher_agent", [
                'id' => $contrat->getId()
            ]);
        }

        $form = $this->createForm(TraitementType::class, $contrat);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Contrat $contratReq **/
            $contratReq = $form->getData();

            $res = $traiterDemandeContratService->call($contratReq, $form->get('resultTraitement')->getData()) ?? false;
            $this->addFlash(
                $res ? "success" : "danger",
                $res ? "L'action a été correctement réalisée. La demande de contrat a été {strtolower( $form->get('resultTraitement')->getData() ).'e'}" : "Erreur !"
            );
            return $this->redirectToRoute("app_gestion_contrat_juridique_home_agent");
        }

        return $this->render('gestion_contrat/juridique/traitement_agent.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/agent-juridique/afficher/{id}", name="app_gestion_contrat_juridique_afficher_agent", methods={"GET"})
     */
    public function afficherDemande(Contrat $contrat){
        //Si l'utilisateur n'est pas celui affecté à la demande retour en arrière
        /* @var User $user */
        $user = $this->getUser();
        if($contrat->getUserJuridique()->getUser() != $user){
            return $this->redirectToRoute("app_gestion_contrat_juridique_home_agent");
        }

        $form = $this->createForm(AfficherType::class, $contrat);
        //Variable servant à connaitre l'état de validation pour affichage conditionnel
        $result = $contrat->getCurrentState() === "demande_validee";
        return $this->render('gestion_contrat/juridique/afficher_agent.html.twig', [
            'result' => $result,
            'contrat' => $contrat,
            'form' => $form->createView()
        ]);
    }

}
