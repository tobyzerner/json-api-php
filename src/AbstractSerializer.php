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

use Tobscure\JsonApi\Elements\Collection;
use Tobscure\JsonApi\Elements\Resource;

/**
 * This is the abstract serializer class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
abstract class AbstractSerializer implements SerializerInterface
{
    /**
     * The type.
     *
     * @var string
     */
    protected $type;

    /**
     * Get the type.
     *
     * @param $model
     * @return string
     */
    protected function getType($model)
    {
        return $this->type;
    }

    /**
     * Get the id.
     *
     * @param $model
     * @return string
     */
    protected function getId($model)
    {
        return $model->id;
    }

    /**
     * Get the attributes array.
     *
     * @param $model
     * @return array
     */
    abstract protected function getAttributes($model);

    /**
     * {@inheritdoc}
     */
    public function collection(array $data, array $include = [], array $link = [])
    {
        if (empty($data)) {
            return;
        }

        $resources = [];

        foreach ($data as $record) {
            $resources[] = $this->resource($record, $include, $link);
        }

        return new Collection($this->getType($record), $resources);
    }

    /**
     * {@inheritdoc}
     */
    public function resource($data, array $include = [], array $link = [])
    {
        if (empty($data)) {
            return;
        }

        if (! is_object($data)) {
            return new Resource($this->getType($data), $data);
        }

        $included = $links = [];

        $relationships = [
            'link' => $this->parseRelationshipPaths($link),
            'include' => $this->parseRelationshipPaths($include),
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

        return new Resource($this->getType($data), $this->getId($data), $this->getAttributes($data), $links, $included);
    }

    /**
     * Get relationship from method name.
     *
     * @param string $name
     * @return mixed
     */
    protected function getRelationshipFromMethod($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }
    }

    /**
     * Parse relationship paths.
     *
     * Given a flat array of relationship paths like:
     *
     * ['user', 'user.employer', 'user.employer.country', 'comments']
     *
     * create a nested array of relationship paths one-level deep that can
     * be passed on to other serializers:
     *
     * ['user' => ['employer', 'employer.country'], 'comments' => []]
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
