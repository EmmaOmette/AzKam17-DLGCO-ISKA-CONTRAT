<?php

namespace App\Controller\GestionAvisConseils;

use App\Entity\AvisConseils\Avis;
use App\Entity\AvisConseils\DocAvisConseils;
use App\Entity\Contrat\DocumentContrat;
use App\Entity\User;
use App\Form\AvisConseils\AvisType;
use App\Form\AvisConseils\AvisViewType;
use App\Repository\AvisConseils\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/gestion/avis/conseils/avis")
 */
class AvisController extends AbstractController
{
    /**
     * @Route("/", name="app_gestion_avis_conseils_avis_index", methods={"GET"})
     */
    public function index(AvisRepository $avisRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $totalAvis = ($this->isGranted('ROLE_JURIDIQUE')) ? $avisRepository->pending() : $avisRepository->myRequests($user);
        return $this->render('gestion_avis_conseils/avis/index.html.twig', [
            'avis' => $totalAvis,
            'nbrAvis' => count($totalAvis)
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_avis_conseils_avis_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AvisRepository $avisRepository, EntityManagerInterface $manager, ParameterBagInterface $parameterBag): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis
                ->setCurrentState("waiting_for_attribution")
                ->setUserFrom( $this->getUser() )
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
                $docDB = (new DocAvisConseils())
                    ->setOriginalName($doc->getClientOriginalName())
                    ->setPath($fichier)
                    ->setAvis($avis);

                $manager->persist($docDB);

                $avis->addDocAvisConseil($docDB);
            }

            $avisRepository->add($avis);
            $manager->flush();

            $this->addFlash(
                'success',
                "La demande d'avis a été correctement enregistrée."
            );

            return $this->redirectToRoute('app_gestion_avis_conseils_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_avis_conseils/avis/new.html.twig', [
            'avi' => $avis,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_avis_conseils_avis_show", methods={"GET", "POST"})
     */
    public function show(Avis $avis, Request $request, AvisRepository $avisRepository): Response
    {
        $form = $this->createForm(AvisViewType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setCurrentState("treated");
            dump($avis);
            $avisRepository->add($avis);
            dump($avis);
            $this->addFlash(
                'success',
                "Demande d'avis traitée"
            );
            return $this->redirectToRoute('app_gestion_avis_conseils_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestion_avis_conseils/avis/show.html.twig', [
            'avis' => $avis,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_gestion_avis_conseils_avis_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->add($avi);
            return $this->redirectToRoute('app_gestion_avis_conseils_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_avis_conseils/avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_avis_conseils_avis_delete", methods={"POST"})
     */
    public function delete(Request $request, Avis $avi, AvisRepository $avisRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
            $avisRepository->remove($avi);
        }

        return $this->redirectToRoute('app_gestion_avis_conseils_avis_index', [], Response::HTTP_SEE_OTHER);
    }
}
