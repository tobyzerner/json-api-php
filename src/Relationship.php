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

class Relationship implements JsonSerializable
{
    use LinksTrait, SelfLinkTrait, RelatedLinkTrait, PaginationLinksTrait, MetaTrait;

    private $data;

    private function __construct()
    {
    }

    public static function fromMeta($meta)
    {
        $r = new self;
        $r->replaceMeta($meta);

        return $r;
    }

    public static function fromSelfLink($link)
    {
        $r = new self;
        $r->setSelfLink($link);

        return $r;
    }

    public static function fromRelatedLink($link)
    {
        $r = new self;
        $r->setRelatedLink($link);

        return $r;
    }

    public static function fromData($data)
    {
        $r = new self;
        $r->data = $data;

        return $r;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        $relationship = [];

        if ($this->data) {
            $relationship['data'] = is_array($this->data)
                ? array_map([$this, 'buildIdentifier'], $this->data)
                : $this->buildIdentifier($this->data);
        }

        return array_filter($relationship + [
            'meta' => $this->meta,
            'links' => $this->links
        ]);
    }

    private function buildIdentifier(ResourceInterface $resource)
    {
        $id = new ResourceIdentifier($resource->getType(), $resource->getId());
        $id->setMeta($resource->getMeta());

        return $id;
    }
}
