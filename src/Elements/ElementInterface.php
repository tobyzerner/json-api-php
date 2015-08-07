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
    public function getId();

    public function getType();

    public function getResources();

    public function toArray();
}
