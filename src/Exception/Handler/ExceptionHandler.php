<?php
namespace Tobscure\JsonApi\Exception\Handler;

use Exception;

interface ExceptionHandler
{
    /**
     * If the exception handler is able to format a response for the provided exception,
     * then the implementation should return true.
     *
     * @param Exception $e
     * @return bool
     */
    public function manages(Exception $e);

    /**
     * Handle the provided exception.
     *
     * @param Exception $e
     * @return ResponseBag
     */
    public function handle(Exception $e);
}
