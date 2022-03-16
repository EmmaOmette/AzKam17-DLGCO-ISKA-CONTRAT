<?php

namespace App\Controller\GestionObligation;

use App\Entity\Obligation\Document;
use App\Entity\Obligation\Obligation;
use App\Entity\User;
use App\Form\Obligation\ObligationType;
use App\Repository\Obligation\ObligationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/apps/gestion/obligation")
 */
class ObligationController extends AbstractController
{
    /**
     * @Route("/", name="app_gestion_obligation_obligation_index", methods={"GET"})
     */
    public function index(ObligationRepository $obligationRepository): Response
    {
        $allObligations = $obligationRepository->findAll();
        return $this->render('gestion_obligation/obligation/index.html.twig', [
            'obligations' => $allObligations,
            'nombreTotal' => count($allObligations),
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_obligation_obligation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ObligationRepository $obligationRepository,
                        SluggerInterface $slugger,
                        EntityManagerInterface $manager): Response
    {
        $obligation = new Obligation();
        $form = $this->createForm(ObligationType::class, $obligation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Fichier dans le formulaire
            /** @var UploadedFile $modeleContratFile */
            $fichier = $form->get('fichier')->getData();

            /** @var User $user */
            $user = $this->getUser();

            if ($fichier) {
                $orginalFileName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($orginalFileName);
                $newFilename = uniqid() . '.' . $fichier->guessExtension();

                try {
                    $fichier->move(
                        $this->getParameter('type_contrat_doc_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e->getMessage();
                }

                $doc = (new Document())
                    ->setOriginalName($newFilename)
                    ->setPath(
                        '/uploads/documents/type_contrat/' . $newFilename
                    );

                $this->addFlash(
                    'success',
                    'Le type de contrat a bien enregistré.'
                );
                $manager->persist($doc);
                $manager->flush();

                $obligation->setDocument(
                    $doc
                )->setReponsable($user);

                $obligationRepository->add($obligation);
                return $this->redirectToRoute('app_gestion_obligation_obligation_index', [], Response::HTTP_SEE_OTHER);

            }
        }

        return $this->renderForm('gestion_obligation/obligation/new.html.twig', [
            'obligation' => $obligation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_obligation_obligation_show", methods={"GET"})
     */
    public function show(Obligation $obligation): Response
    {
        return $this->render('gestion_obligation/obligation/show.html.twig', [
            'obligation' => $obligation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_gestion_obligation_obligation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Obligation $obligation, ObligationRepository $obligationRepository): Response
    {
        $form = $this->createForm(ObligationType::class, $obligation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $obligationRepository->add($obligation);
            return $this->redirectToRoute('app_gestion_obligation_obligation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_obligation/obligation/edit.html.twig', [
            'obligation' => $obligation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_obligation_obligation_delete", methods={"POST"})
     */
    public function delete(Request $request, Obligation $obligation, ObligationRepository $obligationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$obligation->getId(), $request->request->get('_token'))) {
            $obligationRepository->remove($obligation);
        }

        return $this->redirectToRoute('app_gestion_obligation_obligation_index', [], Response::HTTP_SEE_OTHER);
    }
}
