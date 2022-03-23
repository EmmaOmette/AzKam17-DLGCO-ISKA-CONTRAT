<?php

namespace App\Controller\GestionAvisConseils;

use App\Entity\AvisConseils\Avis;
use App\Form\AvisConseils\AvisType;
use App\Repository\AvisConseils\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $totalAvis = $avisRepository->findAll();
        return $this->render('gestion_avis_conseils/avis/index.html.twig', [
            'avis' => $totalAvis,
            'nbrAvis' => count($totalAvis)
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_avis_conseils_avis_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AvisRepository $avisRepository): Response
    {
        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->add($avi);
            return $this->redirectToRoute('app_gestion_avis_conseils_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_avis_conseils/avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_avis_conseils_avis_show", methods={"GET"})
     */
    public function show(Avis $avi): Response
    {
        return $this->render('gestion_avis_conseils/avis/show.html.twig', [
            'avi' => $avi,
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
