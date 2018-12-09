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

use RuntimeException;

class AbstractSerializerRegistry implements SerializerRegistryInterface
{
    /**
     * @var array
     */
    protected $serializers = [];

    /**
     * Instantiate serializer from the serializable object.
     *
     * @param object $serializable
     * @return \Tobscure\JsonApi\SerializerInterface
     */
    public function getFromSerializable($serializable)
    {
        $class = get_class($serializable);

        if (!isset($this->serializers[$class])) {
            throw new RuntimeException("Serializer with name `{$class}` is not exists");
        }

        return new $this->serializers[$class]();
    }
}
