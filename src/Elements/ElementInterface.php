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
 * This is the element interface.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
interface ElementInterface
{
    /**
     * Get an id or an array of ids.
     *
     * @return string|array
     */
    public function getId();

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get the resources array.
     *
     * @return array
     */
    public function getResources();

    /**
     * Map resources to an array.
     *
     * @return array
     */
    public function toArray();
}
