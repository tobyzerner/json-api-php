<?php namespace Tobscure\JsonApi;

interface SerializerInterface
{
    public function collection($data);

    public function resource($data);
}
