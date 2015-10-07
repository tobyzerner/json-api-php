<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi;

class Collection implements ElementInterface
{
    /**
     * @var array
     */
    protected $resources;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Create a new collection instance.
     *
     * @param array $data
     * @param SerializerInterface $serializer
     */
    public function __construct(array $data, SerializerInterface $serializer)
    {
        $this->resources = array_map(function ($data) use ($serializer) {
            return new Resource($data, $serializer);
        }, $data);

        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set the resources array.
     *
     * @param array $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * Request a relationship to be included for all resources.
     *
     * @param string|array $relationships
     * @return $this
     */
    public function with($relationships)
    {
        foreach ($this->resources as $resource) {
            $resource->with($relationships);
        }

        return $this;
    }

    /**
     * Request a relationship to be identified for all resources.
     *
     * @param string|array $relationships
     * @return $this
     */
    public function identify($relationships)
    {
        foreach ($this->resources as $resource) {
            $resource->identify($relationships);
        }

        return $this;
    }

    /**
     * Request a restricted set of fields.
     *
     * @param array|null $fields
     * @return $this
     */
    public function fields($fields)
    {
        foreach ($this->resources as $resource) {
            $resource->fields($fields);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(function (Resource $resource) {
            return $resource->toArray();
        }, $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier()
    {
        return array_map(function (Resource $resource) {
            return $resource->toIdentifier();
        }, $this->resources);
    }
}
