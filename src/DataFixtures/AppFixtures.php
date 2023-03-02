<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // créer un constructeur
    // lignes 19 peut être remplacer par private devant param du construct
    // = public function __construct(private EntityManagerInterface $entityManager){...
    private EntityManagerInterface $entityManager;
    private Generator $faker;
    public function __construct(
        // mettre que des services en param
        // php bin/console debug:autowiring nomDuneVar pour savoir si c'est un service
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }


    // Point d'entrée par défaut des fixtures
    public function load(ObjectManager $manager): void
    {
        // remplacer par le constructeur
//        $faker = Factory::create('fr_FR');
//        $this->addSeries($manager, $faker);

        //$this->addSeries();
        $this->addUsers(20);
    }

    public function addSeries(ObjectManager $manager, Generator $faker)
    {
        for ($i = 0; $i < 50; $i++) {
            $serie = new Serie();

            // generator remplacé par $this->faker
            $serie
                ->setName(implode(" ", $this->faker->words(3)))
                ->setVote($this->faker->numberBetween(0, 10))
                ->setStatus($this->faker->randomElement(['ended', 'returning', 'cancelled']))
                ->setPoster("poster.png")
                ->setTmdbId(123)
                ->setPopularity(250)
                ->setFirstAirDate($this->faker->dateTimeBetween('-6 month'))
                ->setLastAirDate($this->faker->dateTimeBetween($serie->getFirstAirDate()))
                ->setGenres($this->faker->randomElement(['Western', 'Horror', 'Drama']))
                ->setBackdrop("backdrop.png");
            $manager->persist($serie);
        }
        $manager->flush();
    }

    // avant création du constructeur pour créer un service
//            $serie
//                // pas besoin de concaténation grâce aux doubles cotes " qui interprètent
//                // ->setName("Serie $i")
//
//                // renvoyer 3 mots de Lorem au hasard
//                // + implode car renvoie un tableau
//                ->setName(implode(" ", $generator->words(3)))
//                ->setVote($generator->numberBetween(0, 10))
//                ->setStatus($generator->randomElement(['ended', 'returning', 'cancelled']))
//                ->setPoster("poster.png")
//                ->setTmdbId(123)
//                ->setPopularity(250)
//                ->setFirstAirDate($generator->dateTimeBetween('-6 month'))
//                ->setLastAirDate($generator->dateTimeBetween($serie->getFirstAirDate()))
//                ->setGenres($generator->randomElement(['Western', 'Horror', 'Drama']))
//                ->setBackdrop("backdrop.png");
//            $manager->persist($serie);
//        }
//        $manager->flush();
//    }
    private function addUsers(int $number)
    {
        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->email)
                ->setFirstname($this->faker->firstName)
                ->setLastname($this->faker->lastName)
                ->setPassword('123');
            // utilisation du service pour encoder le password
            $password = $this->passwordHasher->hashPassword($user, '123');
            $user->setPassword($password);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }
}
