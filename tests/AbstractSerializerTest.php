<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi;

use Tobscure\JsonApi\Relationship;
use Tobscure\Tests\JsonApi\stubs\SerializerTestPostSerializer;

class AbstractSerializerTest extends AbstractTestCase
{
    public function testGetTypeReturnsTheType()
    {
        $serializer = new SerializerTestPostSerializer();

        $this->assertEquals('posts', $serializer->getType());
    }

    public function testGetAttributesReturnsTheAttributes()
    {
        $serializer = new SerializerTestPostSerializer();
        $post = (object) ['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar'], $serializer->getAttributes($post));
    }

    public function testGetRelationshipReturnsRelationshipFromMethod()
    {
        $serializer = new SerializerTestPostSerializer();

        $relationship = $serializer->getRelationship(null, 'comments');

        $this->assertTrue($relationship instanceof Relationship);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetRelationshipValidatesRelationship()
    {
        $serializer = new SerializerTestPostSerializer();

        $serializer->getRelationship(null, 'invalid');
    }
}
