<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 4; $i++) {
            $author = new Author();
            $author->setEmail($faker->email)
                   ->setName($faker->userName);
            $manager->persist($author);
            $this->addReference('author_reference_'.$i, $author);
        }

        $manager->flush();
    }
}
