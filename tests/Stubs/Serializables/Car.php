<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi\Stubs\Serializables;

class Car
{
    public $id;

    public $name = 'Car';

    public function __construct($id)
    {
        $this->id = $id;
    }
}
