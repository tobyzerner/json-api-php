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

use JsonSerializable;

class Relationship implements JsonSerializable
{
    use LinksTrait;
    use SelfLinkTrait;
    use RelatedLinkTrait;
    use PaginationLinksTrait;
    use MetaTrait;

    /**
     * The data object.
     *
     * @var \Tobscure\JsonApi\ResourceInterface|\Tobscure\JsonApi\ResourceInterface[]|null
     */
    protected $data;

    /**
     * Create a new relationship.
     *
     * @param \Tobscure\JsonApi\ResourceInterface|\Tobscure\JsonApi\ResourceInterface[]|null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Get the data object.
     *
     * @return \Tobscure\JsonApi\ResourceInterface|\Tobscure\JsonApi\ResourceInterface[]|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data object.
     *
     * @param \Tobscure\JsonApi\ResourceInterface|\Tobscure\JsonApi\ResourceInterface[]|null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Build the relationship as an array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $relationship = [];

        if ($this->data) {
            $relationship['data'] = is_array($this->data)
                ? array_map([$this, 'buildIdentifier'], $this->data)
                : $this->buildIdentifier($this->data);
        }

        return array_filter($relationship + [
            'meta' => $this->meta,
            'links' => $this->links
        ]);
    }

    /**
     * Build an idenitfier array for the given resource.
     *
     * @param ResourceInterface $resource
     *
     * @return array
     */
    private function buildIdentifier(ResourceInterface $resource)
    {
        return [
            'type' => $resource->getType(),
            'id' => $resource->getId()
        ];
    }
}
