<?php namespace Tobscure\JsonApi\Elements;

class Resource extends ElementAbstract
{
    protected $id;

    protected $attributes = [];

    protected $links = [];

    public function __construct($type, $id, $attributes = [], $links = [])
    {
        $this->type = $type;
        $this->attributes = $attributes;
        $this->links = $links;

        $this->setId($id);
    }

    public function getId()
    {
        return $this->id;
    }

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

    public function addLink($name, $relationship)
    {
        $this->links[$name] = $relationship;
    }

    public function getResources()
    {
        return [$this];
    }

    public function toArray($full = true)
    {
        $array = ['type' => $this->type, 'id' => $this->id];

        if ($full) {
            $array += (array) $this->attributes;

            if ($this->links) {
                $array['links'] = [];

                foreach ($this->links as $name => $link) {
                    $array['links'][$name] = $link->toArray();
                }
            }
        }

        return $array;
    }
}
