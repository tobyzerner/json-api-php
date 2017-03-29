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

abstract class AbstractResource implements ResourceInterface
{
    use LinksTrait, SelfLinkTrait, MetaTrait;

    /**
     * The resource type.
     *
     * @var string
     */
    protected $type;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(array $fields = null)
    {
        return [];
    }

    /**
     * Get the links.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * Get the meta data.
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException
     */
    public function getRelationship($name)
    {
        $method = $this->getRelationshipMethodName($name);

        $relationship = $this->$method();

        if ($relationship !== null && ! ($relationship instanceof Relationship)) {
            throw new LogicException('Relationship method must return null or an instance of Tobscure\JsonApi\Relationship');
        }

        return $relationship;
    }

    /**
     * Get the method name for the given relationship.
     *
     * snake_case and kebab-case are converted into camelCase.
     *
     * @param string $name
     *
     * @return string
     */
    private function getRelationshipMethodName($name)
    {
        return 'get'.implode(array_map('ucfirst', preg_split('/[-_]/', $name))).'Relationship';
    }
}
