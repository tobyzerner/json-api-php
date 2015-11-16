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
use Tobscure\JsonApi\Resource;
use Tobscure\Tests\JsonApi\stubs\DocumentTestPostSerializer;

/**
 * This is the document test class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class DocumentTest extends AbstractTestCase
{
    public function testToArrayIncludesTheResourcesRepresentation()
    {
        $post = (object) [
            'id' => 1,
            'foo' => 'bar'
        ];

        $resource = new Resource($post, new DocumentTestPostSerializer());
        $document = new Document($resource);

        $this->assertEquals(['data' => $resource->toArray()], $document->toArray());
    }

    public function testItCanBeSerializedToJson()
    {
        $this->assertEquals('[]', (string) new Document());
    }
}
