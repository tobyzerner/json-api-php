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
 * This is the criteria class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class Criteria
{
    /**
     * The input array.
     *
     * @var array
     */
    protected $input;

    /**
     * Create a new criteria instance.
     *
     * @param array $input
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Get the includes.
     *
     * @return array
     */
    public function getInclude()
    {
        $include = $this->getInput('include');

        return $include ? explode(',', $include) : [];
    }

    /**
     * Get number of offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return max(0, $this->getPage('offset'));
    }

    /**
     * Get the limit.
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->getPage('limit');
    }

    /**
     * Get the sort.
     *
     * @return array
     */
    public function getSort()
    {
        $sort = [];

        $fields = explode(',', $this->getInput('sort'));

        foreach ($fields as $field) {
            $order = substr($field, 0, 1);

            if ($order === '+' || $order === '-' || $order === ' ') {
                $sort[substr($field, 1)] = $order === '+' || $order === ' ' ? 'asc' : 'desc';
            }
        }

        return $sort;
    }

    /**
     * Get an input item.
     *
     * @param string $key
     *
     * @return string|null
     */
    protected function getInput($key)
    {
        return isset($this->input[$key]) ? $this->input[$key] : null;
    }

    /**
     * Get the page.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getPage($key)
    {
        $page = $this->getInput('page');

        return isset($page[$key]) ? $page[$key] : '';
    }
}
