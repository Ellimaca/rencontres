<?php

namespace App\DataFixtures;

use App\Entity\Critere;
use App\Entity\Profil;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //crée notre faker pour générer de belles données aléatoires en français !
        $faker = \Faker\Factory::create("fr_FR");

        for($i = 0; $i < 100; $i++) {

            //on instancie nos 3 objets
            $profil = new Profil();
            $critere = new Critere();
            $user = new User();

            //On hydrate les propriétés du user
            $user->setEmail($faker->email);
            $user->setPassword('123456');
            $user->setPseudo($faker->userName);
            $user->setProfil($profil);

            //on sauvegarde en bdd notre user
            $manager->persist($user);

            //On hydrate les propriétés des critères
            $critere->setAgeRecherches($faker->numberBetween(18,100));
            $critere->setDepartementsRecherches($faker->numberBetween(00000,99999));
            $critere->setSexesRecherches($faker->randomElement(['femme', 'homme', 'non-binaire']));

            //on sauvegarde en bdd notre critère
            $manager->persist($critere);

            //On hydrate les propriétés du profil
            $profil->setCodePostal($faker->numberBetween(00001, 99999));
            $profil->setCoeur($faker->boolean(10));
            $profil->setDateNaissance($faker->dateTimeBetween('-100 years', '-8 years'));
            $profil->setSexe($faker->randomElement(['femme', 'homme', 'non-binaire']));
            $profil->setVille($faker->city);
            $profil->setUser($user);
            $profil->setCriteres($critere);

            //on sauvegarde en bdd notre profil
            $manager->persist($profil);
        }

        $manager->flush();
    }
}
