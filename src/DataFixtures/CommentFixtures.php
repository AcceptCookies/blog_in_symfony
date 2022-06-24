<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->realText(100,1))
                ->setCreated()
                ->setAuthor(
                    $this->getReference('author_reference_'.$faker->numberBetween(0,3))
                )
                ->setPost(
                    $this->getReference('post_reference_'.$faker->numberBetween(0,9))
                );
//            $this->getReference('post_reference_'.$i);
//            $this->getReference('author_reference_'.$i);
            $manager->persist($comment);
            $this->addReference('comment_reference_'.$i, $comment);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            PostFixtures::class,
            AuthorFixtures::class,
        ];
    }
}
