<?php

namespace App\Form\Obligation;

use App\Entity\Obligation\Obligation;
use App\Repository\Obligation\ImportanceObligationRepository;
use App\Repository\Obligation\ReferenceListeRepository;
use App\Repository\Obligation\SourceListeRepository;
use App\Repository\Obligation\StatutObligationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObligationType extends AbstractType
{
    /**
     * @var SourceListeRepository
     */
    private $sourceListeRepository;
    /**
     * @var ReferenceListeRepository
     */
    private $referenceListeRepository;
    /**
     * @var StatutObligationRepository
     */
    private $statutObligationRepository;
    /**
     * @var ImportanceObligationRepository
     */
    private $importanceObligationRepository;

    public function __construct(
        SourceListeRepository $sourceListeRepository,
        ReferenceListeRepository $referenceListeRepository,
        StatutObligationRepository $statutObligationRepository,
        ImportanceObligationRepository $importanceObligationRepository
    )
    {
        $this->sourceListeRepository = $sourceListeRepository;
        $this->referenceListeRepository = $referenceListeRepository;
        $this->statutObligationRepository = $statutObligationRepository;
        $this->importanceObligationRepository = $importanceObligationRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $opt = ['dispoDeroulante' => true];
        $sources = $this->sourceListeRepository->findBy($opt);
        $refs = $this->referenceListeRepository->findBy($opt);
        $statuts = $this->statutObligationRepository->findAll();
        $obl = $this->importanceObligationRepository->findBy($opt);

        $builder
            ->add('sourceList', ChoiceType::class, [
                'label' => 'Type de la source',
                'empty_data' => '',
                'choices' => $sources,
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sourceDisposition', TextareaType::class, [
                'label' => 'Libellé de la source',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('referenceListe', ChoiceType::class, [
                'label' => 'Type de la référence',
                'empty_data' => '',
                'choices' => $refs,
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('reference', TextareaType::class, [
                'label' => 'Libellé de la référence',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('pointsAbordes', TextareaType::class, [
                'label' => 'Points abordés',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('obligation', TextareaType::class, [
                'label' => 'Obligation',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut de l\'obligation',
                'empty_data' => '',
                'choices' => $statuts,
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sanctions', TextareaType::class, [
                'label' => 'Sanctions',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('prevues', TextareaType::class, [
                'label' => 'Sanctions prévues',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('impact', TextareaType::class, [
                'label' => 'Impact des sanctions',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('importanceObligation', ChoiceType::class, [
                'label' => 'Importance de l\'obligation',
                'empty_data' => '',
                'choices' => $obl,
                'choice_label'=> function($choice, $key, $value){
                    return $choice->getLib();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Joindre le fichier',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                /*'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/msword',
                            'application/x-pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],*/
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Obligation::class,
        ]);
    }
}
