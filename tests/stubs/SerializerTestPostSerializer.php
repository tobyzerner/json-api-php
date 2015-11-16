<?php

namespace Tobscure\Tests\JsonApi\stubs;

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;

class SerializerTestPostSerializer extends AbstractSerializer
{
    protected $type = 'posts';

    public function getAttributes($post, array $fields = [])
    {
        return [
            'foo' => $post->foo
        ];
    }

    public function comments($post)
    {
        $element = new Collection([], new SerializerTestCommentSerializer());

        return new Relationship($element);
    }

    public function invalid($post)
    {
        return 'invalid';
    }
}
