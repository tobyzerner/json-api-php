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

class Document implements JsonSerializable
{
    use LinksTrait, SelfLinkTrait, PaginationLinksTrait, MetaTrait;

    const MEDIA_TYPE = 'application/vnd.api+json';

    private $data;
    private $errors;
    private $jsonapi;

    private $include = [];
    private $fields = [];

    private function __construct()
    {
    }

    /**
     * @param ResourceInterface|ResourceInterface[] $data
     *
     * @return self
     */
    public static function fromData($data)
    {
        $document = new self;
        $document->setData($data);

        return $document;
    }

    /**
     * @param array $meta
     *
     * @return self
     */
    public static function fromMeta(array $meta)
    {
        $document = new self;
        $document->setMeta($meta);

        return $document;
    }

    /**
     * @param Error[] $errors
     *
     * @return self
     */
    public static function fromErrors(array $errors)
    {
        $document = new self;
        $document->setErrors($errors);

        return $document;
    }

    /**
     * Set the primary data.
     *
     * @param ResourceInterface|ResourceInterface[]|null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Set the errors array.
     *
     * @param Error[]|null $errors
     */
    public function setErrors(array $errors = null)
    {
        $this->errors = $errors;
    }

    /**
     * Set the jsonapi version.
     *
     * @param string $version
     */
    public function setApiVersion($version)
    {
        $this->jsonapi['version'] = $version;
    }

    /**
     * Set the jsonapi meta information.
     *
     * @param array $meta
     */
    public function setApiMeta(array $meta)
    {
        $this->jsonapi['meta'] = $meta;
    }

    /**
     * Set the relationship paths to include.
     * 
     * @param string[] $include
     */
    public function setInclude($include)
    {
        $this->include = $include;
    }

    /**
     * Set the sparse fieldsets.
     * 
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Serialize for JSON usage.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $document = [
            'links' => $this->links,
            'meta' => $this->meta,
            'errors' => $this->errors,
            'jsonapi' => $this->jsonapi
        ];

        if ($this->data) {
            $isCollection = is_array($this->data);
            $resources = $isCollection ? $this->data : [$this->data];

            $map = $this->buildResourceMap($resources);

            $primary = $this->extractResourcesFromMap($map, $resources);

            $document['data'] = $isCollection ? $primary : $primary[0];

            if ($map) {
                $document['included'] = call_user_func_array('array_merge', $map);
            }
        }

        return (object) array_filter($document);
    }

    private function buildResourceMap(array $resources)
    {
        $map = [];

        $include = $this->buildRelationshipTree($this->include);

        $this->mergeResources($map, $resources, $include);

        return $map;
    }

    private function mergeResources(array &$map, array $resources, array $include)
    {
        foreach ($resources as $resource) {
            $relationships = [];

            foreach ($include as $name => $nested) {
                if (! ($relationship = $resource->getRelationship($name))) {
                    continue;
                }

                $relationships[$name] = $relationship;

                if ($data = $relationship->getData()) {
                    $children = is_array($data) ? $data : [$data];

                    $this->mergeResources($map, $children, $nested);
                }
            }

            $this->mergeResource($map, $resource, $relationships);
        }
    }

    private function mergeResource(array &$map, ResourceInterface $resource, array $relationships)
    {
        $type = $resource->getType();
        $id = $resource->getId();
        $links = $resource->getLinks();
        $meta = $resource->getMeta();

        $fields = isset($this->fields[$type]) ? $this->fields[$type] : null;

        $attributes = $resource->getAttributes($fields);

        if ($fields) {
            $keys = array_flip($fields);

            $attributes = array_intersect_key($attributes, $keys);
            $relationships = array_intersect_key($relationships, $keys);
        }

        if (empty($map[$type][$id])) {
            $map[$type][$id] = new ResourceObject($type, $id);
        }

        array_map([$map[$type][$id], 'setAttribute'], array_keys($attributes), $attributes);
        array_map([$map[$type][$id], 'setRelationship'], array_keys($relationships), $relationships);
        array_map([$map[$type][$id], 'setLink'], array_keys($links), $links);
        array_map([$map[$type][$id], 'setMetaItem'], array_keys($meta), $meta);
    }

    private function extractResourcesFromMap(array &$map, array $resources)
    {
        return array_filter(
            array_map(function ($resource) use (&$map) {
                $type = $resource->getType();
                $id = $resource->getId();

                if (isset($map[$type][$id])) {
                    $resource = $map[$type][$id];
                    unset($map[$type][$id]);

                    return $resource;
                }
            }, $resources)
        );        
    }

    private function buildRelationshipTree(array $paths)
    {
        $tree = [];

        foreach ($paths as $path) {
            $keys = explode('.', $path);
            $array = &$tree;

            foreach ($keys as $key) {
                if (! isset($array[$key])) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }
        }

        return $tree;
    }
}
