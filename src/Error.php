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

class Error implements JsonSerializable
{
    use LinksTrait, MetaTrait;

    private $id;
    private $status;
    private $code;
    private $title;
    private $detail;
    private $source;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAboutLink($link)
    {
        $this->links['about'] = $link;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    public function setSourcePointer($pointer)
    {
        $this->source['pointer'] = $pointer;
    }

    public function setSourceParameter($parameter)
    {
        $this->source['parameter'] = $parameter;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'id' => $this->id,
                'links' => $this->links,
                'status' => $this->status,
                'code' => $this->code,
                'title' => $this->title,
                'detail' => $this->detail,
                'source' => $this->source,
                'meta' => $this->meta
            ]
        );
    }
}
