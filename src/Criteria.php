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
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function getInclude()
    {
        return explode(',', $this->getInput('include'));
    }

    public function getOffset()
    {
        return max(0, $this->getPage('offset'));
    }

    public function getLimit()
    {
        return $this->getPage('limit');
    }

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

    protected function getInput($key)
    {
        return isset($this->input[$key]) ? $this->input[$key] : null;
    }

    protected function getPage($key)
    {
        $page = $this->getInput('page');

        return isset($page[$key]) ? $page[$key] : '';
    }
}
