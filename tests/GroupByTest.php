<?php

declare(strict_types=1);

namespace Doctrine\Tests\Common\Collections;

/*
 * Tests for the feature groupBy
 *
 *  This test cover the following use cases.
 *  This covers the expressions on the page document page Expressions
 *  https://www.doctrine-project.org/projects/doctrine-collections/en/2.3/expressions.html#expressions
 *
 *  1) Base groupBy
 *
 *     Test that the group by works
 *     without any other criteria or aggregates.
 *
 *  2) Test groupBy with aggregators
 *
 *     Test that we can use multiple aggregators by
 *     multiple fields.
 *
 *  3) Test orderBy with aggregators
 *
 *     Test that we can order by aggregator columns
 *     in the result set.
 *
 *  4) Test where
 *
 *     First we test that we can filter by the grouped rows
 *     by the aggregate results
 *
 *     Then we test that we can also filter by the values
 *     of the original row set.
 *
 *  5) Test andWhere
 *
 *     Test that we can filter multiple aggregate columns
 *     by AND operator.
 *
 *  6) Test orWhere
 *
 *     Test that we can filter multiple aggregate columns
 *     by AND operator.
 *
 *   7) Test setFirstResult with groupBy.
 *
 *      Set the start row of the result set
 *
 *   8) Test setMaxResult with groupBy.
 *
 *      Set the start row of the result set
 */

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\GroupAggregate;
use Doctrine\Common\Collections\Order;
use PHPUnit\Framework\TestCase;


class GroupByTest extends TestCase
{


    /**
     * Get test collection for all of the tests
     *
     * @return ArrayCollection
     */
    private function getCollection(): ArrayCollection
    {
        return  new ArrayCollection(
            [
                [
                    'id'       => 0,
                    'category' => 'A',
                    'key'      => 3,
                    'label'    => 'something',
                    'value'    => 12,
                ],
                [
                    'id'       => 1,
                    'category' => 'B',
                    'key'      => 3,
                    'label'    => 'abra',
                    'value'    => 4,
                ],
                [
                    'id'       => 2,
                    'category' => 'A',
                    'key'      => 4,
                    'label'    => 'cadabra',
                    'value'    => 14,
                ],
                [
                    'id'       => 3,
                    'category' => 'B',
                    'key'      => 4,
                    'label'    => 'alacreate',
                    'value'    => 3,
                ],
                [
                    'id'       => 4,
                    'category' => 'A',
                    'key'      => 4,
                    'label'    => 'cadabra',
                    'value'    => 2,
                ],
                [
                    'id'       => 6,
                    'category' => 'B',
                    'key'      => 4,
                    'label'    => 'alacreate',
                    'value'    => 8,
                ],
            ]
        );

    }


    /**
     * Test  groupBy
     *
     * Test that the group by works
     * without any other criteria or aggregates.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key
     * GROUP BY category,
     *          key
     */
    public function testGroupBy(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];

