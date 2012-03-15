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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Collections\Expr;

/**
 * An Expression visitor walks a graph of expressions and turns them into a
 * query for the underlying implementation.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
abstract class ExpressionVisitor
{
    abstract public function walkComparison(Comparison $comparison);

    abstract public function walkValue(Value $value);

    abstract public function walkCompositeExpression(CompositeExpression $expr);

    public function dispatch(Expression $expr)
    {
        if ($expr instanceof Comparison) {
            return $this->walkComparison($expr);
        } else if ($expr instanceof Value) {
            return $this->walkValue($expr);
        } else if ($expr instanceof CompositeExpression) {
            return $this->walkCompositeExpression($expr);
        }
    }
}

