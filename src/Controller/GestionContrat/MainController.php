<?php

namespace App\Controller\GestionContrat;

use App\Entity\Contrat\Contrat;
use App\Entity\User;
use App\Form\Contrat\ContratType;
use App\Form\Contrat\Create\ContratNewType;
use App\Repository\Contrat\ContratRepository;
use App\Repository\UserJuridiqueRepository;
use App\Service\Contrat\SaveNewContrat;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/contrats")
 */
class MainController extends AbstractController
{
    /**
     * @var SaveNewContrat
     */
    private $saveNewContratService;

    public function __construct(SaveNewContrat $saveNewContratService)
    {
        $this->saveNewContratService = $saveNewContratService;
    }

    /**
     * @Route("/", name="app_gestion_contrat_main_index", methods={"GET"})
     */
    public function index(ContratRepository $contratRepository, UserJuridiqueRepository $userJuridiqueRepository): Response
    {
        /** @var User $user**/
        $user = $this->getUser();

        $userJuridique = $userJuridiqueRepository->findOneBy([
            'user' => $user
        ]);


        return $this->render('gestion_contrat/main/index.html.twig', [
            'nbrDemandeEnCoursDeValidation' => $this->isGranted("ROLE_JURIDIQUE") ? $contratRepository->nbrEnAttenteValidation($userJuridique) : 0,
            'nbrDemandeTraites' => $this->isGranted("ROLE_JURIDIQUE") ? ($contratRepository->nbrDemandesTraites($userJuridique) ?? 0) : 0,
            'contrats' => $this->isGranted("ROLE_JURIDIQUE") ? $contratRepository->findAll() : $contratRepository->findMyContrats($user),
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_contrat_main_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ContratRepository $contratRepository): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratNewType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contrat = $form->getData();
            if($contrat instanceof Contrat){
                /** @var User $user**/
                $user = $this->getUser();
                $contrat->setAgentInitiateur($user);
                $this->saveNewContratService->call(
                    $contrat, $form->get('documentContrats')->getData()
                );
                return $this->redirectToRoute('app_gestion_contrat_main_index', []);
            }
        }

        return $this->render('gestion_contrat/main/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ], (new Response(null, 200)));
    }

    /**
     * @Route("/view/{id}", name="app_gestion_contrat_main_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        /* @var User $user */
        $user = $this->getUser();

        if($contrat->getAgentInitiateur() !== $user && !$this->isGranted('ROLE_JURIDIQUE')){
            $this->addFlash(
                'danger',
                'Accès non autorisé !'
            );
            return $this->redirectToRoute("app_gestion_contrat_main_index");
        }

        $form = $this->createForm(ContratType::class, $contrat,  [
            'show_submit' => $contrat->getCurrentState() == "demande_non_attribuee" || $contrat->getCurrentState() == "en_attente_manager"
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contratRepository->add(
                $contrat
            );
            $this->addFlash(
                'success',
                'La modification a bien été effectuée.'
            );
            return $this->redirectToRoute('app_gestion_contrat_main_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_contrat/main/view.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_gestion_contrat_main_delete", methods={"POST"})
     */
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contrat->getId(), $request->request->get('_token'))) {
            $contratRepository->remove($contrat);
        }

        return $this->redirectToRoute('app_gestion_contrat_main_index', [], Response::HTTP_SEE_OTHER);
    }
}
