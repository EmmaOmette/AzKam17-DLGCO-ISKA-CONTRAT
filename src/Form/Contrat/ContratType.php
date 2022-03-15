<?php

namespace App\Form\Contrat;

use App\Entity\Contrat\Contrat;
use App\Repository\Contrat\ModeFacturationRepository;
use App\Repository\Contrat\ModeReglementRepository;
use App\Repository\Contrat\ModeRenouvellementRepository;
use App\Repository\Contrat\TypeDemandeContratRepository;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ContratType extends AbstractType
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

    public function __construct(Security $security, ModeRenouvellementRepository $modeRenouvellementRepository, ModeReglementRepository $modeReglementRepository, ModeFacturationRepository $modeFacturationRepository, DepartementRepository $departementRepository, TypeDemandeContratRepository $typeDemandeContratRepository)
    {
        $this->typeDemandeContratRepository = $typeDemandeContratRepository;
        $this->departementRepository = $departementRepository;
        $this->modeFacturationRepository = $modeFacturationRepository;
        $this->modeReglementRepository = $modeReglementRepository;
        $this->modeRenouvellementRepository = $modeRenouvellementRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mois = [];
        for($i = 0; $i < 12; $i++){
            $mois[] = ($i + 1).' mois';
        }

        $builder
            ->add('objet', TextType::class, [
                'label' => 'Objet du contrat',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('identiteConcontractantTemp', TextType::class, [
                'label' => 'Identité du co-contractant',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('clausesParticulieres', TextareaType::class, [
                'label' => 'Clauses particulières',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control',
                    'rows' => 10,
                    'style' => "resize: none;"
                ]
            ])
            ->add('delaiDenonciation', ChoiceType::class, [
                'label' => 'Délai de dénonciation',
                'empty_data' => '',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('periodicitePaiement', ChoiceType::class, [
                'label' => 'Périodicité de paiement',
                'empty_data' => '',
                'choices' => $mois,
                'choice_label'=> function($choice, $key, $value){
                    return $value;
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('typeDemande', ChoiceType::class, [
                'label' => "Type de demande",
                'empty_data' => '',
                'choices' => $this->typeDemandeContratRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeFacturation', ChoiceType::class, [
                'label' => "Mode de facturation",
                'empty_data' => '',
                'choices' => $this->modeFacturationRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeReglement', ChoiceType::class, [
                'label' => "Mode de paiement",
                'empty_data' => '',
                'choices' => $this->modeReglementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modeRenouvellement', ChoiceType::class, [
                'label' => "Mode de renouvellement",
                'empty_data' => '',
                'choices' => $this->modeRenouvellementRepository->findAll(),
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('departementInitiateur', ChoiceType::class, [
                'label' => "Département initiateur",
                'empty_data' => '',
                'choices' => [$this->security->getUser()->getDepartement()],
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control',
                    'disabled' => true
                ]
            ])
            ->add('entreeVigueurAt', DateType::class, [
                'label' => "Date d'entrée en vigueur",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                ]
            ])
            ->add('finContratAt', DateType::class, [
                'label' => "Date de fin du contrat",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control datetimepicker-input',
                ]
            ])
            ->add('objetConditionModification', TextType::class, [
                'label' => "Objet des conditions de modifications",
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false
                ]
            ])
            ->add('libConditionModification', TextareaType::class, [
                'label' => "Détails des conditions de modifications",
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false
                ]
            ]);
        if($options["show_submit"] ?? false){
            $builder->add('submit', SubmitType::class, [
                'label' => "Modifier",
                'attr' => [
                    'class' => 'btn btn-block btn-primary btn-lg'
                ]
            ])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
            'show_submit' => true
        ]);
    }
}
