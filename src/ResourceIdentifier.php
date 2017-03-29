<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi;

use JsonSerializable;
use Tobscure\JsonApi\MetaTrait;

class ResourceIdentifier implements JsonSerializable
{
    use MetaTrait;

    private $type;
    private $id;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'type' => $this->type,
                'id' => $this->id,
                'meta' => $this->meta
            ]
        );
    }
}
