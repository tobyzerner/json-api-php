<?php

namespace Tobscure\JsonApi\Exception\Handler;

use Exception;

class FallbackExceptionHandler implements ExceptionHandler
{
    /**
     * Set the debug mode.
     *
     * @var boolean
     */
    private $debug;

    /**
     * Set the debug mode for the default handler.
     *
     * @param boolean $debugMode
     */
    public function __construct($debugMode)
    {
        $this->debug = $debugMode;
    }

    /**
     * If the exception handler is able to format a response for the provided exception,
     * then the implementation should return true.
     *
     * @param Exception $e
     * @return boolean
     */
    public function manages(Exception $e)
    {
        return true;
    }

    /**
     * Handle the provided exception.
     *
     * @param Exception $e
     * @return mixed
     */
    public function handle(Exception $e)
    {
        $status = 500;
        $error = $this->constructError($e, $status);

        return new ResponseBag($status, [$error]);
    }

    /**
     * @param Exception $e
     * @param $status
     * @return array
     */
    private function constructError(Exception $e, $status)
    {
        $error = ['code' => $status, 'title' => 'Internal Server Error'];

        if ($this->debug) {
            $error['detail'] = (string) $e;
        }

        return $error;
    }
}
