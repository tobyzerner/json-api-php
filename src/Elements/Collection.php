<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi\Elements;

/**
 * This is the collection element class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class Collection extends AbstractElement
{
    protected $resources;

    public function __construct($type, $resources)
    {
        $this->type = $type;
        $this->resources = $resources;
    }

    public function getId()
    {
        $ids = [];

        foreach ($this->resources as $resource) {
            $ids[] = $resource->getId();
        }

        return $ids;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    public function toArray($full = true)
    {
        return array_map(function ($resource) use ($full) {
            return $resource->toArray($full);
        }, $this->resources);
    }
}
