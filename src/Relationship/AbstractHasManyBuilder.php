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

use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractHasManyBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function createElement($data, SerializerInterface $serializer)
    {
        return new Collection($data, $serializer);
    }
}
