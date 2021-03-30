<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //crée notre faker pour générer de belles données aléatoires en français !
        $faker = \Faker\Factory::create("fr_FR");

        $profil = new Profil();

        $manager->flush();
    }
}
