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

        $this->assertProduceSameJson(
            [
                'data' => ['type' => 'a', 'id' => '1'],
            ],
            $document
        );
    }

    public function testCollection()
    {
        $resource1 = $this->mockResource('a', '1');
        $resource2 = $this->mockResource('a', '2');

        $document = Document::fromData([$resource1, $resource2]);

        $this->assertProduceSameJson(
            [
                'data' => [
                    ['type' => 'a', 'id' => '1'],
                    ['type' => 'a', 'id' => '2'],
                ],
            ],
            $document
        );
    }

    public function testMergeResource()
    {
        $array1 = ['a' => 1, 'b' => 1];
        $array2 = ['a' => 2, 'c' => 2];

        $resource1 = $this->mockResource('a', '1', $array1, $array1, $array1);
        $resource2 = $this->mockResource('a', '1', $array2, $array2, $array2);

        $document = Document::fromData([$resource1, $resource2]);

        $this->assertProduceSameJson(
            [
                'data' => [
                    [
                        'type' => 'a',
                        'id' => '1',
                        'attributes' => $merged = array_merge($array1, $array2),
                        'meta' => $merged,
                        'links' => $merged,
                    ],
                ],
            ],
            $document
        );
    }

    public function testSparseFieldsets()
    {
        $resource = $this->mockResource('a', '1', ['present' => 1, 'absent' => 1]);

        $resource->expects($this->once())->method('getAttributes')->with($this->equalTo(['present']));

        $document = Document::fromData($resource);
        $document->setFields(['a' => ['present']]);

        $this->assertProduceSameJson(
            [
                'data' => [
                    'type' => 'a',
                    'id' => '1',
                    'attributes' => ['present' => 1],
                ],
            ],
            $document
        );
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

        $this->assertProduceSameJson(
            [
                'data' => [
                    'type' => 'a',
                    'id' => '1',
                    'relationships' => ['a' => $relationshipJson],
                ],
                'included' => [
                    [
                        'type' => 'b',
                        'id' => '1',
                    ],
                    [
                        'type' => 'a',
                        'id' => '2',
                        'relationships' => ['b' => $relationshipJson],
                    ],
                ],
            ],
            $document
        );
    }

    public function testErrors()
    {
        $document = Document::fromErrors(['a']);

        $this->assertProduceSameJson(['errors' => ['a']], $document);
    }

    public function testLinks()
    {
        $document = Document::fromMeta([]);
        $document->setLink('a', 'b');

        $this->assertProduceSameJson(['links' => ['a' => 'b']], $document);
    }

    public function testMeta()
    {
        $document = Document::fromMeta(['a' => 'b']);

        $this->assertProduceSameJson(['meta' => ['a' => 'b']], $document);
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
