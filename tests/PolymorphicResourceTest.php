<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi\Element;

use Tobscure\JsonApi\PolymorphicResource;
use Tobscure\Tests\JsonApi\AbstractTestCase;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Bike;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Car;
use Tobscure\Tests\JsonApi\Stubs\Serializers\VehicleSerializerRegistry;

class PolymorphicResourceTest extends AbstractTestCase
{
    public function testPolymorphicResourceType()
    {
        $car = new Car(123);
        $bike = new Bike(234);
        $serializerRegistry = new VehicleSerializerRegistry();

        $resource1 = new PolymorphicResource($car, $serializerRegistry);
        $resource2 = new PolymorphicResource($bike, $serializerRegistry);

        $this->assertSame('cars', $resource1->getType());
        $this->assertSame('bikes', $resource2->getType());
    }
}
