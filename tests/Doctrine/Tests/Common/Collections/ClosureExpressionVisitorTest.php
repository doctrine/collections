<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\ExpressionBuilder;

/**
 * @group DDC-1637
 */
class ClosureExpressionVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClosureExpressionVisitor
     */
    private $visitor;

    /**
     * @var ExpressionBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->visitor = new ClosureExpressionVisitor();
        $this->builder = new ExpressionBuilder();
    }

    public function testGetObjectFieldValueIsAccessor()
    {
        $object = new TestObject(1, 2, true);

        $this->assertTrue($this->visitor->getObjectFieldValue($object, 'baz'));
    }

    public function testGetObjectFieldValueIsAccessorCamelCase()
    {
        $object = new TestObjectNotCamelCase(1);

        $this->assertEquals(1, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        $this->assertEquals(1, $this->visitor->getObjectFieldValue($object, 'foobar'));
        $this->assertEquals(1, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBoth()
    {
        $object = new TestObjectBothCamelCaseAndUnderscore(1, 2);

        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorOnePublic()
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBothPublic()
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foo_bar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'foobar'));
        $this->assertEquals(2, $this->visitor->getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueMagicCallMethod()
    {
        $object = new TestObject(1, 2, true, 3);

        $this->assertEquals(3, $this->visitor->getObjectFieldValue($object, 'qux'));
    }

    public function testWalkEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->eq("foo", 1));

        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(2)));

        $closure = $this->visitor->walkComparison($this->builder->eq("foo", "ignorecase", true));
        $this->assertTrue($closure(new TestObject("ignoreCase")));

        $closure = $this->visitor->walkComparison($this->builder->eq("foo", "ignorecase", false));
        $this->assertFalse($closure(new TestObject("ignoreCase")));
    }

    public function testWalkNotEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->neq("foo", 1));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(2)));

        $closure = $this->visitor->walkComparison($this->builder->neq("foo", "ignorecase", true));
        $this->assertFalse($closure(new TestObject("ignoreCase")));

        $closure = $this->visitor->walkComparison($this->builder->neq("foo", "ignorecase", false));
        $this->assertTrue($closure(new TestObject("ignoreCase")));
    }

    public function testWalkLessThanComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->lt("foo", 1));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(0)));

        $closure = $this->visitor->walkComparison($this->builder->lt("foo", 1));

        $this->assertFalse($closure(new TestObject(1)));
    }

    public function testWalkLessThanEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->lte("foo", 1));

        $this->assertFalse($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->gte("foo", 1));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->gt("foo", 1));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertFalse($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));
    }

    public function testWalkInComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->in("foo", array(1, 2, 3)));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));

        $closure = $this->visitor->walkComparison($this->builder->in("foo", array(1, 'ignorecase', 3), true));
        $this->assertTrue($closure(new TestObject('ignoreCase')));

        $closure = $this->visitor->walkComparison($this->builder->in("foo", array(1, 'ignorecase', 3)), false);
        $this->assertFalse($closure(new TestObject('ignoreCase')));

    }

    public function testWalkNotInComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->notIn("foo", array(1, 2, 3)));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(0)));
        $this->assertTrue($closure(new TestObject(4)));

        $closure = $this->visitor->walkComparison($this->builder->notIn("foo", array(1, 'ignorecase', 3), true));
        $this->assertFalse($closure(new TestObject('ignoreCase')));

        $closure = $this->visitor->walkComparison($this->builder->notIn("foo", array(1, 'ignorecase', 3)), false);
        $this->assertTrue($closure(new TestObject('ignoreCase')));
    }

    public function testWalkContainsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'hello'));

        $this->assertTrue($closure(new TestObject('hello world')));
        $this->assertFalse($closure(new TestObject('world')));

        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'ignorecase', true));
        $this->assertTrue($closure(new TestObject('ignoreCase')));

        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'ignorecase', false));
        $this->assertFalse($closure(new TestObject('ignoreCase')));

    }

    public function testWalkMemberOfComparisonWithObject()
    {
        $closure = $this->visitor->walkComparison($this->builder->memberof("foo", 2));

        $this->assertTrue($closure(new TestObject(array(1,2,3))));
        $this->assertTrue($closure(new TestObject(array(2))));
        $this->assertFalse($closure(new TestObject(array(1,3,5))));
    }

    public function testWalkStartsWithComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->startsWith('foo', 'hello'));

        $this->assertTrue($closure(new TestObject('hello world')));
        $this->assertFalse($closure(new TestObject('world')));
    }

    public function testWalkEndsWithComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->endsWith('foo', 'world'));

        $this->assertTrue($closure(new TestObject('hello world')));
        $this->assertFalse($closure(new TestObject('hello')));
    }

    public function testWalkAndCompositeExpression()
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->andX(
                $this->builder->eq("foo", 1),
                $this->builder->eq("bar", 1)
            )
        );

        $this->assertTrue($closure(new TestObject(1, 1)));
        $this->assertFalse($closure(new TestObject(1, 0)));
        $this->assertFalse($closure(new TestObject(0, 1)));
        $this->assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkOrCompositeExpression()
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->orX(
                $this->builder->eq("foo", 1),
                $this->builder->eq("bar", 1)
            )
        );

        $this->assertTrue($closure(new TestObject(1, 1)));
        $this->assertTrue($closure(new TestObject(1, 0)));
        $this->assertTrue($closure(new TestObject(0, 1)));
        $this->assertFalse($closure(new TestObject(0, 0)));
    }

    public function testSortByFieldAscending()
    {
        $objects = array(new TestObject("b"), new TestObject("a"), new TestObject("c"));
        $sort = ClosureExpressionVisitor::sortByField("foo");

        usort($objects, $sort);

        $this->assertEquals("a", $objects[0]->getFoo());
        $this->assertEquals("b", $objects[1]->getFoo());
        $this->assertEquals("c", $objects[2]->getFoo());
    }

    public function testSortByFieldDescending()
    {
        $objects = array(new TestObject("b"), new TestObject("a"), new TestObject("c"));
        $sort = ClosureExpressionVisitor::sortByField("foo", -1);

        usort($objects, $sort);

        $this->assertEquals("c", $objects[0]->getFoo());
        $this->assertEquals("b", $objects[1]->getFoo());
        $this->assertEquals("a", $objects[2]->getFoo());
    }

    public function testSortDelegate()
    {
        $objects = array(new TestObject("a", "c"), new TestObject("a", "b"), new TestObject("a", "a"));
        $sort = ClosureExpressionVisitor::sortByField("bar", 1);
        $sort = ClosureExpressionVisitor::sortByField("foo", 1, $sort);

        usort($objects, $sort);

        $this->assertEquals("a", $objects[0]->getBar());
        $this->assertEquals("b", $objects[1]->getBar());
        $this->assertEquals("c", $objects[2]->getBar());
    }

    public function testArrayComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->eq("foo", 42));

        $this->assertTrue($closure(array('foo' => 42)));
    }

    public function testToLower() {
        $this->assertEquals('hello', $this->visitor->toLower('Hello'));

        $input = array(1, 'Hello', array('Hello'));
        $output = array(1, 'hello', array('hello'));
        $this->assertEquals($output, $this->visitor->toLower($input));

        $input = new \stdClass();
        $input->foo = "Foo";
        $output = new \stdClass();
        $output->foo = "foo";
        $this->assertEquals($output, $this->visitor->toLower($input));
    }

}

class TestObject
{
    private $foo;
    private $bar;
    private $baz;
    private $qux;

    public function __construct($foo = null, $bar = null, $baz = null, $qux = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
        $this->qux = $qux;
    }

    public function __call($name, $arguments)
    {
        if ('getqux' === $name) {
            return $this->qux;
        }
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function isBaz()
    {
        return $this->baz;
    }
}

class TestObjectNotCamelCase
{
    private $foo_bar;

    public function __construct($foo_bar = null)
    {
        $this->foo_bar = $foo_bar;
    }

    public function getFooBar()
    {
        return $this->foo_bar;
    }
}

class TestObjectBothCamelCaseAndUnderscore
{
    private $foo_bar;
    private $fooBar;

    public function __construct($foo_bar = null, $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->fooBar;
    }
}

class TestObjectPublicCamelCaseAndPrivateUnderscore
{
    private $foo_bar;
    public $fooBar;

    public function __construct($foo_bar = null, $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->fooBar;
    }
}

class TestObjectBothPublic
{
    public $foo_bar;
    public $fooBar;

    public function __construct($foo_bar = null, $fooBar = null)
    {
        $this->foo_bar = $foo_bar;
        $this->fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->foo_bar;
    }
}
