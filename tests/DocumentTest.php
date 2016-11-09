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
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;

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

        $resource = new Resource($post, new PostSerializer2);

        $document = new Document($resource);

        $this->assertEquals(['data' => $resource->toArray()], $document->toArray());
    }

    public function testItCanBeSerializedToJson()
    {
        $this->assertEquals('[]', (string) new Document());
    }

    public function testToArrayIncludesIncludedResources()
    {
        $comment = (object) ['id' => 1, 'foo' => 'bar'];
        $post = (object) ['id' => 1, 'foo' => 'bar', 'comments' => [$comment]];

        $resource = new Resource($post, new PostSerializer2);
        $includedResource = new Resource($comment, new CommentSerializer2);

        $document = new Document($resource->with('comments'));

        $this->assertEquals([
            'data' => $resource->toArray(),
            'included' => [
                $includedResource->toArray()
            ]
        ], $document->toArray());
    }
}

class PostSerializer2 extends AbstractSerializer
{
    protected $type = 'posts';

    public function getAttributes($post, array $fields = null)
    {
        return ['foo' => $post->foo];
    }

    public function comments($post)
    {
        return new Relationship(new Collection($post->comments, new CommentSerializer2));
    }
}

class CommentSerializer2 extends AbstractSerializer
{
    protected $type = 'comments';

    public function getAttributes($comment, array $fields = null)
    {
        return ['foo' => $comment->foo];
    }
}
