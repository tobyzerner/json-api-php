<?php namespace Tobscure\JsonApi\Elements;

class Resource extends ElementAbstract {

    protected $attributes;

    protected $links = [];

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

    public function setLinks($links)
    {
        $this->links = $links;
    }

    public function addLink($type, $element)
    {
        $this->links[$type] = $element;
    }

    public function getResources()
    {
        return [$this];
    }

    public function toArray()
    {
        $array = ['id' => $this->id] + (array) $this->attributes;

        if ($this->links) {
            $array['links'] = [];

            foreach ($this->links as $type => $link) {
                $array['links'][$type] = $link->getId();
            }
        }

        return $array;
    }

}
