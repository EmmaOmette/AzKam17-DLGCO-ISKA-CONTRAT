<?php

namespace App\Controller\GestionBaseDocumentaire;

use App\Entity\Contrat\TypeDocContrat;
use App\Form\Contrat\TypeDocContratType;
use App\Repository\Contrat\TypeDocContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @IsGranted("ROLE_JURIDIQUE")
 * @Route("/base-documentaire/type-document-contrat")
 */
class TypeDocContratController extends AbstractController
{
    /**
     * @Route("/", name="app_gestion_base_documentaire_type_doc_contrat_index", methods={"GET", "POST"})
     */
    public function index(
        Request $request,
        TypeDocContratRepository $typeDocContratRepository,
        SluggerInterface $slugger,
        EntityManagerInterface $manager
    ): Response
    {
        $typeDocContrat = new TypeDocContrat();
        $form = $this->createForm(TypeDocContratType::class, $typeDocContrat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Fichier dans le formulaire
            /** @var UploadedFile $modeleContratFile */
            $fichier = $form->get('fichier')->getData();

            $label = $form->get('label')->getData();

            if($fichier){
                $orginalFileName = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($orginalFileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fichier->guessExtension();

                try{
                    $fichier->move(
                        $this->getParameter('type_contrat_doc_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    echo $e->getMessage();
                }

                $typeDocContrat
                    ->setOriginalName($label)
                    ->setPath(
                        '/uploads/documents/type_contrat/'.$newFilename
                    );

                $this->addFlash(
                    'success',
                    'Le type de contrat a bien enregistré.'
                );
                $manager->persist($typeDocContrat);
                $manager->flush();
            }
        }

        return $this->render('gestion_base_documentaire/type_doc_contrat/home.html.twig', [
            'form' => $form->createView(),
            'modeles' => $typeDocContratRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="app_gestion_base_documentaire_type_doc_contrat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TypeDocContratRepository $typeDocContratRepository): Response
    {
        $typeDocContrat = new TypeDocContrat();
        $form = $this->createForm(TypeDocContratType::class, $typeDocContrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeDocContratRepository->add($typeDocContrat);
            return $this->redirectToRoute('app_gestion_base_documentaire_type_doc_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_base_documentaire/type_doc_contrat/new.html.twig', [
            'type_doc_contrat' => $typeDocContrat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_base_documentaire_type_doc_contrat_show", methods={"GET"})
     */
    public function show(TypeDocContrat $typeDocContrat): Response
    {
        return $this->render('gestion_base_documentaire/type_doc_contrat/show.html.twig', [
            'type_doc_contrat' => $typeDocContrat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_gestion_base_documentaire_type_doc_contrat_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TypeDocContrat $typeDocContrat, TypeDocContratRepository $typeDocContratRepository): Response
    {
        $form = $this->createForm(TypeDocContratType::class, $typeDocContrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeDocContratRepository->add($typeDocContrat);
            return $this->redirectToRoute('app_gestion_base_documentaire_type_doc_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gestion_base_documentaire/type_doc_contrat/edit.html.twig', [
            'type_doc_contrat' => $typeDocContrat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_gestion_base_documentaire_type_doc_contrat_delete", methods={"POST"})
     */
    public function delete(Request $request, TypeDocContrat $typeDocContrat, TypeDocContratRepository $typeDocContratRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeDocContrat->getId(), $request->request->get('_token'))) {
            $typeDocContratRepository->remove($typeDocContrat);
        }

        return $this->redirectToRoute('app_gestion_base_documentaire_type_doc_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
}
