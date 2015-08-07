<?php namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Elements\Resource;
use Tobscure\JsonApi\Elements\Collection;

abstract class SerializerAbstract implements SerializerInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array|null
     */
    protected $link;

    /**
     * @var array|null
     */
    protected $include;

    /**
     * @param array|null $include
     * @param array|null $link
     */
    public function __construct(array $include = [], array $link = [])
    {
        $this->include = $include;
        $this->link = $link;
    }

    /**
     * @param $model
     * @return mixed
     */
    abstract protected function getAttributes($model);

    /**
     * @param $model
     * @return mixed
     */
    protected function getId($model)
    {
        return $model->id;
    }

    /**
     * @param $include
     */
    public function setInclude($include)
    {
        $this->include = $include;
    }

    /**
     * @param $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param $data
     * @return Collection|null
     */
    public function collection($data)
    {
        if (empty($data)) {
            return null;
        }

        $resources = [];

        foreach ($data as $record) {
            $resources[] = $this->resource($record);
        }

        return new Collection($this->type, $resources);
    }

    /**
     * @param object|array $data
     * @return Resource|null
     */
    public function resource($data)
    {
        if (empty($data)) {
            return null;
        }

        if (! is_object($data)) {
            return new Resource($this->type, $data);
        }

        $included = $links = [];

        $relationships = [
            'link' => $this->parseRelationshipPaths($this->link),
            'include' => $this->parseRelationshipPaths($this->include)
        ];

        foreach (['link', 'include'] as $type) {
            $include = $type === 'include';

            foreach ($relationships[$type] as $name => $nested) {
                $method = $this->getRelationshipFromMethod($name);

                if ($method) {
                    $element = $method(
                        $data,
                        $include,
                        isset($relationships['include'][$name]) ? $relationships['include'][$name] : [],
                        isset($relationships['link'][$name]) ? $relationships['link'][$name] : []
                    );
                }

                if ($method && $element) {
                    if (! ($element instanceof Relationship)) {
                        $element = new Relationship($element);
                    }
                    if ($include) {
                        $included[$name] = $element;
                    } else {
                        $links[$name] = $element;
                    }
                }
            }
        }

        return new Resource($this->type, $this->getId($data), $this->getAttributes($data), $links, $included);
    }

    protected function getRelationshipFromMethod($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }
    }

    /**
     * Given a flat array of relationship paths like:
     *
     *     ['user', 'user.employer', 'user.employer.country', 'comments']
     *
     * ... create a nested array of relationship paths one-level deep that can
     * be passed on to other serializers:
     *
     *     ['user' => ['employer', 'employer.country'], 'comments' => []]
     *
     * @param array $paths
     * @return array
     */
    protected function parseRelationshipPaths(array $paths)
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
