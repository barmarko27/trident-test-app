<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class WishlistItemControllerTest
 * @package App\Tests
 */
class WishlistItemControllerTest extends AbstractControllerTest
{
    private $wishlistID;

    private $productID;

    protected function setUp()
    {
        parent::setUp();

        $this->wishlistID = $this->getRandomWishlist();

        $this->productID = $this->getRandomProduct();
    }

    /**
     * @return mixed
     */
    public function testCreateWishlistItem()
    {
        $desiredPrice = rand(1.0, 999.00);

        $quantity = rand(1, 100);

        $this->client->request(
            'POST',
            '/api/wishlist/'.$this->wishlistID.'/items',
            [
                'product' => ['id' => $this->productID],
                'desired_price' => $desiredPrice,
                'quantity' => $quantity
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist item not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist item ID is not an integer');

        $this->assertGreaterThan(0, $response->id, 'Wishlist item ID is not greater than zero');

        $this->assertObjectHasAttribute('quantity', $response, 'Wishlist item not contains quantity property');

        $this->assertSame($quantity, $response->quantity, 'Wishlist item quantity is not equal of that provided');

        return $response->id;
    }

    /**
     * @depends testCreateWishlistItem
     * @return int|null
     */
    public function testGetAllWishlistItem(): ?int
    {
        $this->client->request(
            'GET',
            '/api/wishlist/'.$this->wishlistID.'/items',
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

        $this->assertIsArray($response, 'Wishlist items - get all is not an array');

        $this->assertGreaterThan(0, count($response),  'Wishlist item results is not greater than zero');

        $this->assertObjectHasAttribute('id', $response[0], 'Wishlist itemnot contains ID property');

        $this->assertIsInt($response[0]->id, 'Wishlist item ID is not an integer');

        $this->assertGreaterThan(0, $response[0]->id, 'Wishlist item ID is not greater than zero');

        return $response[0]->id; // Returns this ID for next tests
    }

    /**
     * @depends testGetAllWishlistItem
     */
    public function testGetOne($id)
    {
        $this->client->request(
            'GET',
            '/api/wishlist/'. $this->wishlistID . '/items/'.$id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist item not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist item ID is not an integer');

        $this->assertSame($id, $response->id, 'Wishlist item ID is not equal of that provided');
    }

    /**
     * @depends testCreateWishlistItem
     */
    public function testUpdateWishlistItem($id)
    {
        $quantity = rand(1, 100);

        $this->client->request(
            'PUT',
            '/api/wishlist/'. $this->wishlistID . '/items/' . $id,
            [
                'quantity' => $quantity
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Wishlist item not contains ID property');

        $this->assertIsInt($response->id, 'Wishlist item ID is not an integer');

        $this->assertSame($id, $response->id, 'Wishlist item ID is not equal of that provided');

        $this->assertObjectHasAttribute('quantity', $response, 'Wishlist item not contains quantity property');

        $this->assertSame($quantity, $response->quantity, 'Wishlist item name is not equal of that provided');

        return $id;
    }

    /**
     * @depends testUpdateWishlistItem
     */
    public function testDeleteWishlistItem($id)
    {
        $this->client->request(
            'DELETE',
            '/api/wishlist/'. $this->wishlistID. '/items/' . $id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertSame($response, [], 'Error during deleting wishlist item');
    }

    /**
     * @return int|null
     */
    private function getRandomWishlist(): ?int
    {
        $this->client->request(
            'GET',
            '/api/wishlist',
            [
                'page' => 0,
                'per_page' => 1,
                'order' => '-creation_date'
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );


        $response = json_decode($this->client->getResponse()->getContent());

        return $response[0]->id; // Returns this ID for next tests
    }

    /**
     * @return int|null
     */
    private function getRandomProduct(): ?int
    {
        $this->client->request(
            'GET',
            '/api/product',
            [
                'page' => 0,
                'per_page' => 1,
                'order' => '-creation_date'
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );


        $response = json_decode($this->client->getResponse()->getContent());

        return $response[0]->id; // Returns this ID for next tests
    }
}
