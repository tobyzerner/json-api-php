<?php namespace Tobscure\JsonApi;

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

    /**
     * [getSort description]
     *
     * @todo require + prefix for ascending order
     * @todo add support for multiple sorts (+foo,-bar)
     * @return [type] [description]
     */
    public function getSort()
    {
        $field = $this->getInput('sort');
        $order = null;

        if (substr($field, 0, 1) === '-') {
            $order = 'desc';
            $field = substr($field, 1);
        }

        if ($field && ! $order) {
            $order = 'asc';
        }

        return [$field => $order];
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
