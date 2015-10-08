<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi\Relationship;

use Tobscure\JsonApi\Relationship;

interface BuilderInterface
{
    /**
     * @param mixed $model
     * @return Relationship|null
     */
    public function build($model);
}
