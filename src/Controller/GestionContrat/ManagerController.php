<?php

namespace App\Controller\GestionContrat;

use App\Entity\Contrat\Contrat;
use App\Entity\User;
use App\Form\Contrat\Manager\ContratManagerViewType;
use App\Repository\Contrat\ContratRepository;
use App\Service\Contrat\Manager\ApproveContrat;
use App\Service\Contrat\Manager\DenyContrat;
use App\Service\Contrat\Manager\ListingDemandes;
use App\Service\Contrat\Manager\ViewContrat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/contrats/manager")
 * @IsGranted("ROLE_USER_MANAGER")
 */
class ManagerController extends AbstractController
{
    /**
     * @var ListingDemandes
     */
    private $listingDemandesService;
    /**
     * @var ApproveContrat
     */
    private $approveContratService;
    /**
     * @var DenyContrat
     */
    private $denyContratService;
    /**
     * @var ContratRepository
     */
    private $contratRepository;
    /**
     * @var ViewContrat
     */
    private $viewContratService;

    public function __construct(ContratRepository $contratRepository, ListingDemandes $listingDemandesService, ApproveContrat $approveContratService, DenyContrat $denyContratService, ViewContrat $viewContratService)
    {
        $this->listingDemandesService = $listingDemandesService;
        $this->approveContratService = $approveContratService;
        $this->denyContratService = $denyContratService;
        $this->contratRepository = $contratRepository;
        $this->viewContratService = $viewContratService;
    }

    //Listing des demandes du service
    /**
     * @Route("/{etat}", name="app_gestion_contrat_manager_index", methods={"GET"})
     */
    public function index($etat = ''){
        $demandes = $this->listingDemandesService->call($etat);

        $additionnalCases = [];

        switch ($etat){
            case "demande_acceptee_manager":
                $additionnalCases = array_merge(
                    $this->listingDemandesService->call("demande_non_attribuee"),
                    $this->listingDemandesService->call("demande_non_attribuee"),
                    $this->listingDemandesService->call("demande_non_attribuee")
                );
                break;
        }

        $demandes = array_merge(
            $demandes,
            $etat == 'demande_acceptee_manager' ? $this->listingDemandesService->call('demande_non_attribuee') : []);
        $nombreTotalDemandeEnAttente = 0;
        $nombreTotalDemandesContratDepartement = 0;

        $user = $this->getUser();
        if($user instanceof User){
            $nombreTotalDemandesContratDepartement = $this->contratRepository->countContratPerDepartement(
                $user->getDepartement()
            )['total'];

            $nombreTotalDemandeEnAttente = $this->contratRepository->countContratPerDepartement(
                $user->getDepartement(), 'en_attente_manager'
            )['total'];

            //Eviter la division par zéro
            $nombreTotalDemandesContratDepartement = empty($nombreTotalDemandesContratDepartement) ? 1 : $nombreTotalDemandesContratDepartement;
        }

        return $this->render('gestion_contrat/manager/home.html.twig', [
            'demandes' => $demandes,
            "nombreTotalDemandeEnAttente" => $nombreTotalDemandeEnAttente,
            "nombreTotalDemandesContratDepartement" => $nombreTotalDemandesContratDepartement,
            'tauxDemandes' => ($nombreTotalDemandeEnAttente * 100) / $nombreTotalDemandesContratDepartement
        ]);
    }

    //Consultation d'une demande
    /**
     * @Route("/view/{id}", name="app_gestion_contrat_manager_view", methods={"GET"})
     */
    public function view(Contrat $contrat){

        $form = $this->createForm(ContratManagerViewType::class, $contrat);
        $options = $this->viewContratService->call($contrat);

        return $this->render('gestion_contrat/manager/view_contrat.html.twig',
            array_merge(
                [
                    'form' => $form->createView(),
                    'contrat' => $contrat
                ], $options
            )

        );
    }

    //Validation d'une demande
    /**
     * @Route("/action/approve/{id}", name="app_gestion_contrat_manager_approve", methods={"GET"})
     */
    public function approve(Contrat $contrat){

        $this->approveContratService->call($contrat);

        $this->addFlash(
            'success',
            "La demande de contrat N° <i><b>{$contrat->getId()}</b></i> a été approuvée, elle a été transmise au service juridique."
        );
        return $this->redirectToRoute('app_gestion_contrat_manager_index');
    }

    //Rejet d'une demande
    /**
     * @Route("/action/deny/{id}", name="app_gestion_contrat_manager_deny", methods={"GET"})
     */
    public function deny(Contrat $contrat){

        $this->denyContratService->call($contrat);

        $this->addFlash(
            'info',
            "La demande de contrat N° <i><b>{$contrat->getId()}</b></i> a été refusée, elle a été retransmise à l'agent porteur."
        );
        return $this->redirectToRoute('app_gestion_contrat_manager_index');
    }
}