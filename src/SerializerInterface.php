<?php namespace Tobscure\JsonApi;

interface SerializerInterface
{
    public function __construct($include = []);

    public function collection($dataSet);

    public function resource($data);
}
