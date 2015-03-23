<?php namespace Tobscure\JsonApi\Elements;

abstract class ElementAbstract implements ElementInterface
{
    protected $type;

    abstract public function getId();

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
