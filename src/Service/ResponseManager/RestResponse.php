<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 12/03/2020
 * Time: 23:36
 */

namespace App\Service\ResponseManager;


use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RestResponse
{
    private $statusCode;

    private $dataBody;

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Set Data Body of response
     * @param mixed $data
     * @param int $statusCode
     * @return RestResponse
     */
    public function setDataBody($data, int $statusCode = Response::HTTP_OK): self
    {
        $this->dataBody = $data;

        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Return JSON Response of Serialized Data
     * @return JsonResponse
     */
    public function getResponse(): JsonResponse
    {
        return JsonResponse::fromJsonString($this->serializer->serialize($this->dataBody, 'json'), $this->statusCode);
    }
}