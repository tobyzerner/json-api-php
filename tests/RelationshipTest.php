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

class RelationshipTest extends AbstractTestCase
{
    public function testJsonSerialize()
    {
        $resource1 = new RelationshipResourceStub();
        $resource2 = new RelationshipResourceStub();

        $relationship = new Relationship($resource1);

        $this->assertEquals([
            'data' => ['type' => 'stub', 'id' => '1']
        ], $relationship->jsonSerialize());

        $relationship = new Relationship([$resource1, $resource2]);

        $this->assertEquals([
            'data' => [
                ['type' => 'stub', 'id' => '1'],
                ['type' => 'stub', 'id' => '1']
            ]
        ], $relationship->jsonSerialize());
    }
}

class RelationshipResourceStub extends AbstractResource
{
    public function getType()
    {
        return 'stub';
    }

    public function getId()
    {
        return '1';
    }
}
