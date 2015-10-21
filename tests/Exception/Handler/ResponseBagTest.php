<?php
namespace Tobscure\Tests\Exception\Handler;

use Tobscure\JsonApi\Exception\Handler\ResponseBag;
use Tobscure\Tests\JsonApi\AbstractTestCase;

class ResponseBagTest extends AbstractTestCase
{
    public function test_should_instantiate_object()
    {
        $response = new ResponseBag(400, ['error' => 'Some error']);

        $this->assertInstanceOf(ResponseBag::class, $response);
    }

    public function test_should_return_set_values()
    {
        $response = new ResponseBag(400, ['error' => 'Some error']);

        $this->assertEquals(400, $response->getStatus());
        $this->assertEquals(['error' => 'Some error'], $response->getErrors());
    }
}
