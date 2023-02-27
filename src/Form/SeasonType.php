<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'First air Date : '
            ])
            ->add('overview')
            ->add('poster')
            ->add('tmdbId')

            // rajouter manuellement plus tard
            // ->add('dateCreated')
            // ->add('dateModified')

            // classe de type Serie (on ne crée pas une instance, on donne un type)
            ->add('serie', EntityType::class, [
                'class' => Serie::class,
                // attribut pour affichage des séries à l'écran (ici, selon le nom)
                'choice_label' => 'name',
                'label' => 'Associated serie',
                // trier les séries par ordre alphabétique
                'query_builder'=> function (SerieRepository $serieRepository) {
                    $qb = $serieRepository->createQueryBuilder('s');
                    $qb->addOrderBy('s.name');
                    return $qb;
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
