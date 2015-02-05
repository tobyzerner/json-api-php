<?php namespace Tobscure\JsonApi\Elements;

class Resource extends ElementAbstract
{
    protected $attributes;

    protected $links = [];

    protected $includes = [];

    public function setId($id)
    {
        $this->id = (string) $id;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getIncludes()
    {
        return $this->includes;
    }

    public function setLinks($links)
    {
        $this->links = $links;
    }

    public function setIncludes($includes)
    {
        $this->includes = $includes;
    }

    public function addLink($type, $element)
    {
        $this->links[$type] = $element;
    }

    public function addInclude($type, $element)
    {
        $this->includes[$type] = $element;
    }

    public function getResources()
    {
        return [$this];
    }

    public function toArray()
    {
        $array = ['id' => $this->id] + (array) $this->attributes;

        if ($this->includes || $this->links) {
            $array['links'] = [];

            foreach ($this->includes as $type => $element) {
                $array['links'][$type] = $element->getId();
            }
            foreach ($this->links as $type => $element) {
                $array['links'][$type] = $element->getId();
            }
        }

        return $array;
    }
}
