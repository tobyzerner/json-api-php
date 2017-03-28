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
    /**
     * The meta data.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Get the meta data.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set the meta data.
     *
     * @param array $meta
     */
    public function replaceMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * Set a piece of meta data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setMeta($key, $value)
    {
        $this->meta[$key] = $value;
    }

    /**
     * Remove a piece of meta data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function removeMeta($key)
    {
        unset($this->meta[$key]);
    }
}
