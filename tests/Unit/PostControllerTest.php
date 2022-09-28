<?php

namespace App\Tests\Unit;

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
    protected Post $post;

    public function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->post = new Post();
    }

    /** @test */
    public function a_post_record_can_be_created_in_the_database(): void
    {
        $this->databaseTool->loadFixtures([
            AuthorFixtures::class
        ]);

        /** @var Author $author */
        $author = $this->entityManager->getRepository(Author::class)->find(1);

        $this->post->setTitle('Title text')
            ->setDescription('Description text')
            ->setContent('Content text')
            ->setCreated()
            ->setAuthor($author);

        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
        $this->entityManager->clear();
        self::assertEquals('Title text', $this->post->getTitle());
        self::assertEquals('Description text', $this->post->getDescription());
        self::assertEquals('Content text', $this->post->getContent());
        self::assertEquals($this->post->getAuthor(), $author);
        self::assertInstanceOf(Post::class, $this->post);
        self::assertInstanceOf(\DateTime::class, $this->post->getCreated());
        self::assertIsObject($this->post);
    }

    /** @test */
    public function a_type_error_is_thrown_when_trying_to_add_a_non_string_to_the_post()
    {
        try {
            $this->post->setTitle([]);
            $this->fail('A TypeError should have been thrown');
        } catch (\TypeError $error) {
            $this->assertStringStartsWith('App\Entity\Post::setTitle():', $error->getMessage());
        }
    }
}
