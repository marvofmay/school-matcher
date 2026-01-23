<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Presentation\API\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SchoolMatcherControllerFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function testSchoolMatchEndpoint(): void
    {

        $this->client->request('GET', '/api/school/match', [
            'name' => 'I Liceum',
            'city' => 'Warszawa',
        ]);

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('matches', $data);
        $this->assertNotEmpty($data['matches']);
        $this->assertEquals(
            'I Liceum Ogólnokształcące im. Adama Mickiewicza',
            $data['matches'][0]['school']
        );
    }
}
