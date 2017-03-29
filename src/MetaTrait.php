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

trait MetaTrait
{
    private $meta = [];

    /**
     * Set the meta data.
     *
     * @param array $meta
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * Set a piece of meta data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setMetaItem($key, $value)
    {
        $this->meta[$key] = $value;
    }

    /**
     * Remove a piece of meta data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function removeMetaItem($key)
    {
        unset($this->meta[$key]);
    }
}
