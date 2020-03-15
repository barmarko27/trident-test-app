<?php

namespace App\Controller;

use App\Service\ApiEntityManager\WishlistItemService;
use App\Service\ResponseManager\RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class WishlistItemController extends AbstractController
{
    private $wishListItemService;

    private $restResponseManager;

    public function __construct(WishlistItemService $wishListItemService, RestResponse $restResponse)
    {
        $this->wishListItemService = $wishListItemService;

        $this->restResponseManager = $restResponse;
    }

    /**
     * Create a Wishlist Item.
     * @SWG\Response(
     *     response=201,
     *     description="Create a Wishlist Item.",
     * )
     * @SWG\Tag(name="wishlist item")
     * @Route("/api/wishlist/{id}/items", name="create-new-wish-list-items", methods={"POST"})
     */
    public function create(int $id): JsonResponse
    {
        $wishListItemEntity = $this->wishListItemService->create(['wishlist' => ['id' => $id]]);

        $this->restResponseManager->setDataBody($wishListItemEntity, Response::HTTP_CREATED);

        if(count($errors = $this->wishListItemService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get all Wishlist Items.
     * @SWG\Response(
     *     response=200,
     *     description="Get all Wishlist Items",
     * )
     * @SWG\Tag(name="wishlist item")
     * @Route("/api/wishlist/{id}/items", name="get-all-wish-list-items-belong-wishlist", methods={"GET"})
     */
    public function getAll(int $id): JsonResponse
    {
        $wishListItems = $this->wishListItemService->getAll($id);

        $this->restResponseManager->setDataBody($wishListItems, Response::HTTP_OK);

        if(count($errors = $this->wishListItemService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Get a Wishlist Items.
     * @SWG\Response(
     *     response=200,
     *     description="Get a Wishlist Items",
     * )
     * @SWG\Tag(name="wishlist item")
     * @Route("/api/wishlist/{id}/items/{itemId}", name="get-wish-list-item", methods={"GET"})
     */
    public function getOne(int $id, int $itemId): JsonResponse
    {
        $wishListItem = $this->wishListItemService->get($id, $itemId);

        $this->restResponseManager->setDataBody($wishListItem, Response::HTTP_OK);

        if(count($errors = $this->wishListItemService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Update a Wishlist Items.
     * @SWG\Response(
     *     response=202,
     *     description="Update a Wishlist Items",
     * )
     * @SWG\Tag(name="wishlist item")
     * @Route("/api/wishlist/{id}/items/{itemId}", name="update-wish-list-item", methods={"PUT"})
     */
    public function update(int $id, int $itemId): JsonResponse
    {
        $wishListItem = $this->wishListItemService->update($id, $itemId);

        $this->restResponseManager->setDataBody($wishListItem, Response::HTTP_ACCEPTED);

        if(count($errors = $this->wishListItemService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }

    /**
     * Delete a Wishlist Items.
     * @SWG\Response(
     *     response=204,
     *     description="Delete a Wishlist Items",
     * )
     * @SWG\Tag(name="wishlist item")
     * @Route("/api/wishlist/{id}/items/{itemId}", name="delete-wish-list-item", methods={"DELETE"})
     */
    public function delete(int $id, int $itemId): JsonResponse
    {
        $this->wishListItemService->delete($id, $itemId);

        $this->restResponseManager->setDataBody([], Response::HTTP_NO_CONTENT);

        if(count($errors = $this->wishListItemService->getLastErrors())) {

            $this->restResponseManager->setDataBody($errors, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->restResponseManager->getResponse();
    }
}