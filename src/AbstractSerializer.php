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
     * {@inheritdoc}
     */
    public function getType($model)
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getId($model)
    {
        return $model->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException
     */
    public function getRelationship($model, $name)
    {
        if (stripos($name, '-')) {
            $name = $this->replaceDashWithUppercase($name);
        }

        if (method_exists($this, $name)) {
            $relationship = $this->$name($model);

            if ($relationship !== null && ! ($relationship instanceof Relationship)) {
                throw new LogicException('Relationship method must return null or an instance of '
                    .Relationship::class);
            }

            return $relationship;
        }
    }

    /**
     * Removes all dashes from relationsship and uppercases the following letter.
     * @example If relationship parent-page is needed the the function name will be changed to parentPage
     * 
     * @param string Name of the function
     * 
     * @return string New function name
     */
    private function replaceDashWithUppercase($name)
    {
        return lcfirst(implode('', array_map('ucfirst', explode('-', $name))));
    }
}
