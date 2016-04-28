<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\Exception\Handler;

use Exception;
use Tobscure\JsonApi\Exception\Handler\InvalidParameterExceptionHandler;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;
use Tobscure\JsonApi\Exception\InvalidParameterException;

class InvalidParameterExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlerCanManageInvalidParameterExceptions()
    {
        $handler = new InvalidParameterExceptionHandler();

        $this->assertTrue($handler->manages(new InvalidParameterException));
    }

    public function testHandlerCanNotManageOtherExceptions()
    {
        $handler = new InvalidParameterExceptionHandler();

        $this->assertFalse($handler->manages(new Exception));
    }

    public function testErrorHandling()
    {
        $handler = new InvalidParameterExceptionHandler();
        $response = $handler->handle(new InvalidParameterException('error', 1, null, 'include'));

        $this->assertInstanceOf(ResponseBag::class, $response);
        $this->assertEquals(400, $response->getStatus());
        $this->assertEquals([['code' => 1, 'source' => ['parameter' => 'include']]], $response->getErrors());
    }
}
