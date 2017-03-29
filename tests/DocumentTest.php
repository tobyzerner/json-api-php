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
    public function testResource()
    {
        $resource = $this->mockResource('a', '1');

        $document = Document::fromData($resource);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => ['type' => 'a', 'id' => '1']
        ]), json_encode($document));
    }

    public function testCollection()
    {
        $resource1 = $this->mockResource('a', '1');
        $resource2 = $this->mockResource('a', '2');

        $document = Document::fromData([$resource1, $resource2]);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                ['type' => 'a', 'id' => '1'],
                ['type' => 'a', 'id' => '2']
            ]
        ]), json_encode($document));
    }

    public function testMergeResource()
    {
        $array1 = ['a' => 1, 'b' => 1];
        $array2 = ['a' => 2, 'c' => 2];

        $resource1 = $this->mockResource('a', '1', $array1, $array1, $array1);
        $resource2 = $this->mockResource('a', '1', $array2, $array2, $array2);

        $document = Document::fromData([$resource1, $resource2]);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                [
                    'type' => 'a',
                    'id' => '1',
                    'attributes' => $merged = array_merge($array1, $array2),
                    'meta' => $merged,
                    'links' => $merged
                ]
            ]
        ]), json_encode($document));
    }

    public function testSparseFieldsets()
    {
        $resource = $this->mockResource('a', '1', ['present' => 1, 'absent' => 1]);

        $resource->expects($this->once())->method('getAttributes')->with($this->equalTo(['present']));

        $document = Document::fromData($resource);
        $document->setFields(['a' => ['present']]);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                'type' => 'a',
                'id' => '1',
                'attributes' => ['present' => 1]
            ]
        ]), json_encode($document));
    }

    public function testIncludeRelationships()
    {
        $resource1 = $this->mockResource('a', '1');
        $resource2 = $this->mockResource('a', '2');
        $resource3 = $this->mockResource('b', '1');

        $relationshipJson = ['data' => 'stub'];

        $relationshipA = $this->getMockBuilder(Relationship::class)->disableOriginalConstructor()->getMock();
        $relationshipA->method('getData')->willReturn($resource2);
        $relationshipA->method('jsonSerialize')->willReturn($relationshipJson);

        $relationshipB = $this->getMockBuilder(Relationship::class)->disableOriginalConstructor()->getMock();
        $relationshipB->method('getData')->willReturn($resource3);
        $relationshipB->method('jsonSerialize')->willReturn($relationshipJson);

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

        $document = Document::fromData($resource1);
        $document->setInclude(['a', 'a.b']);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => [
                'type' => 'a',
                'id' => '1',
                'relationships' => ['a' => $relationshipJson]
            ],
            'included' => [
                [
                    'type' => 'b',
                    'id' => '1'
                ],
                [
                    'type' => 'a',
                    'id' => '2',
                    'relationships' => ['b' => $relationshipJson]
                ]
            ]
        ]), json_encode($document));
    }

    public function testErrors()
    {
        $document = Document::fromErrors(['a']);

        $this->assertJsonStringEqualsJsonString(json_encode(['errors' => ['a']]), json_encode($document));
    }

    public function testLinks()
    {
        $document = Document::fromMeta([]);
        $document->setLink('a', 'b');

        $this->assertJsonStringEqualsJsonString(json_encode(['links' => ['a' => 'b']]), json_encode($document));
    }

    public function testMeta()
    {
        $document = Document::fromMeta(['a' => 'b']);

        $this->assertJsonStringEqualsJsonString(json_encode(['meta' => ['a' => 'b']]), json_encode($document));
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
