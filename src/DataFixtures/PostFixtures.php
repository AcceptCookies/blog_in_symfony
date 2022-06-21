<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->setTitle($faker->realText(10, 1))
                ->setContent($faker->realText(20,1))
                ->setDate($faker->date('now'))
                ->setAuthor(
                    $this->getReference('author_reference_'.$i)
                );
            $this->getReference('author_reference_'.$i);
            $manager->persist($post);
            $this->addReference('post_reference_'.$i, $post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AuthorFixtures::class,
        ];
    }
}
