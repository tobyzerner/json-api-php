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

trait PaginationLinksTrait
{
    abstract public function setLink($key, $value);
    abstract public function removeLink($key);
    
    /**
     * Set pagination links (first, prev, next, and last).
     *
     * @param string $url The base URL for pagination links.
     * @param array $queryParams The query params provided in the request.
     * @param int $offset The current offset.
     * @param int $limit The current limit.
     * @param int|null $total The total number of results, or null if unknown.
     */
    public function setPaginationLinks($url, array $queryParams, $offset, $limit, $total = null)
    {
        if (isset($queryParams['page']['number'])) {
            $offset = floor($offset / $limit) * $limit;
        }

        $this->setPaginationLink('first', $url, $queryParams, 0, $limit);

        $this->removeLink('prev');
        $this->removeLink('next');
        $this->removeLink('last');

        if ($offset > 0) {
            $this->setPaginationLink('prev', $url, $queryParams, max(0, $offset - $limit), $limit);
        }

        if ($total === null || $offset + $limit < $total) {
            $this->setPaginationLink('next', $url, $queryParams, $offset + $limit, $limit);
        }

        if ($total) {
            $this->setPaginationLink('last', $url, $queryParams, floor(($total - 1) / $limit) * $limit, $limit);
        }
    }

    /**
     * Set a pagination link.
     *
     * @param string $name The name of the link.
     * @param string $url The base URL for pagination links.
     * @param array $queryParams The query params provided in the request.
     * @param int $offset The offset to link to.
     * @param int $limit The current limit.
     */
    private function setPaginationLink($name, $url, array $queryParams, $offset, $limit)
    {
        if (! isset($queryParams['page']) || ! is_array($queryParams['page'])) {
            $queryParams['page'] = [];
        }

        $page = &$queryParams['page'];

        if (isset($page['number'])) {
            $page['number'] = floor($offset / $limit) + 1;

            if ($page['number'] <= 1) {
                unset($page['number']);
            }
        } else {
            $page['offset'] = $offset;

            if ($page['offset'] <= 0) {
                unset($page['offset']);
            }
        }

        if (isset($page['limit'])) {
            $page['limit'] = $limit;
        } elseif (isset($page['size'])) {
            $page['size'] = $limit;
        }

        $queryString = http_build_query($queryParams);

        $this->setLink($name, $url.($queryString ? '?'.$queryString : ''));
    }    
}
