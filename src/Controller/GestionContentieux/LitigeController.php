<?php

namespace App\Controller\GestionContentieux;

use App\Entity\GestionContentieux\Litige;
use App\Entity\GestionContentieux\Procedure;
use App\Form\GestionContentieux\LitigeType;
use App\Repository\GestionContentieux\LitigeRepository;
use App\Repository\GestionContentieux\ProcedureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/gestion/contentieux/litige")
 */
class LitigeController extends AbstractController
{
    /**
     * @Route("/", name="app_gestion_contentieux_litige_index", methods={"GET"})
     */
    public function index(LitigeRepository $litigeRepository): Response
    {
        $allLitiges = $litigeRepository->findAll();
        return $this->render('gestion_contentieux/litige/index.html.twig', [
            'litiges' => $allLitiges,
            'nbrDemandeEnCoursDeValidation' => count($allLitiges)
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_contentieux_litige_new", methods={"GET", "POST"})
     */
    public function new(Request $request, LitigeRepository $litigeRepository, ProcedureRepository $procedureRepository): Response
    {
        $litige = new Litige();
        $form = $this->createForm(LitigeType::class, $litige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $procedure = (new Procedure())
                ->setValue($form->get('procedure')->getData() ?? "Début du litige"
                );

            $procedureRepository->add($procedure);

            $litige->addProcedure($procedure);

            $litigeRepository->add($litige);
            $this->addFlash(
                "success",
                "Litige enregistré avec succès. Son identifiant est {$litige->getId()}."
            );
            return $this->redirectToRoute('app_gestion_contentieux_litige_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_contentieux/litige/new.html.twig', [
            'litige' => $litige,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_contentieux_litige_show", methods={"GET"})
     */
    public function show(Litige $litige): Response
    {
        $form = $this->createForm(LitigeType::class, $litige);

        return $this->render('gestion_contentieux/litige/show.html.twig', [
            'litige' => $litige,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_gestion_contentieux_litige_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Litige $litige, LitigeRepository $litigeRepository, ProcedureRepository $procedureRepository): Response
    {
        $form = $this->createForm(LitigeType::class, $litige);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $procedure = (new Procedure())
                ->setValue($form->get('procedure')->getData());

            $procedureRepository->add($procedure);

            $litige->addProcedure($procedure);

            $litigeRepository->add($litige);
            $this->addFlash(
                "success",
                "Litige {$litige->getId()} modifié avec succès."
            );
            return $this->redirectToRoute('app_gestion_contentieux_litige_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('gestion_contentieux/litige/edit.html.twig', [
            'litige' => $litige,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_contentieux_litige_delete", methods={"POST"})
     */
    public function delete(Request $request, Litige $litige, LitigeRepository $litigeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$litige->getId(), $request->request->get('_token'))) {
            $litigeRepository->remove($litige);
        }

        return $this->redirectToRoute('app_gestion_contentieux_litige_index', [], Response::HTTP_SEE_OTHER);
    }
}
