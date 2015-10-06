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

use Tobscure\JsonApi\Element\ElementInterface;

class Relationship
{
    /**
     * The data object.
     *
     * @var ElementInterface
     */
    protected $data;

    /**
     * The self variable.
     *
     * @var array
     */
    protected $self;

    /**
     * The related array.
     *
     * @var array
     */
    protected $related;

    /**
     * The meta data array.
     *
     * @var array
     */
    protected $meta;

    /**
     * Create a new relationship.
     *
     * @param ElementInterface $data
     */
    public function __construct(ElementInterface $data)
    {
        $this->data = $data;
    }

    /**
     * Get the data object.
     *
     * @return ElementInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data object.
     *
     * @param ElementInterface $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add meta data.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addMeta($key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }

    /**
     * Set meta data.
     *
     * @param array $meta
     * @return $this
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Set self.
     *
     * @param array $self
     * @return $this
     */
    public function setSelf($self)
    {
        $this->self = $self;

        return $this;
    }

    /**
     * Set related data.
     *
     * @param array $related
     * @return $this
     */
    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    /**
     * Map everything to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        if (! empty($this->data)) {
            $array['data'] = $this->data->toIdentifier();
        }

        if (! empty($this->self)) {
            $array['self'] = $this->self;
        }

        if (! empty($this->related)) {
            $array['related'] = $this->related;
        }

        if (! empty($this->meta)) {
            $array['meta'] = $this->meta;
        }

        return $array;
    }
}
