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

use Tobscure\JsonApi\AbstractSerializerRegistry;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Bike;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Car;

class VehicleSerializerRegistry extends AbstractSerializerRegistry
{
    protected $serializers = [
        Car::class => CarSerializer::class,
        Bike::class => BikeSerializer::class,
    ];
}
