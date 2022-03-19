<?php

namespace App\Http\Responses;

use App\Http\Serializers\JsonApiSerializerCustom;
use Illuminate\Http\Request as DefaultRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Collection as IlluminateCollection;
use Laravel\Lumen\Http\ResponseFactory;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\Response;

class FractalResponse extends AbstractApiResponse
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * ItemResponse constructor.
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->manager->setSerializer(new JsonApiSerializerCustom());
    }

    /**
     * @param $entity
     * @param $transformer |null
     * @return Response|static
     */
    public function item($entity, $transformer = null, $includes = [])
    {
        $entityBaseName = $this->getEntityBaseClassName($entity);
        list($moduleName, $type) = $this->getTypeAndModuleName($entityBaseName);

        if (is_null($transformer)) {
            $transformer = $this->getTransformer($moduleName, $entityBaseName);
        }

        $data = new Item($entity, $transformer, $type);

        return $this->outputToJson($this->setMetaAndCreateData($data));
    }

    /**
     * @param object $entity
     * @param $transformer |null
     * @return \Symfony\Component\HttpFoundation\Response
     * @codeCoverageIgnore
     */
    public function collection($entity, $transformer = null, $includes = [])
    {
        if (empty($entity[0])) {
            return $this->nullResource();
        }

        $entityBaseName = $this->getEntityBaseClassName($entity[0]);
        list($moduleName, $type) = $this->getTypeAndModuleName($entityBaseName);

        if (is_null($transformer)) {
            $transformer = $this->getTransformer($moduleName, $entityBaseName);
        }

        $data = new Collection($entity, $transformer, $type);

        return $this->outputToJson($this->setMetaAndCreateData($data));
    }

    /**
     * @param $entity
     * @param DefaultRequest $request
     * @param null $transformer
     * @param array $includes
     * @return FractalResponse|Response
     * @codeCoverageIgnore
     */
    public function cursor($entity, DefaultRequest $request, $transformer = null, $includes = [])
    {
        if (empty($entity[0])) {
            return $this->nullResource();
        }

        $entityBaseName = $this->getEntityBaseClassName($entity[0]);
        list($moduleName, $type) = $this->getTypeAndModuleName($entityBaseName);

        if (is_null($transformer)) {
            $transformer = $this->getTransformer($moduleName, $entityBaseName);
        }

        $current = $request->get('current', null);
        $previous = $request->get('previous', null);


        $newCursor = $entity->last()->id;

        $cursor = new Cursor($current, $previous, $newCursor, $entity->count());

        $resource = new Collection($entity, $transformer, $type);
        $resource->setCursor($cursor);

        return $this->outputToJson($this->setMetaAndCreateData($resource));
    }

    /**
     * @param $entity
     * @param null $transformer
     * @return FractalResponse|Response
     */
    public function paginate($entity, $transformer = null)
    {
        if (empty($entity[0])) {
            return $this->nullPaginatedResource();
        }

        $entityBaseName = $this->getEntityBaseClassName($entity[0]);
        list($moduleName, $type) = $this->getTypeAndModuleName($entityBaseName);

        if (is_null($transformer)) {
            $transformer = $this->getTransformer($moduleName, $entityBaseName);
        }

        $entityCollection = $entity->getCollection();

        $resource = new Collection($entityCollection, $transformer, $type);
        $resource->setPaginator(new IlluminatePaginatorAdapter($entity));

        return $this->outputToJson($this->setMetaAndCreateData($resource));
    }

    /**
     * @return Response|static
     * @codeCoverageIgnore
     */
    public function nullResource()
    {
        return $this->outputToJson([
            'data' => [],
            'links' => ['self' => Request::url()],
        ]);
    }

    /**
     * @return Response|static
     */
    public function nullPaginatedResource()
    {
        return $this->outputToJson([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total' => 0,
                    'count' => 0,
                    'per_page' => 0,
                    'current_page' => 0,
                    'total_pages' => 0,
                    'links' => [],
                ],
            ],
            'links' => ['self' => Request::url()],
        ]);
    }

    /**
     * @param string $content
     * @param int $statusCode
     * @return \Illuminate\Http\Response
     */
    public function nullRoute($content = '', $statusCode = Response::HTTP_OK)
    {
        return (new ResponseFactory())->make($content, $statusCode);
    }

    /**
     * @param string $type
     * @param IlluminateCollection $attributes
     * @param string $transformer
     * @param int $statusCode
     * @return FractalResponse|Response
     */
    public function customCollection(
        string $type,
        IlluminateCollection $attributes,
        string $transformer,
        $statusCode = Response::HTTP_OK
    ) {

        $transformer = $this->getTransformer($type, $transformer);

        $data = new Collection($attributes, $transformer, $type);

        return $this->outputToJson($this->setMetaAndCreateData($data));

/*        return $this->outputToJson([
            'data' => [
                'type' => $type,
                'attributes' => $attributes,
            ],
            'links' => ['self' => Request::url()],
        ]);*/
    }

    /**
     * @param $entity
     * @return Response|static
     */
    public function createSuccess($entity, $transformer = null)
    {
        return $this->setStatusCode(Response::HTTP_CREATED)->item($entity, $transformer);
    }

    /**
     * @param int $statusCode
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @codeCoverageIgnore
     */
    public function noContent($message)
    {
        return (new ResponseFactory())->make($message, 204);
    }

    /**
     * @param string $path
     * @param string $name
     * @param array $headers
     * @param string $disposition
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $path, string $name, $headers = [], $disposition = 'attachment')
    {
        return response()->download($path, $name, $headers, $disposition);
    }

    /**
     * @param Item|Collection $data
     *
     * @return array
     */
    protected function setMetaAndCreateData($data)
    {
        $data->setMeta($this->getMeta());

        return $this->manager->createData($data)->toArray();
    }

    /**
     * @param string $entityBaseName
     *
     * @return object
     */
    protected function getTransformer($modulename, $entityBaseName)
    {
        $transformer = 'App\Http\Transformers\\'.$entityBaseName.'Transformer';

        return new $transformer;
    }

    /**
     * @param string $entityBaseName
     * @return array
     * @codeCoverageIgnore
     */
    protected function getTypeAndModuleName($entityBaseName)
    {
        $entity = '';
        $pattern = '/(?<=[a-z])(?=[A-Z])/x';
        $splitName = preg_split($pattern, $entityBaseName);

        $moduleName = $splitName[0];

        if ($entity == '') {
            $entity = $moduleName;
        }

        if (count($splitName) == 2) {
            $splitPattern = preg_split($pattern, $splitName[1]);
            $entity = join($splitPattern, "_");

            return [$moduleName, strtolower($entity)];
        }

        unset($splitName[0]);

        for ($i = 1; $i <= count($splitName) - 1; $i++) {
            $entity = join($splitName, "_");
        }

        return [$moduleName, strtolower($entity)];
    }

    /**
     * @param object $entity
     * @return string
     */
    protected function getEntityBaseClassName($entity)
    {
        $chunkEntityPath = explode('\\', get_class($entity));

        return array_pop($chunkEntityPath);
    }
}
