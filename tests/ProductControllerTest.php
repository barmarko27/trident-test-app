<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends AbstractControllerTest
{
    /**
     * @return mixed
     */
    public function testCreateProduct()
    {
        $this->client->request(
            'POST',
            '/api/product',
            [
                'name' => 'Test Product Name',
                'description' => 'Test Description very very long',
                'ean' => rand(1000000000000, 9999999999999),
                'thumbnail' => 'https://lorempixel.com/300/300/?'.rand(90000, 100000),
                'price' => rand(1.0, 999.99)
            ],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Product not contains ID property');

        $this->assertIsInt($response->id, 'Product ID is not an integer');

        $this->assertGreaterThan(0, $response->id, 'Product ID is not greater than zero');

        $this->assertObjectHasAttribute('name', $response, 'Product not contains name property');

        $this->assertSame('Test Product Name', $response->name, 'Product name is not equal of that provided');

        return $response->id;
    }

    /**
     * @depends testCreateProduct
     * @return int|null
     */
    public function testGetAllProducts(): ?int
    {
        $this->client->request(
            'GET',
            '/api/product',
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

        $this->assertIsArray($response, 'Products - get all is not an array');

        $this->assertGreaterThan(0, count($response),  'Product results is not greater than zero');

        $this->assertObjectHasAttribute('id', $response[0], 'Product not contains ID property');

        $this->assertIsInt($response[0]->id, 'Product ID is not an integer');

        $this->assertGreaterThan(0, $response[0]->id, 'Product ID is not greater than zero');

        return $response[0]->id; // Returns this ID for next tests
    }

    /**
     * @depends testGetAllProducts
     */
    public function testGetOne($id)
    {
        $this->client->request(
            'GET',
            '/api/product/'. $id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $response, 'Product not contains ID property');

        $this->assertIsInt($response->id, 'Product ID is not an integer');

        $this->assertSame($id, $response->id, 'Product ID is not equal of that provided');
    }

    /**
     * @depends testCreateProduct
     */
    public function testUpdateProduct($id)
    {
        $this->client->request(
            'PUT',
            '/api/product/'. $id,
            [
                'name' => 'New Product Name'
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

        $this->assertSame('New Product Name', $response->name, 'Wishlist name is not equal of that provided');

        return $id;
    }

    /**
     * @depends testUpdateProduct
     */
    public function testDeleteProduct($id)
    {
        $this->client->request(
            'DELETE',
            '/api/product/'. $id,
            [],
            [],
            ["HTTP_Authorization" => "Bearer $this->token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $response = json_decode($this->client->getResponse()->getContent());

        $this->assertSame($response, [], 'Error during deleting product');
    }
}
