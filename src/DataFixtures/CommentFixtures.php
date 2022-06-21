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
        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->realText(20,1))
                ->setDate($faker->date('now'))
                ->setAuthor(
                    $this->getReference('author_reference_'.$i)
                )
                ->setPost(
                    $this->getReference('post_reference_'.$i)
                );
            $this->getReference('post_reference_'.$i);
            $this->getReference('author_reference_'.$i);
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
