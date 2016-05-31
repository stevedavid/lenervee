<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagControllerTest extends WebTestCase
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
            'tag_voir' => [
                '/blog/tag/minouchette/',
                'minouchette',
                '.page-title',
            ],
        ];
    }
}
