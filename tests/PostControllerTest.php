<?php

namespace App\Tests;

use App\DataFixtures\AuthorFixtures;
use App\Entity\Author;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostControllerTest extends KernelTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function a_post_record_can_be_created_in_the_database(): void
    {
        $this->databaseTool->loadFixtures([
            AuthorFixtures::class
        ]);

        /** @var Author $author */
        $author = $this->entityManager->getRepository(Author::class)->find(1);

        $post = new Post();
        $post->setTitle('Title text')
            ->setDescription('Description text')
            ->setContent('Content text')
            ->setCreated()
            ->setAuthor($author);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->entityManager->clear();

        self::assertEquals('Title text', $post->getTitle());
        self::assertEquals('Description text', $post->getDescription());
        self::assertEquals('Content text', $post->getContent());
    }
}
