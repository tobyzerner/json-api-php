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
    /**
     * The resources array.
     *
     * @var array
     */
    protected $resources;

    /**
     * Create a new collection instance.
     *
     * @param string $type
     * @param array $resources
     */
    public function __construct($type, $resources)
    {
        $this->type = $type;
        $this->resources = $resources;
    }

    /**
     * Get an array of ids.
     *
     * @return array
     */
    public function getId()
    {
        $ids = array();

        foreach ($this->resources as $resource) {
            $ids[] = $resource->getId();
        }

        return $ids;
    }

    /**
     * Get the resources array.
     *
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set the resources array.
     *
     * @param array $resources
     *
     * @return void
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * Map resources to an array.
     *
     * @param bool $full
     *
     * @return array
     */
    public function toArray($full = true)
    {
        return array_map(function ($resource) use ($full) {
            return $resource->toArray($full);
        }, $this->resources);
    }
}
