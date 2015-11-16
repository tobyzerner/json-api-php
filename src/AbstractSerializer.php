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

use LogicException;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the id.
     *
     * @param mixed $model
     * @return string
     */
    public function getId($model)
    {
        return $model->id;
    }

    /**
     * Get the attributes array.
     *
     * @param mixed $model
     * @param array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = [])
    {
        return [];
    }

    /**
     * Get a relationship.
     *
     * @param mixed $model
     * @param string $name
     * @throws LogicException
     * @return \Tobscure\JsonApi\Relationship|null
     */
    public function getRelationship($model, $name)
    {
        if (method_exists($this, $name)) {
            $relationship = $this->$name($model);

            if ($relationship !== null && !($relationship instanceof Relationship)) {
                throw new LogicException('Relationship method must return null or an instance of ' . Relationship::class);
            }

            return $relationship;
        }
    }
}
