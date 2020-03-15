<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

class WishlistControllerTest extends AbstractControllerTest
{
    public function testCreateWishlist()
    {
        $this->client->request(
            'POST',
            '/api/wishlist',
            [
                'name' => 'Name of wishlist',
                'description' => 'Long long description for test wishlist'
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist ID is not an integer');

        $this->assertGreaterThan(0, $response->id, 'Wishlist ID is not greater than zero');

        $this->assertObjectHasAttribute('name', $response, 'Wishlist not contains name property');

        $this->assertSame('Name of wishlist', $response->name, 'Wishlist name is not equal of that provided');

        return $response->id;
    }

    /**
     * @depends testCreateWishlist
     * @return int|null
     */
    public function testGetAllWishlist(): ?int
    {
        $this->client->request(
            'GET',
            '/api/wishlist',
            [
                'page' => 0,
                'per_page' => 10,
                'order' => '-creation_date'
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertIsArray($response, 'Wishlists - get all is not an array');

        $this->assertGreaterThan(0, count($response),  'Wishlist results is not greater than zero');

        $this->assertObjectHasAttribute('id', $response[0], 'Wishlist not contains ID property');

        $this->assertIsInt($response[0]->id, 'Wishlist ID is not an integer');

        $this->assertGreaterThan(0, $response[0]->id, 'Wishlist ID is not greater than zero');

        return $response[0]->id; // Returns this ID for next tests
    }

    /**
     * @depends testGetAllWishlist
     */
    public function testGetOne($id)
    {
        $this->client->request(
            'GET',
            '/api/wishlist/'. $id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist ID is not an integer');

        $this->assertSame($id, $response->id, 'Wishlist ID is not equal of that provided');
    }

    /**
     * @depends testCreateWishlist
     */
    public function testUpdateWishlist($id)
    {
        $this->client->request(
            'PUT',
            '/api/wishlist/'. $id,
            [
                'name' => 'New Wishlist Name'
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist ID is not an integer');

        $this->assertSame($id, $response->id, 'Wishlist ID is not equal of that provided');

        $this->assertObjectHasAttribute('name', $response, 'Wishlist not contains name property');

        $this->assertSame('New Wishlist Name', $response->name, 'Wishlist name is not equal of that provided');

        return $id;
    }

    /**
     * @depends testUpdateWishlist
     */
    public function testDeleteWishlist($id)
    {
        $this->client->request(
            'DELETE',
            '/api/wishlist/'. $id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertSame($response, [], 'Error during deleting wishlist');
    }
}