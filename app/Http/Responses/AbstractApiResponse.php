<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiResponse
{
    const CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * array @var
     */
    protected $meta = [];

    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var string
     */
    protected $errorCode = '';

    /**
     * @var array
     */
    protected $headers = ['Content-Type' => self::CONTENT_TYPE];

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode = Response::HTTP_OK)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setErrorCode($code)
    {
        $this->errorCode = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param $message
     *
     * @return Response|static
     */
    final public function outputToJson($message)
    {
        return new JsonResponse($message, $this->getStatusCode(), $this->getHeaders());
    }
}
