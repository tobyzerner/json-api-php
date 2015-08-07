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

use Tobscure\JsonApi\Elements\ElementInterface;

/**
 * This is the relationship class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class Relationship
{
    protected $data;

    protected $self;

    protected $related;

    protected $meta;

    /**
     * @param ElementInterface $data
     */
    public function __construct(ElementInterface $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function setSelf($self)
    {
        $this->self = $self;

        return $this;
    }

    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    public function toArray()
    {
        $link = [];

        if (!empty($this->data)) {
            $link['data'] = $this->data->toArray(false);
        }

        if (!empty($this->self)) {
            $link['self'] = $this->self;
        }

        if (!empty($this->related)) {
            $link['related'] = $this->related;
        }

        if (!empty($this->meta)) {
            $link['meta'] = $this->meta;
        }

        return $link;
    }
}
