<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\CriteriaFactory;
use Doctrine\Common\Collections\Expr\CompositeExpression;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CriteriaFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $criteriaFactory;

    protected function setUp()
    {
        $this->criteriaFactory = new CriteriaFactory();
    }

    public function testBasicCreation()
    {
        $criteriaArray = array(
            'expression' => array('fld' => 'name', 'op' => '=', 'val' => 'test'),
            'orderings' => array('name' => 'ASC'),
            'first_result' => 10,
            'max_results' => 20,
        );

        $criteria = $this->criteriaFactory->create($criteriaArray);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $where);
        $this->assertEquals('name', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('test', $where->getValue()->getValue());
        $this->assertEquals(array('name' => 'ASC'), $criteria->getOrderings());
        $this->assertEquals(10, $criteria->getFirstResult());
        $this->assertEquals(20, $criteria->getMaxResults());
    }

    public function testPartialCreation()
    {
        $criteriaArray = array(
            'orderings' => array('name' => 'ASC'),
            'max_results' => 10,
        );

        $criteria = $this->criteriaFactory->create($criteriaArray);

        $this->assertNull($criteria->getWhereExpression());
        $this->assertEquals(array('name' => 'ASC'), $criteria->getOrderings());
        $this->assertNull($criteria->getFirstResult());
        $this->assertEquals(10, $criteria->getMaxResults());
    }

    public function testCompositeExpressionCreation()
    {
        $criteriaArray = array(
            'expression' => array(
                '$or' => array(
                    array('fld' => 'firstName', 'op' => '=', 'val' => 'test'),
                    array('fld' => 'lastName', 'op' => '=', 'val' => 'test')
                )
            ),
        );

        $criteria = $this->criteriaFactory->create($criteriaArray);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\CompositeExpression', $where);
        $this->assertEquals(CompositeExpression::TYPE_OR, $where->getType());
        $this->assertCount(2, $where->getExpressionList());
    }

    public function testExceptionIsRaisedInCaseOfInvalidExpression()
    {
        $criteriaArray = array(
            'expression' => 'invalid',
        );

        $this->setExpectedException(
            'Doctrine\Common\Collections\Exception\InvalidCriteriaArrayException',
            'Criteria expression must be an array; received "string"'
        );

        $this->criteriaFactory->create($criteriaArray);
    }

    public function testExceptionIsRaisedInCaseOfInvalidCompositeExpression()
    {
        $criteriaArray = array(
            'expression' => array('$or' => 'invalid'),
        );

        $this->setExpectedException(
            'Doctrine\Common\Collections\Exception\InvalidCriteriaArrayException',
            'Criteria composite expression must be an array; received "string"'
        );

        $this->criteriaFactory->create($criteriaArray);
    }

    public function testExceptionIsRaisedInCaseOfInvalidExpressionArraySyntax()
    {
        $criteriaArray = array(
            'expression' => array('fld' => 'name', 'foo' => 'bar'),
        );

        $this->setExpectedException(
            'Doctrine\Common\Collections\Exception\InvalidCriteriaArrayException',
            'Criteria expression array contains invalid fields'
        );

        $this->criteriaFactory->create($criteriaArray);
    }

    public function testExceptionIsRaisedInCaseOfInvalidOrderings()
    {
        $criteriaArray = array(
            'orderings' => 'invalid',
        );

        $this->setExpectedException(
            'Doctrine\Common\Collections\Exception\InvalidCriteriaArrayException',
            'Criteria orderings must be an array; received "string"'
        );

        $this->criteriaFactory->create($criteriaArray);
    }
}
