<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategorieControllerTest extends WebTestCase
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
            'categorie_voir' => [
                '/vip/',
                'Very Importante Personne',
                '.heading-intro',
            ],
        ];
    }
}
