<?php namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Elements\Resource;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public function testToArrayIncludesTheResourcesRepresentation()
    {
        $resource = new Resource('post', 1);
        $document = new Document();
        $document->setData($resource);

        $this->assertEquals(['data' => $resource->toArray()], $document->toArray());
    }

    public function testItCanBeSerializedToJson()
    {
        $this->assertEquals('[]', (string) new Document());
    }
}
