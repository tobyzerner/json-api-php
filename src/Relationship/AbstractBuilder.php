<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi\Relationship;

use Closure;
use InvalidArgumentException;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var string|Closure|SerializerInterface
     */
    protected $serializer;

    /**
     * @var Closure[]
     */
    protected $configureCallbacks = [];

    /**
     * @param string|Closure|SerializerInterface $serializer
     */
    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function build($model)
    {
        $data = $this->getRelationshipData($model);

        $serializer = $this->resolveSerializer($this->serializer, $model, $data);

        $element = $this->createElement($data, $serializer);

        $relationship = new Relationship($element);

        $this->runConfigureCallbacks($relationship);

        return $relationship;
    }

    /**
     * @param mixed $data
     * @param SerializerInterface $serializer
     * @return ElementInterface
     */
    abstract protected function createElement($data, SerializerInterface $serializer);

    /**
     * @param mixed $model
     * @param bool $included
     * @return mixed
     */
    abstract protected function getRelationshipData($model);

    /**
     * @param mixed $serializer
     * @param mixed $model
     * @param mixed $data
     * @return SerializerInterface
     */
    protected function resolveSerializer($serializer, $model, $data)
    {
        if ($serializer instanceof Closure) {
            $serializer = call_user_func($serializer, $model, $data);
        }

        if (is_string($serializer)) {
            $serializer = $this->resolveSerializerClass($serializer);
        }

        if (! ($serializer instanceof SerializerInterface)) {
            throw new InvalidArgumentException('Serializer must be an instance of '
                . SerializerInterface::class);
        }

        return $serializer;
    }

    /**
     * @param $class
     * @return object
     */
    protected function resolveSerializerClass($class)
    {
        return new $class;
    }

    /**
     * @param Closure $callback
     */
    public function configure(Closure $callback)
    {
        $this->configureCallbacks[] = $callback;
    }

    /**
     * @param Relationship $relationship
     */
    protected function runConfigureCallbacks(Relationship $relationship)
    {
        foreach ($this->configureCallbacks as $callback) {
            $callback($relationship);
        }
    }
}
