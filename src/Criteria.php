<?php namespace Tobscure\JsonApi;

class Criteria
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

	public function getFilter()
	{
		if ($filter = $this->getInput('filter'))
		{
			return explode(',', $filter);
		}
	}

    public function getInclude()
    {
	    $includes = explode(',', $this->getInput('include'));
	    foreach ($includes as $i => $include)
	    {
		    if (!trim($include))
		    {
			    unset($includes[$i]);
		    }
	    }

	    return $includes;
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

            if ($order === '+' || $order === '-') {
                $sort[substr($field, 1)] = $order === '+' ? 'asc' : 'desc';
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
