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

/**
 * This is the serializer interface.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
interface SerializerInterface
{
    /**
     * Create a new collection.
     *
     * @param array $data
     *
     * @return @return \Tobscure\JsonApi\Elements\Collection|null
     */
    public function collection($data);

    /**
     * Create a new collection.
     *
     * @param array $data
     *
     * @return @return \Tobscure\JsonApi\Elements\Resource|null
     */
    public function resource($data);
}
