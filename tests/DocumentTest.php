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

use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\ResourceInterface;

class DocumentTest extends AbstractTestCase
{
    public function testItCanBeSerializedToJson()
    {
        $this->assertEquals('[]', (string) new Document());
    }

    public function testResource()
    {
        $resource = $this->mockResource('a', '1');

        $document = new Document($resource);

        $this->assertEquals([
            'data' => ['type' => 'a', 'id' => '1']
        ], $document->toArray());
    }

    public function testCollection()
    {
        $resource1 = $this->mockResource('a', '1');
        $resource2 = $this->mockResource('a', '2');

        $document = new Document([$resource1, $resource2]);

        $this->assertEquals([
            'data' => [
                ['type' => 'a', 'id' => '1'],
                ['type' => 'a', 'id' => '2']
            ]
        ], $document->toArray());
    }

    public function testMergeResource()
    {
        $array1 = ['a' => 1, 'b' => 1];
        $array2 = ['a' => 2, 'c' => 2];

        $resource1 = $this->mockResource('a', '1', $array1, $array1, $array1);
        $resource2 = $this->mockResource('a', '1', $array2, $array2, $array2);

        $document = new Document([$resource1, $resource2]);

        $this->assertEquals([
            'data' => [
                [
                    'type' => 'a',
                    'id' => '1',
                    'attributes' => $merged = array_merge($array1, $array2),
                    'meta' => $merged,
                    'links' => $merged
                ]
            ]
        ], $document->toArray());
    }

    public function testSparseFieldsets()
    {
        $resource = $this->mockResource('a', '1', ['present' => 1, 'absent' => 1]);

        $resource->expects($this->once())->method('getAttributes')->with($this->equalTo(['present']));

        $document = new Document($resource);
        $document->setFields(['a' => ['present']]);

        $this->assertEquals([
            'data' => [
                'type' => 'a',
                'id' => '1',
                'attributes' => ['present' => 1]
            ]
        ], $document->toArray());
    }

    public function testIncludeRelationships()
    {
        $resource1 = $this->mockResource('a', '1');
        $resource2 = $this->mockResource('a', '2');
        $resource3 = $this->mockResource('b', '1');

        $relationshipArray = ['data' => 'stub'];

        $relationshipA = $this->getMock(Relationship::class);
        $relationshipA->method('getData')->willReturn($resource2);
        $relationshipA->method('toArray')->willReturn($relationshipArray);

        $relationshipB = $this->getMock(Relationship::class);
        $relationshipB->method('getData')->willReturn($resource3);
        $relationshipB->method('toArray')->willReturn($relationshipArray);

        $resource1
            ->expects($this->once())
            ->method('getRelationship')
            ->with($this->equalTo('a'))
            ->willReturn($relationshipA);

        $resource2
            ->expects($this->once())
            ->method('getRelationship')
            ->with($this->equalTo('b'))
            ->willReturn($relationshipB);

        $document = new Document($resource1);
        $document->setInclude(['a', 'a.b']);

        $this->assertEquals([
            'data' => [
                'type' => 'a',
                'id' => '1',
                'relationships' => ['a' => $relationshipArray]
            ],
            'included' => [
                [
                    'type' => 'b',
                    'id' => '1'
                ],
                [
                    'type' => 'a',
                    'id' => '2',
                    'relationships' => ['b' => $relationshipArray]
                ]
            ]
        ], $document->toArray());
    }

    public function testErrors()
    {
        $document = new Document();
        $document->setErrors(['a']);

        $this->assertEquals(['errors' => ['a']], $document->toArray());
    }

    public function testJsonapi()
    {
        $document = new Document();
        $document->setJsonapi(['a']);

        $this->assertEquals(['jsonapi' => ['a']], $document->toArray());
    }

    public function testLinks()
    {
        $document = new Document();
        $document->setLinks(['a']);

        $this->assertEquals(['links' => ['a']], $document->toArray());
    }

    public function testMeta()
    {
        $document = new Document();
        $document->setMeta(['a']);

        $this->assertEquals(['meta' => ['a']], $document->toArray());
    }

    private function mockResource($type, $id, $attributes = [], $meta = [], $links = [])
    {
        $mock = $this->getMock(ResourceInterface::class);

        $mock->method('getType')->willReturn($type);
        $mock->method('getId')->willReturn($id);
        $mock->method('getAttributes')->willReturn($attributes);
        $mock->method('getMeta')->willReturn($meta);
        $mock->method('getLinks')->willReturn($links);

        return $mock;
    }
}
