<?php

use Tobscure\JsonApi\Elements\Collection;
use Tobscure\JsonApi\Elements\Resource;

class CollectionTest extends \PHPUnit_Framework_TestCase
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
