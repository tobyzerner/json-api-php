<?php namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Elements\Resource;
use Tobscure\JsonApi\Elements\Collection;

class SerializerAbstract
{

    protected $type;

    protected $link = [];

    protected $include = [];

    public function __construct($include = [])
    {
        $this->include = array_merge($this->include, $include);
    }

    public function getUrl()
    {
        return url($this->type);
    }

    public function collection($dataSet)
    {
        $collection = new Collection($this->type, $this->getUrl());

        $resources = [];
        foreach ($dataSet as $data) {
            $resources[] = $this->resource($data);
        }
        $collection->setResources($resources);

        return $collection;
    }

    public function resource($data)
    {
        $resource = new Resource($this->type, $this->getUrl());

        if (is_object($data)) {
            $resource->setId($data->id);
            $resource->setAttributes($this->attributes($data));

            $relations = $this->parseRelations(array_merge($this->link, $this->include));

            foreach ($relations as $name => $nested) {
                $method = (in_array($name, $this->include) ? 'include' : 'link').ucfirst($name);
                $linkedElement = $this->$method($data, $nested);
                $resource->addLink($name, $linkedElement);
            }
        } else {
            $resource->setId($data);
        }

        return $resource;
    }

    // Given a flat array of relation paths (e.g. ['user', 'user.employer', 'user.employer.country', 'comments']),
    // create a nested array of relations one-level deep that can be passed on to other serializers.
    // e.g. ['user' => ['employer', 'employer.country'], 'comments' => []]
    protected function parseRelations($relations)
    {
        $tree = [];

        foreach ($relations as $path) {
            list($primary, $nested) = array_pad(explode('.', $path, 2), 2, null);

            if ( ! isset($tree[$primary])) {
                $tree[$primary] = [];
            }

            if ($nested) {
                $tree[$primary][] = $nested;
            }
        }

        return $tree;
    }

}
