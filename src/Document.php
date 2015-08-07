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

/**
 * This is the document class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class Document implements JsonSerializable
{
    protected $links;

    protected $included = [];

    protected $meta;

    protected $errors;

    protected $data;

    public function addIncluded($link)
    {
        $resources = $link->getData()->getResources();

        foreach ($resources as $k => $resource) {
            // If the resource doesn't have any attributes, then we don't need to
            // put it into the included part of the document.
            if (!$resource->getAttributes()) {
                unset($resources[$k]);
            } else {
                foreach ($resource->getIncluded() as $link) {
                    $this->addIncluded($link);
                }
            }
        }

        foreach ($resources as $k => $resource) {
            foreach ($this->included as $includedResource) {
                if ($includedResource->getType() === $resource->getType()
                    && $includedResource->getId() === $resource->getId()) {
                    $includedResource->merge($resource);
                    unset($resources[$k]);
                    break;
                }
            }
        }

        if ($resources) {
            $this->included = array_merge($this->included, $resources);
        }

        return $this;
    }

    public function setData($element)
    {
        $this->data = $element;

        if ($element) {
            foreach ($element->getResources() as $resource) {
                foreach ($resource->getIncluded() as $link) {
                    $this->addIncluded($link);
                }
            }
        }

        return $this;
    }

    public function addLink($key, $value)
    {
        $this->links[$key] = $value;

        return $this;
    }

    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    public function toArray()
    {
        $document = [];

        if (!empty($this->links)) {
            ksort($this->links);
            $document['links'] = $this->links;
        }

        if (!empty($this->data)) {
            $document['data'] = $this->data->toArray();
        }

        if (!empty($this->included)) {
            $document['included'] = [];
            foreach ($this->included as $resource) {
                $document['included'][] = $resource->toArray();
            }
        }

        if (!empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        if (!empty($this->errors)) {
            $document['errors'] = $this->errors;
        }

        return $document;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
