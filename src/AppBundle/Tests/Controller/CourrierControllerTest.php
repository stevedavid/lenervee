<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourrierControllerTest extends WebTestCase
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
            'courrier_index' => [
                '/',
                'consommatrice acrimonieuse qui envoie ses lettres de réclamation aux services consommateurs',
                '.heading-intro',
            ],
            'courrier_voir' => [
                '/deceptions/biscotte-a-tartiner/',
                'Cracotte de LU',
                '.bw-current .post-title'
            ],
            'courrier_rechercher' => [
                '/blog/courriers/rechercher/?s=Lipton',
                'courrier trouvé',
                '.page-title',
            ],
        ];
    }
}
