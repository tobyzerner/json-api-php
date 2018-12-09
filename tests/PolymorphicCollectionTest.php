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

use Tobscure\JsonApi\PolymorphicCollection;
use Tobscure\JsonApi\PolymorphicResource;
use Tobscure\Tests\JsonApi\AbstractTestCase;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Bike;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Car;
use Tobscure\Tests\JsonApi\Stubs\Serializables\Garage;
use Tobscure\Tests\JsonApi\Stubs\Serializers\VehicleSerializerRegistry;

class PolymorphicCollectionTest extends AbstractTestCase
{
    public function testToArrayReturnsArrayOfResources()
    {
        $car = new Car(1);
        $bike = new Bike(2);
        $garage = new Garage(4, [$car, $bike]);
        $serializerRegistry = new VehicleSerializerRegistry();

        $collection = new PolymorphicCollection($garage->vehicles, $serializerRegistry);

        $resource1 = new PolymorphicResource($car, $serializerRegistry);
        $resource2 = new PolymorphicResource($bike, $serializerRegistry);
        $this->assertSame([$resource1->toArray(), $resource2->toArray()], $collection->toArray());
    }

    public function testToArrayReturnsArrayOfPolymorphicResources()
    {
        $car = new Car(1);
        $bike = new Bike(2);
        $garage = new Garage(4, [$car, $bike]);
        $serializerRegistry = new VehicleSerializerRegistry();

        $collection = new PolymorphicCollection($garage->vehicles, $serializerRegistry);

        $resources = $collection->getResources();
        $this->assertSame('cars', $resources[0]->getType());
        $this->assertSame('bikes', $resources[1]->getType());
    }

    public function testToIdentifierReturnsArrayOfResourceIdentifiers()
    {
        $car = new Car(1);
        $bike = new Bike(2);
        $garage = new Garage(4, [$car, $bike]);
        $serializerRegistry = new VehicleSerializerRegistry();

        $collection = new PolymorphicCollection($garage->vehicles, $serializerRegistry);

        $resource1 = new PolymorphicResource($car, $serializerRegistry);
        $resource2 = new PolymorphicResource($bike, $serializerRegistry);
        $this->assertSame([$resource1->toIdentifier(), $resource2->toIdentifier()], $collection->toIdentifier());
    }
}
