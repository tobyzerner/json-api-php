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

class Link implements JsonSerializable
{
    use MetaTrait;

    protected $href;

    public function __construct($href, $meta = null)
    {
        $this->href = $href;
        $this->meta = $meta;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function setHref($href)
    {
        $this->href = $href;
    }

    public function jsonSerialize()
    {
        return $this->meta ? ['href' => $this->href, 'meta' => $this->meta] : $this->href;
    }
}
