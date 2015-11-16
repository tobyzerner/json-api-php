<?php

namespace Tobscure\Tests\JsonApi\stubs;

use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;

class ResourceTestPostSerializer extends AbstractSerializer
{
    protected $type = 'posts';

    public function getAttributes($post, array $fields = [])
    {
        $attributes = [];

        if (isset($post->foo)) {
            $attributes['foo'] = $post->foo;
        }
        if (isset($post->baz)) {
            $attributes['baz'] = $post->baz;
        }

        return $attributes;
    }

    public function comments($post)
    {
        return new Relationship(
            new Collection($post->comments, new ResourceTestCommentSerializer())
        );
    }
}
