<?php namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Elements\Resource;
use Tobscure\JsonApi\Elements\Collection;

class SerializerAbstract
{
    protected $type;

    protected $link = [];

    protected $include = [];

    public function __construct($include = null, $link = null)
    {
        // Override the defaults if includes are specified, as per the JSON-API
        // spec: "If this parameter is used, ONLY the requested linked resources
        // should be returned alongside the primary resource(s)."
        if (! is_null($include)) {
            $this->include = $include;
        }

        if (! is_null($link)) {
            $this->link = $link;
        }
    }

    public function setInclude($include)
    {
        $this->include = $include;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function collection($dataSet)
    {
        if (empty($dataSet)) {
            return;
        }

        $collection = new Collection($this->type, $this->href());

        $resources = [];
        foreach ($dataSet as $data) {
            $resources[] = $this->resource($data);
        }
        $collection->setResources($resources);

        return $collection;
    }

    public function resource($data)
    {
        if (empty($data)) {
            return;
        }

        $resource = new Resource($this->type, $this->href());

        if (is_object($data)) {
            $resource->setId($data->id);
            $resource->setAttributes($this->attributes($data));

            $relations = $this->parseRelations($this->link);
            foreach ($relations as $name => $nested) {
                $method = 'link'.ucfirst($name);
                if ($element = $this->$method($data, $nested)) {
                    $resource->addLink($name, $element);
                }
            }

            $relations = $this->parseRelations($this->include);
            foreach ($relations as $name => $nested) {
                $method = 'include'.ucfirst($name);
                if ($element = $this->$method($data, $nested)) {
                    $resource->addInclude($name, $element);
                }
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
