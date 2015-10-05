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

use Closure;

trait ClosureTrait
{
    /**
     * @var Closure
     */
    protected $closure;

    /**
     * @param mixed $serializer
     * @param Closure $closure
     */
    public function __construct($serializer, Closure $closure)
    {
        parent::__construct($serializer);

        $this->closure = $closure;
    }

    /**
     * @param mixed $model
     * @return mixed
     */
    protected function getRelationshipData($model)
    {
        return call_user_func($this->closure, $model);
    }
}
