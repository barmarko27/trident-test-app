<?php

namespace App\Controller;

use App\Service\ApiEntityManager\ProductService;
use App\Service\ResponseManager\RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class ProductController extends AbstractController
{
    private $productService;

    private $restResponseManager;

    public function __construct(ProductService $productService, RestResponse $restResponse)
    {
        $this->productService = $productService;

        $this->restResponseManager = $restResponse;
    }

    /**
     * Create new Product.
     * @SWG\Response(
     *     response=201,
     *     description="Create new Product.",
     * )
     * @SWG\Tag(name="product")
     * @Route("/api/product", name="create-new-product", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        $productEntity = $this->productService->create();

        $this->restResponseManager->setDataBody($productEntity, Response::HTTP_CREATED);

        if(count($errors = $this->productService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get all Products.
     * @SWG\Response(
     *     response=200,
     *     description="Get all Product.",
     * )
     * @SWG\Tag(name="product")
     * @Route("/api/product", name="get-all-products", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $products = $this->productService->getAll();

        $this->restResponseManager->setDataBody($products, Response::HTTP_OK);

        if(count($errors = $this->productService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get a product.
     * @SWG\Response(
     *     response=200,
     *     description="Get a Product.",
     * )
     * @SWG\Tag(name="product")
     * @Route("/api/product/{id}", name="get-product", methods={"GET"})
     */
    public function getOne(int $id): JsonResponse
    {
        $product = $this->productService->get($id);

        $this->restResponseManager->setDataBody($product, Response::HTTP_OK);

        if(count($errors = $this->productService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Update a product.
     * @SWG\Response(
     *     response=202,
     *     description="Update a Product.",
     * )
     * @SWG\Tag(name="product")
     * @Route("/api/product/{id}", name="update-product", methods={"PUT"})
     */
    public function update(int $id): JsonResponse
    {
        $product = $this->productService->update($id);

        $this->restResponseManager->setDataBody($product, Response::HTTP_ACCEPTED);

        if(count($errors = $this->productService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Delete a product.
     * @SWG\Response(
     *     response=204,
     *     description="Delete a Product.",
     * )
     * @SWG\Tag(name="product")
     * @Route("/api/product/{id}", name="delete-product", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $this->productService->delete($id);

        $this->restResponseManager->setDataBody([], Response::HTTP_NO_CONTENT);

        if(count($errors = $this->productService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }
}