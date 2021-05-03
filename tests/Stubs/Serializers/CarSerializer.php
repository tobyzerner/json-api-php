<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi\Stubs\Serializers;

use Tobscure\JsonApi\AbstractSerializer;

class CarSerializer extends AbstractSerializer
{
    protected $type = 'cars';

    public function getAttributes($car, array $fields = null)
    {
        return [
            'name' => $car->name,
        ];
    }
}
