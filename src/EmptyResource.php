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

class EmptyResource implements ElementInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return [$this];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->toIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function with($relationships)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fields($fields)
    {
        return $this;
    }
}
