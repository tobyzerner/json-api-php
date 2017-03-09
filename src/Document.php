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
    use LinksTrait;
    use MetaTrait;

    const MEDIA_TYPE = 'application/vnd.api+json';

    /**
     * The data object.
     *
     * @var ResourceInterface|ResourceInterface[]|null
     */
    protected $data;

    /**
     * The errors array.
     *
     * @var array|null
     */
    protected $errors;

    /**
     * The jsonapi array.
     *
     * @var array|null
     */
    protected $jsonapi;

    /**
     * Relationships to include.
     *
     * @var array
     */
    protected $include = [];

    /**
     * Sparse fieldsets.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * @param ResourceInterface|ResourceInterface[] $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Get the data object.
     *
     * @return ResourceInterface|ResourceInterface[]|null $data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data object.
     *
     * @param ResourceInterface|ResourceInterface[]|null $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the errors array.
     *
     * @return array|null $errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the errors array.
     *
     * @param array|null $errors
     *
     * @return $this
     */
    public function setErrors(array $errors = null)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the jsonapi array.
     *
     * @return array|null $jsonapi
     */
    public function getJsonapi()
    {
        return $this->jsonapi;
    }

    /**
     * Set the jsonapi array.
     *
     * @param array|null $jsonapi
     *
     * @return $this
     */
    public function setJsonapi(array $jsonapi = null)
    {
        $this->jsonapi = $jsonapi;

        return $this;
    }

    /**
     * Get the relationships to include.
     *
     * @return array $include
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * Set the relationships to include.
     *
     * @param array $include
     *
     * @return $this
     */
    public function setInclude(array $include)
    {
        $this->include = $include;

        return $this;
    }

    /**
     * Get the sparse fieldsets.
     *
     * @return array $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the sparse fieldsets.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Build the JSON-API document as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $document = [];

        if ($this->links) {
            $document['links'] = $this->links;
        }

        if ($this->data) {
            $isCollection = is_array($this->data);

            // Build a multi-dimensional map of all of the distinct resources
            // that are present in the document, indexed by type and ID. This is
            // done by recursively looping through each of the resources and
            // their included relationships. We do this so that any resources
            // that are duplicated may be merged back into a single instance.
            $map = [];
            $resources = $isCollection ? $this->data : [$this->data];

            $this->addResourcesToMap($map, $resources, $this->include);

            // Now extract the document's primary resource(s) from the resource
            // map, and flatten the map's remaining resources to be included in
            // the document's "included" array.
            foreach ($resources as $resource) {
                $type = $resource->getType();
                $id = $resource->getId();

                $primary[] = $map[$type][$id];
                unset($map[$type][$id]);
            }

            $included = call_user_func_array('array_merge', $map);

            $document['data'] = $isCollection ? $primary : $primary[0];

            if ($included) {
                $document['included'] = $included;
            }
        }

        if ($this->meta) {
            $document['meta'] = $this->meta;
        }

        if ($this->errors) {
            $document['errors'] = $this->errors;
        }

        if ($this->jsonapi) {
            $document['jsonapi'] = $this->jsonapi;
        }

        return $document;
    }

    /**
     * Build the JSON-API document and encode it as a JSON string.
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

    /**
     * Recursively add the given resources and their relationships to a map.
     *
     * @param array &$map The map to merge resources into.
     * @param ResourceInterface[] $resources
     * @param array $include An array of relationship paths to include.
     */
    private function addResourcesToMap(array &$map, array $resources, array $include)
    {
        // Index relationship paths so that we have a list of the direct
        // relationships that will be included on these resources, and arrays
        // of their respective nested relationships.
        $include = $this->indexRelationshipPaths($include);

        foreach ($resources as $resource) {
            $relationships = [];

            // Get each of the relationships we're including on this resource,
            // and add their resources (and their relationships, and so on) to
            // the map.
            foreach ($include as $name => $nested) {
                if (! ($relationship = $resource->getRelationship($name))) {
                    continue;
                }

                $relationships[$name] = $relationship;

                if ($data = $relationship->getData()) {
                    $children = is_array($data) ? $data : [$data];

                    $this->addResourcesToMap($map, $children, $nested);
                }
            }

            // Serialize the resource into an array and add it to the map. If
            // it is already present, its properties will be merged into the
            // existing resource.
            $this->addResourceToMap($map, $resource, $relationships);
        }
    }

    /**
     * Serialize the given resource as an array and add it to the given map.
     *
     * If it is already present in the map, its properties will be merged into
     * the existing array.
     *
     * @param array &$map
     * @param ResourceInterface $resource
     * @param Relationship[] $resource
     */
    private function addResourceToMap(array &$map, ResourceInterface $resource, array $relationships)
    {
        $type = $resource->getType();
        $id = $resource->getId();

        if (empty($map[$type][$id])) {
            $map[$type][$id] = [
                'type' => $type,
                'id' => $id
            ];
        }

        $array = &$map[$type][$id];
        $fields = $this->getFieldsForType($type);

        if ($meta = $resource->getMeta()) {
            $array['meta'] = array_replace_recursive(isset($array['meta']) ? $array['meta'] : [], $meta);
        }

        if ($links = $resource->getLinks()) {
            $array['links'] = array_replace_recursive(isset($array['links']) ? $array['links'] : [], $links);
        }

        if ($attributes = $resource->getAttributes($fields)) {
            if ($fields) {
                $attributes = array_intersect_key($attributes, array_flip($fields));
            }
            if ($attributes) {
                $array['attributes'] = array_replace_recursive(isset($array['attributes']) ? $array['attributes'] : [], $attributes);
            }
        }

        if ($relationships && $fields) {
            $relationships = array_intersect_key($relationships, array_flip($fields));
        }
        if ($relationships) {
            $relationships = array_map(function ($relationship) {
                return $relationship->toArray();
            }, $relationships);

            $array['relationships'] = array_replace_recursive(isset($array['relationships']) ? $array['relationships'] : [], $relationships);
        }
    }

    /**
     * Index relationship paths by top-level relationships.
     *
     * Given an array of relationship paths such as:
     *
     * ['user', 'user.employer', 'user.employer.country', 'comments']
     *
     * Returns an array with key-value pairs of top-level relationships and
     * their nested relationships:
     *
     * ['user' => ['employer', 'employer.country'], 'comments' => []]
     *
     * @param array $paths
     *
     * @return array
     */
    private function indexRelationshipPaths(array $paths)
    {
        $tree = [];

        foreach ($paths as $path) {
            list($primary, $nested) = array_pad(explode('.', $path, 2), 2, null);

            if (! isset($tree[$primary])) {
                $tree[$primary] = [];
            }

            if ($nested) {
                $tree[$primary][] = $nested;
            }
        }

        return $tree;
    }

    /**
     * Get the fields that should be included for resources of the given type.
     *
     * @param string $type
     *
     * @return array|null
     */
    private function getFieldsForType($type)
    {
        return isset($this->fields[$type]) ? $this->fields[$type] : null;
    }
}
