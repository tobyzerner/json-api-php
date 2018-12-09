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

use Tobscure\JsonApi\Resource as JsonApiResource;

class PolymorphicResource extends JsonApiResource
{
    /**
     * @param mixed $data
     * @param \Tobscure\JsonApi\SerializerRegistryInterface $serializers
     */
    public function __construct($data, SerializerRegistryInterface $serializers)
    {
        parent::__construct($data, $serializers->getFromSerializable($data));
    }
}
