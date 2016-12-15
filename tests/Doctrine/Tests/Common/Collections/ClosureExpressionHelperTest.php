<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * 'AS IS' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
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

use Doctrine\Common\Collections\Expr\ClosureExpressionHelper;
use Doctrine\TestObject;

/**
 * @group DDC-1637
 */
class ClosureExpressionHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetObjectFieldValueIsAccessor()
    {
        $object = new TestObject(1, 2, true);

        static::assertTrue(ClosureExpressionHelper::getObjectFieldValue($object, 'baz'));
    }

    public function testGetObjectFieldValueIsAccessorCamelCase()
    {
        $object = new TestObjectNotCamelCase(1);

        static::assertEquals(1, ClosureExpressionHelper::getObjectFieldValue($object, 'foo_bar'));
        static::assertEquals(1, ClosureExpressionHelper::getObjectFieldValue($object, 'foobar'));
        static::assertEquals(1, ClosureExpressionHelper::getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBoth()
    {
        $object = new TestObjectBothCamelCaseAndUnderscore(1, 2);

        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foo_bar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foobar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorOnePublic()
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foo_bar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foobar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueIsAccessorBothPublic()
    {
        $object = new TestObjectPublicCamelCaseAndPrivateUnderscore(1, 2);

        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foo_bar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'foobar'));
        static::assertEquals(2, ClosureExpressionHelper::getObjectFieldValue($object, 'fooBar'));
    }

    public function testGetObjectFieldValueMagicCallMethod()
    {
        $object = new TestObject(1, 2, true, 3);

        static::assertEquals(3, ClosureExpressionHelper::getObjectFieldValue($object, 'qux'));
    }

    public function testGetObjectFieldValueArrayAccess()
    {
        $object = new \ArrayObject(['foo' => 'bar']);
        static::assertEquals('bar', ClosureExpressionHelper::getObjectFieldValue($object, 'foo'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage No way found to access value of field 'anyField' on stdClass
     */
    public function testNoFieldValueFound()
    {
        $object = new \stdClass();
        static::assertNull(ClosureExpressionHelper::getObjectFieldValue($object, 'anyField'));
    }

    public function testSortByFieldAscending()
    {
        /** @var TestObject[] $objects */
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort = ClosureExpressionHelper::sortByField('foo');

        usort($objects, $sort);

        static::assertEquals('a', $objects[0]->getFoo());
        static::assertEquals('b', $objects[1]->getFoo());
        static::assertEquals('c', $objects[2]->getFoo());
    }

    public function testSortByFieldDescending()
    {
        /** @var TestObject[] $objects */
        $objects = [new TestObject('b'), new TestObject('a'), new TestObject('c')];
        $sort = ClosureExpressionHelper::sortByField('foo', -1);

        usort($objects, $sort);

        static::assertEquals('c', $objects[0]->getFoo());
        static::assertEquals('b', $objects[1]->getFoo());
        static::assertEquals('a', $objects[2]->getFoo());
    }

    public function testSortByFieldTwoEqual()
    {
        /** @var TestObject[] $objects */
        $objects = [new TestObject('a'), new TestObject('a'), new TestObject('c')];
        $sort = ClosureExpressionHelper::sortByField('foo');

        usort($objects, $sort);

        static::assertEquals('a', $objects[0]->getFoo());
        static::assertEquals('a', $objects[1]->getFoo());
        static::assertEquals('c', $objects[2]->getFoo());
    }

    public function testSortDelegate()
    {
        /** @var TestObject[] $objects */
        $objects = [new TestObject('a', 'c'), new TestObject('a', 'b'), new TestObject('a', 'a')];
        $sort = ClosureExpressionHelper::sortByField('bar', 1);
        $sort = ClosureExpressionHelper::sortByField('foo', 1, $sort);

        usort($objects, $sort);

        static::assertEquals('a', $objects[0]->getBar());
        static::assertEquals('b', $objects[1]->getBar());
        static::assertEquals('c', $objects[2]->getBar());
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
