<?php

declare(strict_types=1);

namespace Doctrine\Common\Collections;

/**
 * A class to to aggregate a grouped row set
 */
class GroupAggregate
{
    /**
     *  Aggregates
     */
    public static string $COUNT = 'count';
    public static string $SUM = 'sum';
    public static string $MAX = 'max';
    public static string $MIN = 'min';
    public static string $AVG = 'avg';
    
    /**
     * Aggregate the data from the original rows to the grouped rows
     * 
     * @param array $originalRows   original row set before grouping
     * @param array $groupedRows    grouped rows
     * @param array $groupedFields  fields used for the grouping
     * @param array $aggregates     aggregates
     */
    public static function aggregate(
        array $originalRows,
        array $groupedRows,
        array $groupedFields,
        array $aggregates
    ): array {

        // Find matching rows in the grouped rows an aggregate
        // If the original row has the same group field values
        // as the grouped row, it should be aggregated to this.
        foreach ($originalRows as  $originalRow) {
            foreach ($groupedRows as &$groupedRow) {
                if (self::isInGroup($originalRow, $groupedRow, $groupedFields)) {
                    $groupedRow = self::aggregateRow($originalRow, $groupedRow, $aggregates);
                }
            }
        }

        $groupedRows = self::defineAvg($groupedRows);

        return $groupedRows;
    }

    /**
     * Check if the given original row belong the the given grouped row
     * 
     */
    private static function isInGroup(
        array $originalRow,
        array $groupedRow,
        array $groupedFields,
    ): bool {
        $inGroup = true;
        foreach ($groupedRow as $key => $groupedRowValue) {
            if (self::isGroupedField($key, $groupedFields)) {
                if ($originalRow[$key] != $groupedRowValue) {
                    $inGroup = false;
                }
            }
        }
        return $inGroup;
    }

    /**
     * Check if the given field is a field to group
     */
    private static function isGroupedField(string $key, array $groupedFields): bool
    {
        return in_array($key, $groupedFields);
    }

    /**
     * Aggregate the original to the grouped row
     * 
     */
    private static function aggregateRow(
        array $originalRow,  
        array $groupedRow, 
        array $aggregates
    ): array 
    {
        foreach ($aggregates as $aggregate => $aggregateFields) {
            $groupedRow = match ($aggregate) {
                self::$COUNT => self::count($groupedRow),
                self::$SUM => self::sum($groupedRow, $originalRow, $aggregateFields),
                self::$MAX => self::minMax('max', $groupedRow, $originalRow, $aggregateFields),
                self::$MIN => self::minMax('min', $groupedRow, $originalRow, $aggregateFields),
                self::$AVG => self::avg($groupedRow, $originalRow, $aggregateFields),
                default => $groupedRow,
            };
        }
        return $groupedRow;
    }

    /**
     * Set count
     * 
     */
    private static function count(array $groupedRow): array
    {
        if (!isset($groupedRow['count'])) {
            $groupedRow['count'] = 1;
        } else {
            $groupedRow['count']++;
        }
        return $groupedRow;
    }

    /**
     * Set sum
     * 
     */
    private static function sum(
        array $groupedRow,
        array $originalRow,
        array $aggregateFields
    ): array {
        foreach ($aggregateFields as $aggregateField) {
            $key = self::getKey('sum', $aggregateField);
            $value = $originalRow[$aggregateField];
            if (!isset($groupedRow[$key])) {
                $groupedRow[$key] = $value;
            } else {
                $groupedRow[$key] += $value;
            }
        }
        return $groupedRow;
    }

    /**
     * set min or max
     * 
     * @param string $minMax  // 'min' or 'max'
     */
    private static function minMax(
        string $minMax,
        array $groupedRow,
        array $originalRow,
        array $aggregateFields
    ): array {

        foreach ($aggregateFields as $aggregateField) {
            $key = self::getKey($minMax, $aggregateField);
            $value = $originalRow[$aggregateField];

            if (!isset($groupedRow[$key])) {
                $groupedRow[$key] = $value;
            } else {
                if ($minMax == 'min' && $value < $groupedRow[$key]) {
                    $groupedRow[$key] = $value;
                }
                if ($minMax == 'max' && $value > $groupedRow[$key]) {
                    $groupedRow[$key] = $value;
                }
            }
        }
        return $groupedRow;
    }

    /**
     * Set avg
     * 
     */
    private static function avg(
        array $groupedRow,
        array $originalRow,
        array $aggregateFields
    ): array {
        foreach ($aggregateFields as $aggregateField) {
            $key = self::getKey('avg', $aggregateField);
            $value = $originalRow[$aggregateField];
            if (!isset($groupedRow[$key])) {
                $groupedRow[$key] =  [
                    'sum' =>  $value,
                    'items' => 1
                ];
            } else {
                $groupedRow[$key] =  [
                    'sum' =>  $groupedRow[$key]['sum'] + $value,
                    'items' => $groupedRow[$key]['items'] + 1
                ];
            }
        }
        return $groupedRow;
    }

    /**
     * Define the averages after all data is read
     * 
     */
    private static function defineAvg(array $groupedRows): array
    {
        foreach ($groupedRows as &$groupedRow) {
            foreach ($groupedRow as $key => &$value) {
                if (str_starts_with($key, 'avg(')) {
                    $value = $value['sum'] / $value['items'];
                }
            }
        }
        return $groupedRows;
    }

    /**
     * get aggregate key
     * 
     * Example : sum(rate)
     * 
     */
    private static function getKey(string $aggregateFunctionName, string $aggregateField): string
    {
        return $aggregateFunctionName . '(' . $aggregateField . ')';
    }
}