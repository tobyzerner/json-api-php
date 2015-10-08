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

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Relationship\ClosureHasManyBuilder;

class AbstractSerializerTest extends AbstractTestCase
{
    public function testGetTypeReturnsTheType()
    {
        $serializer = new PostSerializer1;

        $this->assertEquals('posts', $serializer->getType(null));
    }

    public function testGetAttributesReturnsTheAttributes()
    {
        $serializer = new PostSerializer1;
        $post = (object) ['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar'], $serializer->getAttributes($post));
    }

    public function testGetRelationshipBuilderReturnsBuilderFromMethod()
    {
        $serializer = new PostSerializer1;

        $builder = $serializer->getRelationshipBuilder('comments');

        $this->assertTrue($builder instanceof ClosureHasManyBuilder);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetRelationshipBuilderValidatesBuilder()
    {
        $serializer = new PostSerializer1;

        $serializer->getRelationshipBuilder('invalid');
    }
}

class PostSerializer1 extends AbstractSerializer
{
    protected $type = 'posts';

    public function getAttributes($post, array $fields = null)
    {
        return ['foo' => $post->foo];
    }

    public function comments()
    {
        return new ClosureHasManyBuilder(new self, function ($post) {});
    }

    public function invalid()
    {
        return 'invalid';
    }
}
