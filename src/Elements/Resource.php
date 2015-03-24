<?php namespace Tobscure\JsonApi\Elements;

class Resource extends ElementAbstract
{
    protected $id;

    protected $attributes = [];

    protected $links = [];

    protected $included = [];

    public function __construct($type, $id, $attributes = [], $links = [], $included = [])
    {
        $this->type = $type;
        $this->attributes = $attributes;
        $this->links = $links;
        $this->included = $included;

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

    public function getIncluded()
    {
        return $this->included;
    }

    public function setIncluded($included)
    {
        $this->included = $included;
    }

    public function addIncluded($name, $relationship)
    {
        $this->included[$name] = $relationship;
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

            if ($this->links || $this->included) {
                $array['links'] = [];

                foreach ($this->included as $name => $link) {
                    $array['links'][$name] = $link->toArray();
                }
                foreach ($this->links as $name => $link) {
                    $array['links'][$name] = $link->toArray();
                }
            }
        }

        return $array;
    }
}
