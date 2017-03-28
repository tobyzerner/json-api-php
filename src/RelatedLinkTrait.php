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

trait RelatedLinkTrait
{
    abstract public function setLink($key, $value);
    abstract public function removeLink($key);

    /**
     * Set the related link.
     *
     * @param string|Link $value
     */
    public function setRelatedLink($value)
    {
        return $this->setLink('related', $value);
    }

    /**
     * Remove the related link.
     */
    public function removeRelatedLink()
    {
        return $this->removeLink('related');
    }
}
