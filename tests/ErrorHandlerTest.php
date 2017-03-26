<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\Tests\JsonApi;

use Exception;
use Tobscure\JsonApi\ErrorHandler;

class ErrorHandlerTest extends AbstractTestCase
{
    public function testThrowExceptionWhenNoHandlersPresent()
    {
        $this->setExpectedException('RuntimeException');

        $handler = new ErrorHandler;

        $handler->handle(new Exception);
    }
}
