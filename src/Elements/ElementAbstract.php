<?php namespace Tobscure\JsonApi\Elements;

abstract class ElementAbstract implements ElementInterface
{
    protected $id;

    protected $type;

    protected $href;

    public function __construct($type, $href)
    {
        $this->type = $type;
        $this->href = $href;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
