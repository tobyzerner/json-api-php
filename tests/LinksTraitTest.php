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

use Tobscure\JsonApi\LinksTrait;

class LinksTraitTest extends AbstractTestCase
{
    public function testAddPaginationLinks()
    {
        $document = new LinksTraitStub;
        $document->addPaginationLinks('http://example.org', [], 0, 20);

        $this->assertEquals([
            'first' => 'http://example.org',
            'next' => 'http://example.org?page[offset]=20'
        ], $document->getLinks());

        $document = new LinksTraitStub;
        $document->addPaginationLinks('http://example.org', ['foo' => 'bar', 'page' => ['limit' => 20]], 30, 20, 100);

        $this->assertEquals([
            'first' => 'http://example.org?foo=bar&page[limit]=20',
            'prev' => 'http://example.org?foo=bar&page[limit]=20&page[offset]=10',
            'next' => 'http://example.org?foo=bar&page[limit]=20&page[offset]=50',
            'last' => 'http://example.org?foo=bar&page[limit]=20&page[offset]=80'
        ], $document->getLinks());

        $document = new LinksTraitStub;
        $document->addPaginationLinks('http://example.org', ['page' => ['number' => 2]], 50, 20, 100);

        $this->assertEquals([
            'first' => 'http://example.org',
            'prev' => 'http://example.org?page[number]=2',
            'next' => 'http://example.org?page[number]=4',
            'last' => 'http://example.org?page[number]=5'
        ], $document->getLinks());
    }
}

class LinksTraitStub
{
    use LinksTrait;
}
