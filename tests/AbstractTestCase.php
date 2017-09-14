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

use PHPUnit_Framework_TestCase;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Asserts the two values encode to json and produce same result.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    public static function assertProduceSameJson($expected, $actual, $message = '')
    {
        self::assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual), $message);
    }
}
