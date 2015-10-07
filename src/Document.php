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

use JsonSerializable;
use Tobscure\JsonApi\Element\ElementInterface;
use Tobscure\JsonApi\Element\Resource;

class Document implements JsonSerializable
{
    /**
     * The links array.
     *
     * @var array
     */
    protected $links;

    /**
     * The included array.
     *
     * @var array
     */
    protected $included = [];

    /**
     * The meta data array.
     *
     * @var array
     */
    protected $meta;

    /**
     * The errors array.
     *
     * @var array
     */
    protected $errors;

    /**
     * The jsonapi array.
     *
     * @var array
     */
    protected $jsonapi;

    /**
     * The data object.
     *
     * @var ElementInterface
     */
    protected $data;

    /**
     * @param ElementInterface $data
     */
    public function __construct(ElementInterface $data = null)
    {
        $this->data = $data;
    }

    /**
     * Get included resources.
     *
     * @param ElementInterface $element
     * @return Resource[]
     */
    protected function getIncluded(ElementInterface $element)
    {
        $included = [];

        foreach ($element->getResources() as $resource) {
            $included = $this->mergeResource($included, $resource);

            foreach ($resource->getRelationships() as $relationship) {
                $includedElement = $relationship->getData();

                foreach ($this->getIncluded($includedElement, false) as $child) {
                    $included = $this->mergeResource($included, $child);
                }
            }
        }

        return $included;
    }

    /**
     * @param Resource[] $resources
     * @param Resource $newResource
     * @return Resource[]
     */
    protected function mergeResource(array $resources, Resource $newResource)
    {
        $type = $newResource->getType();
        $id = $newResource->getId();

        foreach ($resources as $resource) {
            if ($resource->getType() === $type && $resource->getId() === $id) {
                $resource->merge($newResource);

                return $resources;
            }
        }

        $resources[] = $newResource;

        return $resources;
    }

    /**
     * Set the data object.
     *
     * @param ElementInterface $element
     * @return $this
     */
    public function setData(ElementInterface $element)
    {
        $this->data = $element;

        return $this;
    }

    /**
     * Add a link.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addLink($key, $value)
    {
        $this->links[$key] = $value;

        return $this;
    }

    /**
     * Add meta data.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    /**
     * Set the meta data array.
     *
     * @param array $meta
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Set the errors array.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Set the jsonapi array.
     *
     * @param array $jsonapi
     * @return $this
     */
    public function setJsonapi($jsonapi)
    {
        $this->jsonapi = $jsonapi;

        return $this;
    }

    /**
     * Map everything to arrays.
     *
     * @return array
     */
    public function toArray()
    {
        $document = [];

        if (! empty($this->links)) {
            ksort($this->links);

            $document['links'] = $this->links;
        }

        if (! empty($this->data)) {
            $resources = $this->getIncluded($this->data);

            $document['data'] = array_shift($resources)->toArray();

            if (count($resources)) {
                $document['included'] = array_map(function (Resource $resource) {
                    return $resource->toArray();
                }, $resources);
            }
        }

        if (! empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        if (! empty($this->errors)) {
            $document['errors'] = $this->errors;
        }

        if (! empty($this->jsonapi)) {
            $document['jsonapi'] = $this->jsonapi;
        }

        return $document;
    }

    /**
     * Map to string.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * Serialize for JSON usage.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
