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
 * This is the abstract element class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
abstract class AbstractElement implements ElementInterface
{
    protected $type;

    abstract public function getId();

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
