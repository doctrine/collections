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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\UnmodifiableCollectionView;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class UnmodifiableCollectionViewTest extends \PHPUnit_Framework_TestCase
{
    const KEY_1 = 'Key #1';
    const KEY_2 = 'Key #2';

    const ELEMENT_1 = 'Element #1';
    const ELEMENT_2 = 'Element #2';

    const OFFSET = 5;
    const LENGTH = 10;

    const COUNT = 8;

    /**
     * @var Collection|MockObject
     */
    private $collection;

    /**
     * @var UnmodifiableCollectionView
     */
    private $view;


    protected function setUp()
    {
        $this->collection = $this->getMockForCollection();
        $this->view = new UnmodifiableCollectionView($this->collection);
    }

    protected function tearDown()
    {
        $this->collection = null;
        $this->view = null;
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot add to an unmodifiable collection
     */
    public function testAdd()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->add(self::ELEMENT_1);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot clear an unmodifiable collection
     */
    public function testClear()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->clear();
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot remove from an unmodifiable collection
     */
    public function testRemove()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->remove(self::KEY_1);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot set on an unmodifiable collection
     */
    public function testSet()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->set(self::KEY_1, self::ELEMENT_1);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot remove from an unmodifiable collection
     */
    public function testRemoveElement()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->removeElement(self::ELEMENT_1);
    }

    public function testGet()
    {
        $this->collection
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo(self::KEY_1))
            ->willReturn(self::ELEMENT_1);

        $this->assertSame(self::ELEMENT_1, $this->view->get(self::KEY_1));
    }

    public function testGetKeys()
    {
        $this->collection
            ->expects($this->once())
            ->method('getKeys')
            ->willReturn(array(self::KEY_1, self::KEY_2));

        $this->assertSame(array(self::KEY_1, self::KEY_2), $this->view->getKeys());
    }

    public function testGetValues()
    {
        $this->collection
            ->expects($this->once())
            ->method('getValues')
            ->willReturn(array(self::ELEMENT_1, self::ELEMENT_2));

        $this->assertSame(array(self::ELEMENT_1, self::ELEMENT_2), $this->view->getValues());
    }

    public function testContainsKey()
    {
        $this->collection
            ->expects($this->once())
            ->method('containsKey')
            ->with($this->identicalTo(self::KEY_1))
            ->willReturn(true);

        $this->assertTrue($this->view->containsKey(self::KEY_1));
    }

    public function testContains()
    {
        $this->collection
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo(self::ELEMENT_1))
            ->willReturn(true);

        $this->assertTrue($this->view->contains(self::ELEMENT_1));
    }

    public function testIsEmpty()
    {
        $this->collection
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $this->assertTrue($this->view->isEmpty());
    }

    public function testToArray()
    {
        $this->collection
            ->expects($this->once())
            ->method('toArray')
            ->willReturn(array(self::ELEMENT_1));

        $this->assertSame(array(self::ELEMENT_1), $this->view->toArray());
    }

    public function testFirst()
    {
        $this->collection
            ->expects($this->once())
            ->method('first')
            ->willReturn(self::ELEMENT_1);

        $this->assertSame(self::ELEMENT_1, $this->view->first());
    }

    public function testLast()
    {
        $this->collection
            ->expects($this->once())
            ->method('last')
            ->willReturn(self::ELEMENT_2);

        $this->assertSame(self::ELEMENT_2, $this->view->last());
    }

    public function testKey()
    {
        $this->collection
            ->expects($this->once())
            ->method('key')
            ->willReturn(self::KEY_1);

        $this->assertSame(self::KEY_1, $this->view->key());
    }

    public function testCurrent()
    {
        $this->collection
            ->expects($this->once())
            ->method('current')
            ->willReturn(self::ELEMENT_1);

        $this->assertSame(self::ELEMENT_1, $this->view->current());
    }

    public function testNext()
    {
        $this->collection
            ->expects($this->once())
            ->method('next')
            ->willReturn(self::ELEMENT_2);

        $this->assertSame(self::ELEMENT_2, $this->view->next());
    }

    public function testExists()
    {
        $predicate = $this->getStubForClosure();
        $this->collection
            ->expects($this->once())
            ->method('exists')
            ->with($this->identicalTo($predicate))
            ->willReturn(true);

        $this->assertTrue($this->view->exists($predicate));
    }
    
    public function testFilter()
    {
        $predicate = $this->getStubForClosure();
        $resultCollection = $this->getMockForCollection();
        $this->collection
            ->expects($this->once())
            ->method('filter')
            ->with($this->identicalTo($predicate))
            ->willReturn($resultCollection);

        $this->assertSame($resultCollection, $this->view->filter($predicate));
    }

    public function testForAll()
    {
        $predicate = $this->getStubForClosure();
        $this->collection
            ->expects($this->once())
            ->method('forAll')
            ->with($this->identicalTo($predicate))
            ->willReturn(true);

        $this->assertTrue($this->view->forAll($predicate));
    }

    public function testMap()
    {
        $func = $this->getStubForClosure();
        $resultCollection = $this->getMockForCollection();
        $this->collection
            ->expects($this->once())
            ->method('map')
            ->with($this->identicalTo($func))
            ->willReturn($resultCollection);

        $this->assertSame($resultCollection, $this->view->map($func));
    }

    public function testPartition()
    {
        $predicate = $this->getStubForClosure();
        $result = array(
            $this->getMockForCollection(),
            $this->getMockForCollection(),
        );
        $this->collection
            ->expects($this->once())
            ->method('partition')
            ->with($this->identicalTo($predicate))
            ->willReturn($result);

        $this->assertSame($result, $this->view->partition($predicate));
    }

    public function testIndexOf()
    {
        $this->collection
            ->expects($this->once())
            ->method('indexOf')
            ->with($this->identicalTo(self::ELEMENT_1))
            ->willReturn(self::KEY_1);

        $this->assertSame(self::KEY_1, $this->view->indexOf(self::ELEMENT_1));
    }

    public function testSlice()
    {
        $this->collection
            ->expects($this->once())
            ->method('slice')
            ->with(
                $this->identicalTo(self::OFFSET),
                $this->identicalTo(self::LENGTH)
            )
            ->willReturn(array());

        $this->assertSame(array(), $this->view->slice(self::OFFSET, self::LENGTH));
    }

    public function testGetIterator()
    {
        $iterator = $this->getMock('Iterator');
        $this->collection
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->assertSame($iterator, $this->view->getIterator());
    }

    public function testOffsetExists()
    {
        $this->collection
            ->expects($this->once())
            ->method('offsetExists')
            ->with($this->identicalTo(self::KEY_1))
            ->willReturn(true);

        $this->assertTrue($this->view->offsetExists(self::KEY_1));
    }

    public function testOffsetGet()
    {
        $this->collection
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo(self::KEY_1))
            ->willReturn(self::ELEMENT_1);

        $this->assertSame(self::ELEMENT_1, $this->view->offsetGet(self::KEY_1));
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot set on an unmodifiable collection
     */
    public function testOffsetSet()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->offsetSet(self::KEY_1, self::ELEMENT_1);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot unset from an unmodifiable collection
     */
    public function testOffsetUnset()
    {
        $this->collectionMethodsMustNotBeCalled();
        $this->view->offsetUnset(self::KEY_1);
    }

    public function testCount()
    {
        $this->collection
            ->expects($this->once())
            ->method('count')
            ->willReturn(self::COUNT);

        $this->assertSame(self::COUNT, $this->view->count());
    }

    private function collectionMethodsMustNotBeCalled()
    {
        $this->collection->expects($this->never())->method($this->anything());
    }

    /**
     * @return \Closure|MockObject
     */
    private function getStubForClosure()
    {
        return function() {};
    }

    /**
     * @return Collection|MockObject
     */
    private function getMockForCollection()
    {
        return $this->getMock('Doctrine\Common\Collections\Collection');
    }
}
