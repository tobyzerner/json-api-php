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
use Tobscure\JsonApi\PaginationLinksTrait;

class PaginationLinksTraitTest extends AbstractTestCase
{
    public function testSetPaginationLinks()
    {
        $stub = new PaginationLinksTraitStub;
        $stub->setPaginationLinks('http://example.org', [], 0, 20);

        $this->assertEquals([
            'first' => 'http://example.org',
            'next' => 'http://example.org?page%5Boffset%5D=20'
        ], $stub->getLinks());

        $stub = new PaginationLinksTraitStub;
        $stub->setPaginationLinks('http://example.org', ['foo' => 'bar', 'page' => ['limit' => 20]], 30, 20, 100);

        $this->assertEquals([
            'first' => 'http://example.org?foo=bar&page%5Blimit%5D=20',
            'prev' => 'http://example.org?foo=bar&page%5Blimit%5D=20&page%5Boffset%5D=10',
            'next' => 'http://example.org?foo=bar&page%5Blimit%5D=20&page%5Boffset%5D=50',
            'last' => 'http://example.org?foo=bar&page%5Blimit%5D=20&page%5Boffset%5D=80'
        ], $stub->getLinks());

        $stub = new PaginationLinksTraitStub;
        $stub->setPaginationLinks('http://example.org', ['page' => ['number' => 2]], 50, 20, 100);

        $this->assertEquals([
            'first' => 'http://example.org',
            'prev' => 'http://example.org?page%5Bnumber%5D=2',
            'next' => 'http://example.org?page%5Bnumber%5D=4',
            'last' => 'http://example.org?page%5Bnumber%5D=5'
        ], $stub->getLinks());

        $stub = new PaginationLinksTraitStub;
        $stub->setPaginationLinks('http://example.org', ['page' => ['number' => 3, 'size' => 1]], 2, 1, 2);

        $this->assertEquals([
            'first' => 'http://example.org?page%5Bsize%5D=1',
            'prev' => 'http://example.org?page%5Bnumber%5D=2&page%5Bsize%5D=1',
            'last' => 'http://example.org?page%5Bnumber%5D=2&page%5Bsize%5D=1'
        ], $stub->getLinks());
    }
}

class PaginationLinksTraitStub
{
    use LinksTrait;
    use PaginationLinksTrait;

    public function getLinks()
    {
        return $this->links;
    }
}
