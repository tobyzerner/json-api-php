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

trait SelfLinkTrait
{
    abstract public function setLink($key, $value);
    abstract public function removeLink($key);

    /**
     * Set the self link.
     *
     * @param string|Link $value
     */
    public function setSelfLink($value)
    {
        return $this->setLink('self', $value);
    }

    /**
     * Remove the self link.
     */
    public function removeSelfLink()
    {
        return $this->removeLink('self');
    }
}
