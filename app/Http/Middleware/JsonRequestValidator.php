<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use App\Validators\JsonRequest\JsonRequestValidatorFactory;
use App\Validators\JSONSchemaFilter\JSONSchemaFilterFactory;
use Closure;
use Illuminate\Contracts\Validation\Validator as ContractValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use Symfony\Component\HttpFoundation\Response;

class JsonRequestValidator
{
    use ProvidesConvenienceMethods;

    /**
     * @var ErrorResponse
     */
    private $errorResponse;

    public function __construct(ErrorResponse $errorResponse)
    {
        $this->errorResponse = $errorResponse;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $validatorKey = $this->getUsesRoute($request).'_'.strtolower($request->getMethod());

        $attributes = $this->filterAttributes($request, $validatorKey);

        $baseValidatorResult = $this->validateBaseRules($request, $attributes);

        if ($baseValidatorResult->fails()) {
            return $this->returnErrorMessage($baseValidatorResult);
        }

        $request->request->add($attributes);

        $request->request->remove('data');

        return $next($request);
    }

    /**
     * @param Request $request
     * @param string $validatorKey
     * @return array
     */
    public function filterAttributes(Request $request, string $validatorKey) : array
    {
        return $this->filterSchema($validatorKey, $request['data']['attributes']);
    }

    /**
     * @param string $validatorKey
     * @param array $attributes
     * @return array
     */
    private function filterSchema(string $validatorKey, array $attributes): array
    {
        return JSONSchemaFilterFactory::make($validatorKey, $attributes);
    }

    /**
     * @param Request $request
     * @param array $attributes
     * @return ContractValidator
     */
    private function validateBaseRules(Request $request, array $attributes): ContractValidator
    {
        $validatorKey = $this->getUsesRoute($request) . '_' . strtolower($request->getMethod());

        $validator = JsonRequestValidatorFactory::make($validatorKey);

        return Validator::make($attributes, $validator->rules(), $validator->messages());
    }

    /**
     * @param ContractValidator $validationErrors
     * @return ErrorResponse|Response
     */
    private function returnErrorMessage(ContractValidator $validationErrors)
    {
        return $this->errorResponse
            ->setErrorCode('FORM_REQUEST_VALIDATION')
            ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->outputToJson([
                'status' => $this->errorResponse->getStatusCode(),
                'title' => Response::$statusTexts[$this->errorResponse->getStatusCode()],
                'code' => $this->errorResponse->getErrorCode(),
                'errors' => $this->collectErrorMessages($this->formatValidationErrors($validationErrors))
            ]);
    }

    /**
     * @param Request $request
     * @return mixed|null|string
     */
    private function getUsesRoute(Request $request)
    {
        return $request->route()[1]['uses'];
    }

    /**
     * @param array $errors
     * @return array
     */
    private function collectErrorMessages($errors)
    {
        $errorMessages = [];

        foreach ($errors as $key => $error) {
            $errorMessages[] = $this->buildNewErrorMessage($key, $error[0]);
        }

        return $errorMessages;
    }

    /**
     * @param string $attribute
     * @param string $detail
     *
     * @return array
     */
    private function buildNewErrorMessage($attribute, $detail)
    {
        return [
            'attribute' => $attribute,
            'detail' => $detail
        ];
    }
}
