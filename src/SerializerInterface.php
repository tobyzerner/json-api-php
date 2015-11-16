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

interface SerializerInterface
{
    /**
     * Get the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the id.
     *
     * @param mixed $model
     * @return string
     */
    public function getId($model);

    /**
     * Get the attributes array.
     *
     * @param mixed $model
     * @param array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = []);

    /**
     * Get a relationship.
     *
     * @param mixed $model
     * @param string $name
     * @return \Tobscure\JsonApi\Relationship|null
     */
    public function getRelationship($model, $name);
}
