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

use InvalidArgumentException;
use LogicException;
use Tobscure\JsonApi\Relationship\BuilderInterface;

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
    public function getRelationshipBuilder($name)
    {
        if (! method_exists($this, $name)) {
            throw new InvalidArgumentException('No method found for ['.$name.']');
        }

        $builder = $this->$name();

        if (! ($builder instanceof BuilderInterface)) {
            throw new LogicException('Relationship method must return an instance of '
                .BuilderInterface::class);
        }

        return $builder;
    }
}
