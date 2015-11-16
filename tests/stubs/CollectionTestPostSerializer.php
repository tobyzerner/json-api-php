<?php

namespace Tobscure\Tests\JsonApi\stubs;

use Tobscure\JsonApi\AbstractSerializer;

class CollectionTestPostSerializer extends AbstractSerializer
{
    protected $type = 'posts';

    public function getAttributes($post, array $fields = [])
    {
        return [
            'foo' => $post->foo
        ];
    }
}
