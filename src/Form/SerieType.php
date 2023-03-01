<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // TextType::class = de type texte
            ->add('name', TextType::class)
            // de base, si rien de précisé, required = true
            ->add('overview', TextareaType::class, [
                'required'=>false,
                // ajouter class="raw" dans l'html
                'attr'=>['class'=>'raw']
            ])
            // select
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Canceled' =>'canceled',
                    'Ended'=>'ended',
                    'Returning'=>'returning',
                ],
                // valeurs par défaut
                'multiple' => false,
                'expanded' => false
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres', ChoiceType::class, [
                'choices' => [
                    'Horror'=>'horror',
                    'Western' =>'western',
                    'Comedy'=>'comedy',
                    'Drama'=>'drama'
                ]
            ])
            ->add('firstAirDate', DateType::class, [
                'label'=>'First air date : ',
                // formattage de l'affichage
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('lastAirDate', DateType::class, [
                'label'=>'Last air date : ',
                // formattage de l'affichage
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('backdrop')
            // upload de l'image
            ->add('poster', FileType::class, [
                // ne met pas l'adresse temporaire de l'image dans serie
                'mapped'=> false,
                // contraintes sur l'upload
                'constraints' => [
                    new Image([
                        // 5 megaOctets = 5M
                        'maxSize' => '5000k',
                        // mimesTypes = extensions des fichiers (par défaut image/*)
                        'mimeTypesMessage' => 'Image format not allowed !'
                    ])
                ]
            ])
            ->add('tmdbId');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class
            // ,
            // le temps de faire les tests sur les formulaires
            // à retirer après tests
            // 'required' => false
        ]);
    }
}
