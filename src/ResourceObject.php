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

use LogicException;
use InvalidArgumentException;

class ResourceObject extends ResourceIdentifier
{
    use LinksTrait, SelfLinkTrait;

    private $attributes = [];
    private $relationships = [];

    public function setAttribute($name, $value)
    {
        if (! $this->validateField($name)) {
            throw new InvalidArgumentException('Invalid attribute name');
        }

        if (isset($this->relationships[$name])) {
            throw new LogicException("Field $name already exists in relationships");
        }

        $this->attributes[$name] = $value;
    }

    public function setRelationship($name, Relationship $value)
    {
        if (! $this->validateField($name)) {
            throw new InvalidArgumentException('Invalid relationship name');
        }

        if (isset($this->attributes[$name])) {
            throw new LogicException("Field $name already exists in attributes");
        }

        $this->relationships[$name] = $value;
    }

    public function jsonSerialize()
    {
        return array_filter(
            parent::jsonSerialize() + [
                'attributes' => $this->attributes,
                'relationships' => $this->relationships,
                'links' => $this->links
            ]
        );
    }

    private function validateField($name)
    {
        return ! in_array($name, ['id', 'type']);
    }
}
