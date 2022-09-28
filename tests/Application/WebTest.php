<?php
namespace App\Tests\Application;

use App\Entity\Post;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebTest extends WebTestCase
{
    protected Post $post;

    /** @test */
    public function home_page_is_successfully_loaded(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Home page');
    }
}
