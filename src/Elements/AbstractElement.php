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
    /**
     * The type.
     *
     * @var string
     */
    protected $type;

    /**
     * Get an id or an array of ids.
     *
     * @return string|array
     */
    abstract public function getId();

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
