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

namespace Doctrine\Common\Collections\Expr\Closure;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Filterable;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\TestObject;

/**
 * @author Oleksandr Sova <sovaalexandr@gmail.com>
 */
class ClosureMatchClosureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new ExpressionBuilder();
    }

    public function testMatchClosure()
    {
        $expression = $this->buildExpression();
        $container = new ClosureSelection();
        $expression->applyTo($container);
        $closure = $container->getFilter();

        $this->checkExpression($closure);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWrongSelection()
    {
        /** @var Filterable|\PHPUnit_Framework_MockObject_MockObject $selection */
        $selection = $this->getMockBuilder(Filterable::class)
            ->getMockForAbstractClass();
        $expression = $this->buildExpression();
        $expression->applyTo($selection);
    }

    /**
     * @return Comparison
     */
    protected function buildExpression()
    {
        return new MatchClosure(function ($object) {
            return $object instanceof TestObject;
        });
    }

    /**
     * @param \Closure $closure
     */
    protected function checkExpression(\Closure $closure)
    {
        static::assertTrue($closure(new TestObject(1)));
        static::assertFalse($closure(new \stdClass()));
    }
}
