<?php

namespace App\Controller;

use App\Service\ApiEntityManager\WishlistService;
use App\Service\ResponseManager\RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class WishlistController extends AbstractController
{
    private $wishlistService;

    private $restResponseManager;

    public function __construct(WishlistService $wishlistService, RestResponse $restResponse)
    {
        $this->wishlistService = $wishlistService;

        $this->restResponseManager = $restResponse;
    }

    /**
     * Create new Wishlist.
     * @SWG\Response(
     *     response=201,
     *     description="Create new Wishlist.",
     * )
     * @SWG\Tag(name="wishlist")
     * @Route("/api/wishlist", name="create-new-wish-list", methods={"POST"})
     */
    public function create(): JsonResponse
    {
        $wishlistEntity = $this->wishlistService->create();

        $this->restResponseManager->setDataBody($wishlistEntity, Response::HTTP_CREATED);

        if(count($errors = $this->wishlistService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get all Wishlists.
     * @SWG\Response(
     *     response=200,
     *     description="Get all Wishlists.",
     * )
     * @SWG\Tag(name="wishlist")
     * @Route("/api/wishlist", name="get-all-wish-lists", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $wishLists = $this->wishlistService->getAll();

        $this->restResponseManager->setDataBody($wishLists, Response::HTTP_OK);

        if(count($errors = $this->wishlistService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get a Wishlist.
     * @SWG\Response(
     *     response=200,
     *     description="Get a Wishlist.",
     * )
     * @SWG\Tag(name="wishlist")
     * @Route("/api/wishlist/{id}", name="get-wish-list", methods={"GET"})
     */
    public function getOne(int $id): JsonResponse
    {
        $wishlist = $this->wishlistService->get($id);

        $this->restResponseManager->setDataBody($wishlist, Response::HTTP_OK);

        if(count($errors = $this->wishlistService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Update a Wishlist.
     * @SWG\Response(
     *     response=202,
     *     description="Update a Wishlist.",
     * )
     * @SWG\Tag(name="wishlist")
     * @Route("/api/wishlist/{id}", name="update-wish-list", methods={"PUT"})
     */
    public function update(int $id): JsonResponse
    {
        $wishlist = $this->wishlistService->update($id);

        $this->restResponseManager->setDataBody($wishlist, Response::HTTP_ACCEPTED);

        if(count($errors = $this->wishlistService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Delete a Wishlist.
     * @SWG\Response(
     *     response=204,
     *     description="Delete a Wishlist.",
     * )
     * @SWG\Tag(name="wishlist")
     * @Route("/api/wishlist/{id}", name="delete-wish-list", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $this->wishlistService->delete($id);

        $this->restResponseManager->setDataBody([], Response::HTTP_NO_CONTENT);

        if(count($errors = $this->wishlistService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }
}