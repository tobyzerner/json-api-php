<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi\Stubs\Serializers;

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\PolymorphicCollection;
use Tobscure\JsonApi\Relationship;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Garage;

class GarageSerializer extends AbstractSerializer
{
    protected $type = 'garages';

    public function getAttributes($garage, array $fields = null)
    {
        return [];
    }

    public function vehicles(Garage $garage)
    {
        $element = new PolymorphicCollection(
            $garage->vehicles,
            new VehicleSerializerRegistry()
        );

        return new Relationship($element);
    }
}
