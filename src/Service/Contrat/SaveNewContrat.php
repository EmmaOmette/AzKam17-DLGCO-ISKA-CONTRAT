<?php

namespace App\Service\Contrat;

use App\Entity\Contrat\Contrat;
use App\Entity\Contrat\DocumentContrat;
use App\Entity\User;
use App\Repository\Contrat\ContratRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;

class SaveNewContrat
{
    /**
     * @var ContratRepository
     */
    private $contratRepository;
    /**
     * @var ParameterBagInterface
     */
    private $params;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var WorkflowInterface
     */
    private $demandeContratStateMachine;

    public function __construct(WorkflowInterface $demandeContratStateMachine, Security $security, ContratRepository $contratRepository, EntityManagerInterface $manager, ParameterBagInterface $params)
    {
        $this->contratRepository = $contratRepository;
        $this->params = $params;
        $this->manager = $manager;
        $this->security = $security;
        $this->demandeContratStateMachine = $demandeContratStateMachine;
    }

    public function call(Contrat $contrat, $documents){

        /*
         * Enregistrement du contrat
         * Définition des props vides
        */
        $contrat
            ->setDureeContrat("")
            ->setDateDerniereEvaluation(
                Carbon::now()
            )
            ->setObjetConditionModification(
                $contrat->getObjetConditionModification() ?? " "
            )
            ->setClausesParticulieres(
                $contrat->getClausesParticulieres() ?? " "
            )
        ;

        //Enregistrement des fichiers
        foreach ($documents as $doc){
            if($doc instanceof UploadedFile){
                $fichier = md5(uniqid()).'.'.$doc->guessExtension();

                //Copie du fichier dans le dossier uploads
                $doc->move(
                    $this->params->get('contrat_doc_directory'),
                    $fichier
                );


                //Création du fichier dans la base de données
                $docDB = (new DocumentContrat())
                    ->setOriginalName($doc->getClientOriginalName())
                    ->setPath($fichier)
                    ->setContrat($contrat);

                $this->manager->persist($docDB);

                $contrat->addDocumentContrat($docDB);
            }
        }


        $contrat->setCurrentState('en_attente_manager');

        //Workflow
        if($this->security->isGranted('ROLE_JURIDIQUE')){
            //L'utilisateur fait partie du service juridique la demande de contrat est automatiquement validée
            $this->demandeContratStateMachine->apply($contrat, 'valider_demande_user_juridique');
        }elseif ($this->security->isGranted('ROLE_USER_MANAGER')){
            //L'utilisateur est manager, elle est en attente d'attibution
            $this->demandeContratStateMachine->apply($contrat, 'valider_manager');
        }

        $user = $this->security->getUser();
        if($user instanceof User){
            $contrat->setDepartementInitiateur(
                $user->getDepartement()
            );

            $contrat->setAgentInitiateur(
                $user
            );
        }

        $this->contratRepository->add($contrat);
        $this->manager->flush();

        return true;
    }

}