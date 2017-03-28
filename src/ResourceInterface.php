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

interface ResourceInterface
{
    /**
     * Get the resource type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the resource ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the resource attributes.
     *
     * @param string[]|null $fields
     *
     * @return array|null
     */
    public function getAttributes(array $fields = null);

    /**
     * Get the resource links.
     *
     * @return array|null
     */
    public function getLinks();

    /**
     * Get the resource meta information.
     *
     * @return array|null
     */
    public function getMeta();

    /**
     * Get a relationship.
     *
     * @param string $name
     *
     * @return \Tobscure\JsonApi\Relationship|null
     */
    public function getRelationship($name);
}
