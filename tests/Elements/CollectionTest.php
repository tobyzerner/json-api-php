<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi\Elements;

use Tobscure\JsonApi\Elements\Collection;
use Tobscure\JsonApi\Elements\Resource;
use Tobscure\Tests\JsonApi\AbstractTestCase;

/**
 * This is the collection test class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class CollectionTest extends AbstractTestCase
{
    public function testToArrayReturnsArrayOfResources()
    {
        $post1 = new Resource('post', 1);
        $post2 = new Resource('post', 2);
        $collection = new Collection('post', [$post1, $post2]);

        $this->assertEquals([$post1->toArray(), $post2->toArray()], $collection->toArray());
    }

    public function testGetIdReturnsArrayOfResourceIds()
    {
        $post1 = new Resource('post', 1);
        $post2 = new Resource('post', 2);
        $collection = new Collection('post', [$post1, $post2]);

        $this->assertEquals([1, 2], $collection->getId());
    }
}
