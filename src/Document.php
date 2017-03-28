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
    use SelfLinkTrait;
    use PaginationLinksTrait;
    use MetaTrait;

    const MEDIA_TYPE = 'application/vnd.api+json';
    const DEFAULT_API_VERSION = '1.0';

    /**
     * The primary data.
     *
     * @var ResourceInterface|ResourceInterface[]|null
     */
    protected $data;

    /**
     * The errors array.
     *
     * @var Error[]|null
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
     * Use named constructors instead.
     */
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
        $document->replaceMeta($meta);

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
     * Get the primary data.
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
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get the errors array.
     *
     * @return Error[]|null $errors
     */
    public function getErrors()
    {
        return $this->errors;
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
     * Get the relationships to include.
     *
     * @return string[] $include
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * Set the relationships to include.
     *
     * @param string[] $include
     */
    public function setInclude(array $include)
    {
        $this->include = $include;
    }

    /**
     * Get the sparse fieldsets.
     *
     * @return array[] $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the sparse fieldsets.
     *
     * @param array[] $fields
     */
    public function setFields(array $fields)
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

            // Build a multi-dimensional map of all of the distinct resources
            // that are present in the document, indexed by type and ID. This is
            // done by recursively looping through each of the resources and
            // their included relationships. We do this so that any resources
            // that are duplicated may be merged back into a single instance.
            $map = [];
            $resources = $isCollection ? $this->data : [$this->data];

            $this->mergeResources($map, $resources, $this->include);

            // Now extract the document's primary resource(s) from the resource
            // map, and flatten the map's remaining resources to be included in
            // the document's "included" array.
            foreach ($resources as $resource) {
                $type = $resource->getType();
                $id = $resource->getId();

                if (isset($map[$type][$id])) {
                    $primary[] = $map[$type][$id];
                    unset($map[$type][$id]);
                }
            }

            $document['data'] = $isCollection ? $primary : $primary[0];
            $document['included'] = call_user_func_array('array_merge', $map);
        }

        return array_filter($document);
    }

    /**
     * Build the JSON-API document and encode it as a JSON string.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Recursively add the given resources and their relationships to a map.
     *
     * @param array &$map The map to merge resources into.
     * @param ResourceInterface[] $resources
     * @param array $include An array of relationship paths to include.
     */
    private function mergeResources(array &$map, array $resources, array $include)
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

                    $this->mergeResources($map, $children, $nested);
                }
            }

            // Serialize the resource into an array and add it to the map. If
            // it is already present, its properties will be merged into the
            // existing resource.
            $this->mergeResource($map, $resource, $relationships);
        }
    }

    /**
     * Merge the given resource into a resource map.
     *
     * If it is already present in the map, its properties will be merged into
     * the existing resource.
     *
     * @param array &$map
     * @param ResourceInterface $resource
     * @param Relationship[] $relationships
     */
    private function mergeResource(array &$map, ResourceInterface $resource, array $relationships)
    {
        $type = $resource->getType();
        $id = $resource->getId();
        $meta = $resource->getMeta();
        $links = $resource->getLinks();

        $fields = isset($this->fields[$type]) ? $this->fields[$type] : null;

        $attributes = $resource->getAttributes($fields);

        if ($fields) {
            $keys = array_flip($fields);

            $attributes = array_intersect_key($attributes, $keys);
            $relationships = array_intersect_key($relationships, $keys);
        }

        $props = array_filter(compact('attributes', 'relationships', 'links', 'meta'));

        if (empty($map[$type][$id])) {
            $map[$type][$id] = compact('type', 'id') + $props;
        } else {
            $map[$type][$id] = array_replace_recursive($map[$type][$id], $props);
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
     * @param string[] $paths
     *
     * @return array[]
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
}
