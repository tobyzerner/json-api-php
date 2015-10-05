<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi;

use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Exception\JsonApiSerializableInterface;
use Exception;

class ExceptionHandler
{
    /**
     * @var boolean
     */
    protected $debug = false;

    /**
     * Set whether to display debug information for non-serializable exceptions.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Get a JSON-API document representing the given exception.
     *
     * @param Exception $e
     * @return Document
     */
    public function handle(Exception $e)
    {
        if ($e instanceof JsonApiSerializableInterface) {
            $status = $e->getStatusCode();

            $errors = $e->getErrors();
        } else {
            $status = 500;

            $error = [
                'code' => $status,
                'title' => 'Internal Server Error'
            ];

            if ($this->debug) {
                $error['detail'] = (string) $e;
            }

            $errors = [$error];
        }

        return $this->createErrorDocument($errors);
    }

    /**
     * Create a JSON-API document with the given errors array.
     *
     * @param array $errors
     * @return Document
     */
    protected function createErrorDocument(array $errors)
    {
        $document = new Document;

        $document->setErrors($errors);

        return $document;
    }
}
