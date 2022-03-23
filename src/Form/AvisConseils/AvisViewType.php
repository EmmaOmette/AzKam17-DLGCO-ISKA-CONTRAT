<?php

namespace App\Form\AvisConseils;

use App\Entity\AvisConseils\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class AvisViewType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', TextType::class, [
                'label' => 'Objet',
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('rensignement', TextareaType::class, [
                'label' => 'Renseignement',
                'empty_data' => '',
                'required' => false,
                'disabled' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control',
                    'rows' => 10,
                    'style' => "resize: none;"
                ]
            ])
            ->add('niveauExecution', TextType::class, [
                'label' => 'Niveau d\'exécution',
                'required' => false,
                'disabled' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('response', TextareaType::class, [
                'label' => 'Réponse',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'id' => 'summernote',
                    'class' => 'form-control',
                    'rows' => 10,
                    'style' => "resize: none;"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}