<?php

namespace App\Form\Contrat\Juridique\Manager;

use App\Entity\Contrat\Contrat;
use App\Entity\User;
use App\Repository\Contrat\ModeFacturationRepository;
use App\Repository\Contrat\ModeReglementRepository;
use App\Repository\Contrat\ModeRenouvellementRepository;
use App\Repository\Contrat\TypeDemandeContratRepository;
use App\Repository\DepartementRepository;
use App\Repository\UserJuridiqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AttributionType extends AbstractType
{

    /**
     * @var TypeDemandeContratRepository
     */
    private $typeDemandeContratRepository;
    /**
     * @var DepartementRepository
     */
    private $departementRepository;
    /**
     * @var ModeFacturationRepository
     */
    private $modeFacturationRepository;
    /**
     * @var ModeReglementRepository
     */
    private $modeReglementRepository;
    /**
     * @var ModeRenouvellementRepository
     */
    private $modeRenouvellementRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;

    public function __construct(Security $security,UserJuridiqueRepository $userJuridiqueRepository, ModeRenouvellementRepository $modeRenouvellementRepository, ModeReglementRepository $modeReglementRepository, ModeFacturationRepository $modeFacturationRepository, DepartementRepository $departementRepository, TypeDemandeContratRepository $typeDemandeContratRepository)
    {
        $this->typeDemandeContratRepository = $typeDemandeContratRepository;
        $this->departementRepository = $departementRepository;
        $this->modeFacturationRepository = $modeFacturationRepository;
        $this->modeReglementRepository = $modeReglementRepository;
        $this->modeRenouvellementRepository = $modeRenouvellementRepository;
        $this->security = $security;
        $this->userJuridiqueRepository = $userJuridiqueRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mois = [];
        for($i = 0; $i < 12; $i++){
            $mois[] = ($i + 1).' mois';
        }

        $builder
            ->add('porteurContrat', TextType::class, [
                'label' => 'Porteur du contrat',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'disabled' => true
                ]
            ])
            ->add('objet', TextType::class, [
                'label' => 'Objet du contrat',
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('identiteConcontractantTemp', TextType::class, [
                'label' => 'Identité du co-contractant',
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            /*->add('clausesParticulieres', TextareaType::class, [
                'label' => 'Clauses particulières',
                'required' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control desactive',
                    'rows' => 10,
                    'style' => "resize: none;",
                    //'disabled' => true
                ]
            ])*/
            /*->add('dureeContrat', ChoiceType::class, [
                'label' => 'Durée du contrat',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])*/
            ->add('delaiDenonciation', ChoiceType::class, [
                'label' => 'Délai de dénonciation',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('periodicitePaiement', ChoiceType::class, [
                'label' => 'Périodicité de paiement',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('typeDemande', ChoiceType::class, [
                'label' => "Type de demande",
                'choices' => $this->typeDemandeContratRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('modeFacturation', ChoiceType::class, [
                'label' => "Mode de facturation",
                'choices' => $this->modeFacturationRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('modeReglement', ChoiceType::class, [
                'label' => "Mode de paiement",
                'choices' => $this->modeReglementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('modeRenouvellement', ChoiceType::class, [
                'label' => "Mode de renouvellement",
                'choices' => $this->modeRenouvellementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('departementInitiateur', ChoiceType::class, [
                'label' => "Département initiateur",
                'choices' => [$this->security->getUser()->getDepartement()],
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control desactive',
                    //'disabled' => true
                ]
            ])
            ->add('entreeVigueurAt', DateType::class, [
                'label' => "Date d'entrée en vigueur",
                'attr' => [
                    'class' => 'form-control datetimepicker-input desactive',
                    //'disabled' => true
                ]
            ])
            ->add('finContratAt', DateType::class, [
                'label' => "Date de fin du contrat",
                'attr' => [
                    'class' => 'form-control datetimepicker-input desactive',
                    //'disabled' => true
                ]
            ])
            /*->add('objetConditionModification', TextType::class, [
                'label' => "Objet des conditions de modifications",
                'required' => false,
                'attr' => [
                    'class' => 'form-control desactive',
                    'required' => false,
                    //'disabled' => true
                ]
            ])*/
            ->add('libConditionModification', TextareaType::class, [
                'label' => "Détails des conditions de modifications",
                'required' => false,
                'attr' => [
                    'class' => 'form-control desactive',
                    'required' => false,
                    //'disabled' => true
                ]
            ])

            ->add('userJuridique', ChoiceType::class, [
                'label' => "Attribuer la demande à l'agent",
                //'mapped' => false,
                'choices' => $this->userJuridiqueRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    /** @var User $user **/
                    $user = $choice->getUser();
                    return $user->getFirstName() . ' ' . $user->getLastName() . ' - ' . $choice->getTempsTraitement() . ' jours';
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Attribuer à l'agent",
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
