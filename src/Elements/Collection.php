<?php namespace Tobscure\JsonApi\Elements;

class Collection extends ElementAbstract
{
    protected $resources;

    public function __construct($type, $resources)
    {
        $this->type = $type;
        $this->resources = $resources;
    }

    public function getId()
    {
        $ids = [];

        foreach ($this->resources as $resource) {
            $ids[] = $resource->getId();
        }

        return $ids;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    public function toArray($full = true)
    {
        return array_map(function ($resource) use ($full) {
            return $resource->toArray($full);
        }, $this->resources);
    }
}
