<?php
namespace App\Tests\Application;

use App\DataFixtures\PostFixtures;
use App\Entity\Post;
use App\Tests\Unit\DatabasePrimer;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser as KernelBrowser;

class WebTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;
    protected KernelBrowser $client;
    protected Post $post;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = KernelTestCase::bootKernel();
        DatabasePrimer::prime($kernel);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->post = new Post();
    }

    /** @test */
    public function home_page_is_successfully_loaded(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Home page');
    }

    /** @test */
    public function posts_page_is_successfully_loaded(): void
    {
        $this->databaseTool->loadFixtures([PostFixtures::class]);
        $this->client->request('GET', '/posts');
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function posts_page_is_redirected_to_create_new_post_page_if_has_no_post(): void
    {
        $this->client->request('GET', '/posts');
        $this->assertResponseRedirects('/posts/create');
    }

    /** @test */
    public function form_on_the_create_new_post_page_is_working_properly(): void
    {
        $crawler = $this->client->request('GET', '/posts/create');
        $form = $crawler->selectButton('Submit post')->form();
        $form->setValues([
            'post_form[title]' => 'test',
            'post_form[description]' => 'test',
            'post_form[content]' => ''
        ]);
        $crawler = $this->client->submit($form);
        $this->assertStringContainsString( 'This value should not be blank.', $crawler->outerHtml());

        $form->setValues([
            'post_form[title]' => '',
            'post_form[description]' => 'test',
            'post_form[content]' => 'test'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertStringContainsString( 'This value should not be blank.', $crawler->outerHtml());

        $form->setValues([
            'post_form[title]' => 'test',
            'post_form[description]' => '',
            'post_form[content]' => 'test'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertStringContainsString( 'This value should not be blank.', $crawler->outerHtml());


        $form->setValues([
            'post_form[title]' => 'test',
            'post_form[description]' => 'test',
            'post_form[content]' => 'test'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertStringNotContainsString( 'This value should not be blank.', $crawler->outerHtml());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function show_post_page_is_successfully_loaded(): void
    {
        $this->databaseTool->loadFixtures([PostFixtures::class]);
        $post = $this->entityManager->getRepository(Post::class)->find(1);

        $this->client->request('GET', '/posts/'.$post->getId());
        $this->assertResponseIsSuccessful();
    }

    /** @test */
    public function show_post_page_is_throwing_exception_if_post_does_not_exist(): void
    {
        $this->client->request('GET', '/posts/1');
        $this->assertResponseStatusCodeSame(404);
    }

    /** @test */
    public function form_on_the_create_new_comment_page_is_working_properly(): void
    {
        $this->databaseTool->loadFixtures([PostFixtures::class]);
        $crawler = $this->client->request('GET', '/comment/create');

        $form = $crawler->selectButton('Submit comment')->form();
        $form->setValues(['comment_form[content]' => '']);

        $crawler = $this->client->submit($form);
        $this->assertStringContainsString( 'This value should not be blank.', $crawler->outerHtml());

        $form->setValues(['comment_form[content]' => 'test']);

        $crawler = $this->client->submit($form);
        $this->assertStringNotContainsString( 'This value should not be blank.', $crawler->outerHtml());
        $this->assertResponseIsSuccessful();
    }
}
