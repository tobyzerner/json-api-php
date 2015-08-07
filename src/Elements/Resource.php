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
 * This is the resource element class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class Resource extends AbstractElement
{
    protected $id;

    protected $attributes = [];

    protected $links = [];

    protected $included = [];

    public function __construct($type, $id, $attributes = [], $links = [], $included = [])
    {
        $this->type = $type;
        $this->attributes = $attributes;
        $this->links = $links;
        $this->included = $included;

        $this->setId($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (string) $id;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setLinks($links)
    {
        $this->links = $links;
    }

    public function addLink($name, $relationship)
    {
        $this->links[$name] = $relationship;
    }

    public function getIncluded()
    {
        return $this->included;
    }

    public function setIncluded($included)
    {
        $this->included = $included;
    }

    public function addIncluded($name, $relationship)
    {
        $this->included[$name] = $relationship;
    }

    public function getResources()
    {
        return [$this];
    }

    public function merge(Resource $resource)
    {
        $this->attributes = array_merge($this->attributes, $resource->attributes);
        $this->links = array_merge($this->links, $resource->links);
        $this->included = array_merge($this->included, $resource->included);
    }

    public function toArray($full = true)
    {
        $array = ['type' => $this->type, 'id' => $this->id];

        if ($full) {
            $array['attributes'] = (array) $this->attributes;

            if ($this->links || $this->included) {
                $array['relationships'] = [];

                foreach ($this->included as $name => $relationship) {
                    $array['relationships'][$name] = $relationship->toArray();
                }
                foreach ($this->links as $name => $relationship) {
                    $array['relationships'][$name] = $relationship->toArray();
                }
            }
        }

        return $array;
    }
}
