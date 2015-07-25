<?php

use Tobscure\JsonApi\Criteria;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIncludeReturnsArrayOfIncludes()
    {
        $criteria = new Criteria(['include' => 'posts,images']);

        $this->assertEquals(['posts', 'images'], $criteria->getInclude());
    }

    public function testGetSortReturnsArrayOfFieldToSortDirection()
    {
        $criteria = new Criteria(['sort' => '+firstname']);

        $this->assertEquals(['firstname' => 'asc'], $criteria->getSort());
    }

    public function testGetSortSupportsMultipleSortedFieldsSeparatedByComma()
    {
        $criteria = new Criteria(['sort' => '+firstname,-lastname']);

        $this->assertEquals(['firstname' => 'asc', 'lastname' => 'desc'], $criteria->getSort());
    }

    public function testGetSortIgnoresInvalidDirections()
    {
        $criteria = new Criteria(['sort' => '*firstname']);

        $this->assertEmpty($criteria->getSort());
    }

    public function testGetSortDefaultsToEmptyArray()
    {
        $criteria = new Criteria([]);

        $this->assertEmpty($criteria->getSort());
    }

    public function testGetOffsetParsesThePageOffset()
    {
        $criteria = new Criteria(['page' => ['offset' => 10]]);

        $this->assertEquals(10, $criteria->getOffset());
    }

    public function testGetOffsetIsAtLeastZero()
    {
        $criteria = new Criteria(['page' => ['offset' => -5]]);

        $this->assertEquals(0, $criteria->getOffset());
    }

    public function testGetLimitParsesThePageLimit()
    {
        $criteria = new Criteria(['page' => ['limit' => 100]]);

        $this->assertEquals(100, $criteria->getLimit());
    }
}
