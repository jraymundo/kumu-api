<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends AbstractApiResponse
{
    /**
     * @var array
     */
    protected $errorMeta = [];

    /**
     * @param string $message
     * @param string $code
     * @return Response|static
     */
    public function httpNotFound($message = '', $code = 'NOT_FOUND')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->setErrorCode($code)->setMessage($message);
    }

    /**
     * @param string $message
     * @param string $code
     * @return Response|static
     */
    public function unAuthorized($message = '', $code = 'UNAUTHORIZED')
    {
        return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)->setErrorCode($code)->setMessage($message);
    }

    /**
     * @param string $message
     * @param string $code
     * @return Response|static
     */
    public function badRequest($message = '', $code = 'BAD_REQUEST')
    {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)->setErrorCode($code)->setMessage($message);
    }

    /**
     * @param string $message
     * @param string $code
     * @return Response|static
     */
    public function unProcessableEntity($message = '', $code = 'UNPROCESSABLE_ENTITY')
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setErrorCode($code)->setMessage($message);
    }

    /**
     * @param string $message
     * @param string $code
     * @return Response|static
     */
    public function internalServer($message = '', $code = 'INTERNAL_SERVER')
    {
        //$message = 'Something went wrong. Please contact your administrator to check logs.';

        $result = $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)->setErrorCode($code)->setMessage($message);

        Log::info('--------------------------------------INTERNAL SERVER ERROR START--------------------------------------');
        Log::info(json_encode($result));
        Log::info('--------------------------------------INTERNAL SERVER ERROR END--------------------------------------');
        return $result;
    }

    /**
     * @param $messageDetail
     *
     * @return Response|static
     */
    public function setMessage($messageDetail)
    {
        $message = $this->buildErrorMessage($messageDetail);

        $message = array_merge($message, $this->errorMeta);

        return $this->outputToJson([
                'status' => $this->getStatusCode(),
                'title' => Response::$statusTexts[$this->getStatusCode()],
                'code' => $this->getErrorCode(),
                'error' => $message
            ]
        );
    }

    /**
     * @param string $detail
     *
     * @return array
     */
    protected function buildErrorMessage($detail)
    {
        return [
            'detail' => $detail
        ];
    }
}
