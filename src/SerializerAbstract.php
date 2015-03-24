<?php namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Elements\Resource;
use Tobscure\JsonApi\Elements\Collection;

abstract class SerializerAbstract
{
    protected $type;

    protected $link = [];

    protected $include = [];

    public function __construct($include = null, $link = null)
    {
        // Override the defaults if includes are specified, as per the JSON-
        // API spec: "If a client supplies an include parameter, the server
        // MUST NOT include other resource objects in the included section of
        // the compound document."
        if (! empty($include)) {
            $this->include = $include;
        }

        if (! empty($link)) {
            $this->link = $link;
        }
    }

    abstract protected function attributes($model);

    public function setInclude($include)
    {
        $this->include = $include;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function collection($data)
    {
        if (empty($data)) {
            return;
        }

        $resources = [];
        foreach ($data as $record) {
            $resources[] = $this->resource($record);
        }

        return new Collection($this->type, $resources);
    }

    public function resource($data)
    {
        if (empty($data)) {
            return;
        }

        if (! is_object($data)) {
            return new Resource($this->type, $data);
        } else {
            $links = [];

            foreach (['link', 'include'] as $type) {
                $include = $type === 'include';

                $relationships = $this->parseRelationshipPaths($this->$type);
                foreach ($relationships as $name => $nested) {
                    if (($method = $this->$name()) && ($element = $method($data, $include, $include ? $nested : null))) {
                        if (! ($element instanceof Link)) {
                            $element = new Link($element);
                        }
                        $links[$name] = $element;
                    }
                }
            }

            return new Resource($this->type, $data->id, $this->attributes($data), $links);
        }
    }

    // Given a flat array of relationship paths (e.g. ['user',
    // 'user.employer', 'user.employer.country', 'comments']), create a nested
    // array of relationship paths one-level deep that can be passed on to
    // other serializers. e.g. ['user' => ['employer', 'employer.country'],
    // 'comments' => []]
    protected function parseRelationshipPaths($paths)
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
