<?php

namespace App\Http\Responses;

class ApiResponse
{
    /**
     * @var FractalResponse
     */
    private $fractalResponse;
    /**
     * @var ErrorResponse
     */
    private $errorResponse;

    /**
     * ApiResponse constructor.
     * @param FractalResponse $fractalResponse
     * @param ErrorResponse $errorResponse
     */
    public function __construct(FractalResponse $fractalResponse, ErrorResponse $errorResponse)
    {
        $this->fractalResponse = $fractalResponse;
        $this->errorResponse = $errorResponse;
    }

    /**
     * @return FractalResponse
     */
    public function fractal()
    {
        return $this->fractalResponse;
    }

    /**
     * @return ErrorResponse
     */
    public function error()
    {
        return $this->errorResponse;
    }
}