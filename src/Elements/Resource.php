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
    /**
     * The resource id.
     *
     * @var int
     */
    protected $id;

    /**
     * The attributes array.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The links array.
     *
     * @var array
     */
    protected $links = [];

    /**
     * The included array.
     *
     * @var array
     */
    protected $included = [];

    /**
     * Create a new resource instance.
     *
     * @param string $type
     * @param int $id
     * @param array $attributes
     * @param array $links
     * @param array $included
     */
    public function __construct($type, $id, $attributes = [], $links = [], $included = [])
    {
        $this->type = $type;
        $this->attributes = $attributes;
        $this->links = $links;
        $this->included = $included;

        $this->setId($id);
    }

    /**
     * Get the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id.
     *
     * @param string|int $id
     */
    public function setId($id)
    {
        $this->id = (string) $id;
    }

    /**
     * Get the attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes.
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the links.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set the links.
     *
     * @param array $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * Add a link.
     *
     * @param string $name
     * @param \Tobscure\JsonApi\Relationship $relationship
     */
    public function addLink($name, $relationship)
    {
        $this->links[$name] = $relationship;
    }

    /**
     * Get the included array.
     *
     * @return array
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * Set the included array.
     *
     * @param array $included
     */
    public function setIncluded($included)
    {
        $this->included = $included;
    }

    /**
     * Add an include.
     *
     * @param string $name
     * @param \Tobscure\JsonApi\Relationship $relationship
     */
    public function addIncluded($name, $relationship)
    {
        $this->included[$name] = $relationship;
    }

    /**
     * Get the resources array.
     *
     * @return array
     */
    public function getResources()
    {
        return [$this];
    }

    /**
     * Merge resources.
     *
     * @param \Tobscure\JsonApi\Elements\Resource $resource
     */
    public function merge(Resource $resource)
    {
        $this->attributes = array_merge($this->attributes, $resource->attributes);
        $this->links = array_merge($this->links, $resource->links);
        $this->included = array_merge($this->included, $resource->included);
    }

    /**
     * Map to an array.
     *
     * @param bool $full
     * @return array
     */
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
