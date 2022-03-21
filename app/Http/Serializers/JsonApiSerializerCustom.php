<?php

namespace App\Http\Serializers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\Fractal\Serializer\ArraySerializer;

class JsonApiSerializerCustom extends ArraySerializer
{
    /**
     * @var string
     */
    protected $baseApiUrl;

    /**
     * JsonApiSerializerCustom constructor.
     */
    public function __construct()
    {
        $this->baseApiUrl = Config::get('settings.current_uri');
    }

    /**
     * Serialize an item.
     *
     * @param  string  $resourceKey
     * @param  array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data): array
    {
        $id = $this->getIdFromData($data);

        $resource = [
            'data' => [
                'type' => Str::plural($resourceKey),
                'id' => "$id",
                'attributes' => $data,
            ],
        ];

        $resource['links']['self'] = $this->baseApiUrl.$resource['data']['attributes']['links']['uri'];

        unset($resource['data']['attributes']['links']);
        unset($resource['data']['attributes']['id']);

        return $resource;
    }

    /**
     * @param $resourceKey
     * @param  array  $data
     * @return array
     */
    public function itemForCollectionSerializer($resourceKey, array $data)
    {
        $id = $this->getIdFromData($data);

        $resource = [
            'type' => Str::plural($resourceKey),
            'id' => $id,
            'attributes' => $data,
        ];

        unset($resource['attributes']['id']);
        $resource['links']['self'] = $this->baseApiUrl.$resource['attributes']['links']['uri'];
        unset($resource['attributes']['links']);

        return $resource;
    }

    /**
     * Serialize a collection.
     *
     * @param  string  $resourceKey
     * @param  array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data): array
    {
        $resources = [];
        foreach ($data as $resource) {
            $resources[] = $this->itemForCollectionSerializer($resourceKey, $resource);
        }

        return [
            'data' => $resources,
            'links' => ['self' => Request::fullUrl()],
        ];
    }

    /**
     * @param  array  $data
     *
     * @return integer
     */
    protected function getIdFromData(array $data)
    {
        if (!array_key_exists('id', $data)) {
            throw new InvalidArgumentException(
                'JSON API resource objects MUST have a valid id'
            );
        }

        return $data['id'];
    }
}
