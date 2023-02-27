<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    // Point d'entrée de fixtures
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->addSeries($manager, $faker);
    }

    public function addSeries(ObjectManager $manager, Generator $generator)
    {
        for ($i=0; $i < 50; $i++) {
            $serie = new Serie();

            $serie
                // pas besoin de concaténation grâce aux doubles cotes " qui interprètent
                // ->setName("Serie $i")

                // renvoyer 3 mots de Lorem au hasard
                // + implode car renvoie un tableau
                ->setName(implode(" ", $generator->words(3)))
                ->setVote($generator->numberBetween(0, 10))
                ->setStatus($generator->randomElement(['ended', 'returning', 'cancelled']))
                ->setPoster("poster.png")
                ->setTmdbId(123)
                ->setPopularity(250)
                ->setFirstAirDate($generator->dateTimeBetween('-6 month'))
                ->setLastAirDate($generator->dateTimeBetween($serie->getFirstAirDate()))
                ->setGenres($generator->randomElement(['Western', 'Horror', 'Drama']))
                ->setBackdrop("backdrop.png");
            $manager->persist($serie);
        }
        $manager->flush();
    }
}
