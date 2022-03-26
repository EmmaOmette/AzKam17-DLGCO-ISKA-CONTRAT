<?php

namespace App\Controller\GestionAutorisation;

use App\Entity\AutorisationOffre\Demande;
use App\Entity\AutorisationOffre\DocDemande;
use App\Entity\User;
use App\Form\AutorisationOffre\DemandeType;
use App\Form\AutorisationOffre\DemandeViewType;
use App\Repository\AutorisationOffre\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apps/gestion/autorisation/demande")
 */
class DemandeController extends AbstractController
{
    /**
     * @Route("/", name="app_gestion_autorisation_demande_index", methods={"GET"})
     */
    public function index(DemandeRepository $demandeRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $totalDemandes= ($this->isGranted('ROLE_JURIDIQUE')) ? $demandeRepository->pending() : $demandeRepository->myRequests($user);

        return $this->render('gestion_autorisation/demande/index.html.twig', [
            'demandes' => $totalDemandes,
            'nbrDemandes' => count($totalDemandes)
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_autorisation_demande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DemandeRepository $demandeRepository, EntityManagerInterface $manager): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $demande
                ->setCurrentState("waiting_for_attribution")
                ->setCreatedBy($user)
            ;
            $docs = $form->get('documents')->getData();
            foreach ($docs as $doc){
                $fichier = md5(uniqid()).'.'.$doc->guessExtension();
                //Copie du fichier dans le dossier uploads
                $doc->move(
                    $this->getParameter('contrat_doc_directory'),
                    $fichier
                );


                //Création du fichier dans la base de données
                $docDB = (new DocDemande())
                    ->setOriginalName($doc->getClientOriginalName())
                    ->setPath($fichier)
                    ->setDemande($demande);

                $manager->persist($docDB);

                $demande->addDocument($docDB);
            }

            $demandeRepository->add($demande);
            $manager->flush();

            $this->addFlash(
                'success',
                "La demande d'autorisation a été correctement enregistrée."
            );
            return $this->redirectToRoute('app_gestion_autorisation_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_autorisation/demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_autorisation_demande_show", methods={"GET", "POST"})
     */
    public function show(Demande $demande, Request $request, DemandeRepository $demandeRepository): Response
    {
        $form = $this->createForm(DemandeViewType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setCurrentState("treated");
            $demandeRepository->add($demande);
            $this->addFlash(
                'success',
                "Demande d'autorisation traitée"
            );
            return $this->redirectToRoute('app_gestion_autorisation_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestion_autorisation/demande/show.html.twig', [
            'demande' => $demande,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_gestion_autorisation_demande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Demande $demande, DemandeRepository $demandeRepository): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandeRepository->add($demande);
            return $this->redirectToRoute('app_gestion_autorisation_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_autorisation/demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_autorisation_demande_delete", methods={"POST"})
     */
    public function delete(Request $request, Demande $demande, DemandeRepository $demandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $demandeRepository->remove($demande);
        }

        return $this->redirectToRoute('app_gestion_autorisation_demande_index', [], Response::HTTP_SEE_OTHER);
    }
}
