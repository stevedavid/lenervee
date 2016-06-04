<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testIndex($path, $text, $selector)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $path);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains($text, $crawler->filter($selector)->text());
    }

    public function urlProvider()
    {
        return [
            'blog_cookie_accept' => [
                '/cookies/',
                'Cookies',
                'h2.main-title',
            ],
            'blog_contact' => [
                '/contactez-nous/',
                'Contact',
                'h2.main-title',
            ],
            'blog_about' => [
                '/a-propos/',
                'A propos',
                'h2.main-title',
            ],
            'blog_mentions' => [
                '/mentions-legales/',
                'Mentions légales',
                'h2.main-title',
            ],
            'blog_feed' => [
                '/feed/',
                'Derniers courriers',
                'description',
            ],
        ];
    }
}
