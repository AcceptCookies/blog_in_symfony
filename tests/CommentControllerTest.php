<?php

namespace App\Tests;

use App\DataFixtures\AuthorFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentControllerTest extends KernelTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;
    protected Comment $comment;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->comment = new Comment();
    }

    /** @test */
    public function a_comment_record_can_be_created_in_the_database()
    {
        $this->databaseTool->loadFixtures([
            AuthorFixtures::class,
            PostFixtures::class
        ]);

        /** @var Author $author */
        $author = $this->entityManager->getRepository(Author::class)->find(1);
        $post = $this->entityManager->getRepository(Post::class)->find(1);

        $this->comment->setContent('Content text')
            ->setCreated()
            ->setPost($post)
            ->setAuthor($author);

        $this->entityManager->persist($this->comment);
        $this->entityManager->flush();

        $this->entityManager->clear();

        self::assertEquals('Content text', $this->comment->getContent());
        self::assertEquals($this->comment->getAuthor(), $author);
        self::assertInstanceOf(Comment::class, $this->comment);
        self::assertInstanceOf(\DateTime::class, $this->comment->getCreated());
        self::assertIsObject($this->comment);
    }

    /** @test */
    public function a_type_error_is_thrown_when_trying_to_add_a_non_string_to_the_comment()
    {
        try {
            $this->comment->setContent([]);
            $this->fail('A TypeError should have been thrown');
        } catch (\TypeError $error) {
            $this->assertStringStartsWith('App\Entity\Comment::setContent():', $error->getMessage());
        }
    }
}