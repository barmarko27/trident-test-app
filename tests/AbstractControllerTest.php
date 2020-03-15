<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 15/03/2020
 * Time: 02:48
 */

namespace App\Tests;

use PHPUnit\Framework\Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    protected $token;

    protected function setUp()
    {
        $this->client = static::createClient();

        $this->client->request(
            'POST',
            '/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'admin@trident.test.local',
                'password' => 'trident_test_password'
            ])
        );

        $response = json_decode($this->client->getResponse()->getContent());

        if(is_object($response) && property_exists($response, 'token') && is_string($response->token)) {

            $this->token = $response->token;

        } else {

            throw new Exception("This was not expected.");
        }
    }
}