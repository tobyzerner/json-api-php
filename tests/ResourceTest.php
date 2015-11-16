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

use Tobscure\JsonApi\Resource;
use Tobscure\Tests\JsonApi\AbstractTestCase;
use Tobscure\Tests\JsonApi\stubs\ResourceTestPostSerializer;

class ResourceTest extends AbstractTestCase
{
    public function testToArrayReturnsArray()
    {
        $data = (object) ['id' => '123', 'foo' => 'bar', 'baz' => 'qux'];

        $resource = new Resource($data, new ResourceTestPostSerializer());

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar',
                'baz' => 'qux'
            ]
        ], $resource->toArray());
    }

    public function testToIdentifierReturnsResourceIdentifier()
    {
        $data = (object) ['id' => '123', 'foo' => 'bar'];

        $resource = new Resource($data, new ResourceTestPostSerializer());

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123'
        ], $resource->toIdentifier());

        $resource->addMeta('foo', 'bar');

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'meta' => ['foo' => 'bar']
        ], $resource->toIdentifier());
    }

    public function testGetIdReturnsString()
    {
        $data = (object) ['id' => 123];

        $resource = new Resource($data, new ResourceTestPostSerializer());

        $this->assertSame('123', $resource->getId());
    }

    public function testGetIdWorksWithScalarData()
    {
        $resource = new Resource(123, new ResourceTestPostSerializer());

        $this->assertSame('123', $resource->getId());
    }

    public function testCanFilterFields()
    {
        $data = (object) ['id' => '123', 'foo' => 'bar', 'baz' => 'qux'];

        $resource = new Resource($data, new ResourceTestPostSerializer());

        $resource->fields(['posts' => ['baz']]);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'baz' => 'qux'
            ]
        ], $resource->toArray());
    }

    public function testCanMergeWithAnotherResource()
    {
        $post1 = (object) ['id' => '123', 'foo' => 'bar', 'comments' => [1]];
        $post2 = (object) ['id' => '123', 'baz' => 'qux', 'comments' => [1, 2]];

        $resource1 = new Resource($post1, new ResourceTestPostSerializer());
        $resource2 = new Resource($post2, new ResourceTestPostSerializer());

        $resource1->with(['comments']);
        $resource2->with(['comments']);

        $resource1->merge($resource2);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'baz' => 'qux',
                'foo' => 'bar'
            ],
            'relationships' => [
                'comments' => [
                    'data' => [
                        ['type' => 'comments', 'id' => '1'],
                        ['type' => 'comments', 'id' => '2']
                    ]
                ]
            ]
        ], $resource1->toArray());
    }
}
