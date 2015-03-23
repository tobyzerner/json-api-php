<?php namespace Tobscure\JsonApi\Elements;

interface ElementInterface
{
    public function getId();

    public function getType();

    public function getResources();

    public function toArray();
}
