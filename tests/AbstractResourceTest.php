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

use Tobscure\JsonApi\AbstractResource;
use Tobscure\JsonApi\Relationship;

class AbstractResourceTest extends AbstractTestCase
{
    public function testGetTypeReturnsTheType()
    {
        $resource = new AbstractResourceStub;

        $this->assertEquals('stub', $resource->getType());
    }

    public function testGetAttributesReturnsEmptyArray()
    {
        $resource = new AbstractResourceStub;

        $this->assertEquals([], $resource->getAttributes());
    }

    public function testGetRelationshipReturnsRelationshipFromMethod()
    {
        $resource = new AbstractResourceStub;

        $relationship = $resource->getRelationship('valid');
        $this->assertTrue($relationship instanceof Relationship);

        $relationship = $resource->getRelationship('va-lid');
        $this->assertTrue($relationship instanceof Relationship);

        $relationship = $resource->getRelationship('va_lid');
        $this->assertTrue($relationship instanceof Relationship);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetRelationshipValidatesRelationship()
    {
        $resource = new AbstractResourceStub;

        $resource->getRelationship('invalid');
    }
}

class AbstractResourceStub extends AbstractResource
{
    protected $type = 'stub';

    public function getId()
    {
    }

    public function valid()
    {
        return new Relationship();
    }

    public function invalid()
    {
        return 'invalid';
    }
}