        $criteria = Criteria::create()->groupBy($fieldsToGroup);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            [
                'category' => 'A',
                'key'      => 3,
            ],
            [
                'category' => 'A',
                'key'      => 4,
            ],
            [
                'category' => 'B',
                'key'      => 3,
            ],
            [
                'category' => 'B',
                'key'      => 4,
            ],
        ];

        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test criteria groupBy with aggregates
     *
     * Test that we can use multiple aggregators by
     * multiple fields.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value),
     *          sum(key),
     *          max(value),
     *          max(id),
     *          min(value),
     *          avg(value)
     * FROM     Collection
     * GROUP BY category,
     *          key
     */
    public function testGroupByWithAggregates(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [
            GroupAggregate::$COUNT => [],
            GroupAggregate::$SUM   => [
                'value',
                'key',
            ],
            GroupAggregate::$MAX   => [
                'value',
                'id',
            ],
            GroupAggregate::$MIN   => ['value'],
            GroupAggregate::$AVG   => ['value'],
        ];

        $criteria = Criteria::create()->groupBy($fieldsToGroup, $aggregates);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            [
                'category'   => 'A',
                'key'        => 3,
                'count'      => 1,
                'sum(value)' => 12,
                'sum(key)'   => 3,
                'max(value)' => 12,
                'max(id)'    => 0,
                'min(value)' => 12,
                'avg(value)' => 12,
            ],
            [
                'category'   => 'A',
                'key'        => 4,
                'count'      => 2,
                'sum(value)' => 16,
                'sum(key)'   => 8,
                'max(value)' => 14,
                'max(id)'    => 4,
                'min(value)' => 2,
                'avg(value)' => 8,
            ],
            [
                'category'   => 'B',
                'key'        => 3,
                'count'      => 1,
                'sum(value)' => 4,
                'sum(key)'   => 3,
                'max(value)' => 4,
                'max(id)'    => 1,
                'min(value)' => 4,
                'avg(value)' => 4,
            ],
            [
                'category'   => 'B',
                'key'        => 4,
                'count'      => 2,
                'sum(value)' => 11,
                'sum(key)'   => 8,
                'max(value)' => 8,
                'max(id)'    => 6,
                'min(value)' => 3,
                'avg(value)' => 5.5,
            ],

        ];

        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test order by
     *
     * Test that we can order by aggregator columns
     * in the result set.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value)
     * FROM     Collection
     * GROUP BY category,
     *          key
     * ORDER BY sum(value)
     */
    public function testOrderByWithAggregators(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [
            GroupAggregate::$SUM => ['value'],
        ];

        $criteria = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates)
            ->orderBy(['sum(value)' => Order::Descending]);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 16,
            ],
            0 => [
                'category'   => 'A',
                'key'        => 3,
                'sum(value)' => 12,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
            ],
            2 => [
                'category'   => 'B',
                'key'        => 3,
                'sum(value)' => 4,
            ],
        ];

        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test where
     *
     * First we test that we can filter by the grouped rows
     * by the aggregate results
     *
     * Then we test that we can also filter by the values
     * of the original row set.
     *
     * Corresponding SQL:
     *
     * 1)
     * SELECT   category,
     *          key,
     *          sum(value)
     * FROM     Collection
     * WHERE    sum(value) > 5
     * GROUP BY category,
     *          key
     * ORDER BY sum(value)
     *
     * 2)
     * SELECT   category,
     *          key,
     *          sum(value)
     * FROM     Collection
     * WHERE    sum(value) > 5
     * AND      value > 2
     * GROUP BY category,
     *          key
     * ORDER BY sum(value)
     */
    public function testWhere(): void
    {
        $collection = $this->getCollection();

        //
        // Test to filter by the aggregate column 'sum(value)'
        //
        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [
            GroupAggregate::$SUM => ['value'],
        ];

        $expr = new Comparison('sum(value)', Comparison::GT, 6);

        /*
         * @var Criteria   $groupCriteria
         */
        $groupCriteria = Criteria::create()->where($expr);

        $criteria = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates, $groupCriteria)
            ->orderBy(['sum(value)' => Order::Descending]);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 16,
            ],
            0 => [
                'category'   => 'A',
                'key'        => 3,
                'sum(value)' => 12,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
            ],
        ];

        self::assertSame($expected, $groupedRows);

        //
        // Test to filter by the aggregate column 'sum(value)'
        // and the column 'value' of the original row set
        //
        $exprMinValue = new Comparison('value', Comparison::GT, 2);
        $criteria     = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates, $groupCriteria)
            ->where($exprMinValue)
            ->orderBy(['sum(value)' => Order::Descending]);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 14,
            ],
            0 => [
                'category'   => 'A',
                'key'        => 3,
                'sum(value)' => 12,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
            ],
        ];

        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test andWhere
     *
     * Test that we can filter multiple aggregate columns
     * by AND operator.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value)
     * FROM     Collection
     * WHERE    sum(value) > 5
     * AND      sum(key) > 5
     * GROUP BY category,
     *          key
     * ORDER BY sum(value) DESC
     */
    public function testAndWhere(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [
            GroupAggregate::$SUM => [
                'value',
                'key',
            ],
        ];

        $exprMinSumValue = new Comparison('sum(value)', Comparison::GT, 6);
        $exprMinSumKey   = new Comparison('sum(key)', Comparison::GT, 5);

        /*
         * @var Criteria   $groupCriteria
         */
        $groupCriteria = Criteria::create()
            ->where($exprMinSumValue)
            ->andWhere($exprMinSumKey);

        $criteria = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates, $groupCriteria)
            ->orderBy(['sum(value)' => Order::Descending]);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 16,
                'sum(key)'   => 8,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
                'sum(key)'   => 8,
            ],
        ];

        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test orWhere
     *
     * Test that we can filter multiple aggregate columns
     * by OR operator.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value),
     *          sum(key)
     * FROM Collection
     * WHERE    sum(value) > 6
     * OR       sum(key) > 5
     * GROUP BY category,
     *          key
     * ORDER BY sum(value) DESC
     */
    public function testOrWhere(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [
            GroupAggregate::$SUM => [
                'value',
                'key',
            ],
        ];

        $exprMinSumValue = new Comparison('sum(value)', Comparison::GT, 6);
        $exprMinSumKey   = new Comparison('sum(key)', Comparison::GT, 5);

        $groupCriteria = Criteria::create()
            ->where($exprMinSumValue)
            ->orWhere($exprMinSumKey);

        $criteria = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates, $groupCriteria)
            ->orderBy(['sum(value)' => Order::Descending]);

        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 16,
                'sum(key)'   => 8,
            ],
            0 => [
                'category'   => 'A',
                'key'        => 3,
                'sum(value)' => 12,
                'sum(key)'   => 3,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
                'sum(key)'   => 8,
            ],
        ];

        self::assertSame($expected, $groupedRows);

    }//end testOrWhere()


    /**
     * Test setFirstResult
     *
     * Set the start row of the result set with groupBy.
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value),
     *          sum(key)
     * FROM Collection
     * GROUP BY category,
     *          key
     * LIMIT    10000
     * OFFSET   2
     */
    public function testSetFirstResult(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [GroupAggregate::$SUM => ['value']];

        $criteria    = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates)
            ->setFirstResult(2)
            ->orderBy(['sum(value)' => Order::Descending]);
        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
            ],
            2 => [
                'category'   => 'B',
                'key'        => 3,
                'sum(value)' => 4,
            ],
        ];
        self::assertSame($expected, $groupedRows);

    }


    /**
     * Test setMaxResults
     *
     * Test set the max amount of records with groupBy.
     *
     * Set the start row of the result set
     *
     * Corresponding SQL:
     *
     * SELECT   category,
     *          key,
     *          sum(value),
     *          sum(key)
     * FROM Collection
     * GROUP BY category,
     *          key
     * LIMIT    3
     * OFFSET   0
     */
    public function testSetMaxResults(): void
    {
        $collection = $this->getCollection();

        $fieldsToGroup = [
            'category',
            'key',
        ];
        $aggregates    = [GroupAggregate::$SUM => ['value']];

        $criteria    = Criteria::create()
            ->groupBy($fieldsToGroup, $aggregates)
            ->setMaxResults(3)
            ->orderBy(['sum(value)' => Order::Descending]);
        $groupedRows = $collection->matching($criteria)->toArray();

        $expected = [
            1 => [
                'category'   => 'A',
                'key'        => 4,
                'sum(value)' => 16,
            ],
            0 => [
                'category'   => 'A',
                'key'        => 3,
                'sum(value)' => 12,
            ],
            3 => [
                'category'   => 'B',
                'key'        => 4,
                'sum(value)' => 11,
            ],

        ];

        self::assertSame($expected, $groupedRows);

    }


}
