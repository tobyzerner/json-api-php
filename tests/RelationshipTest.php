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

        $relationship = Relationship::fromData($resource1);

        $this->assertProduceSameJson(
            [
                'data' => ['type' => 'stub', 'id' => '1'],
            ],
            $relationship
        );

        $relationship = Relationship::fromData([$resource1, $resource2]);

        $this->assertProduceSameJson(
            [
                'data' => [
                    ['type' => 'stub', 'id' => '1'],
                    ['type' => 'stub', 'id' => '1'],
                ],
            ],
            $relationship
        );
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
