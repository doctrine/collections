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

namespace Doctrine\Common\Collections\Expr;

/**
 * Expression of Expressions combined by AND or OR operation.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.3
 */
abstract class Composition implements Expression
{
    /**
     * @var Expression[]
     */
    private $expressions = array();

    /**
     * @param array  $expressions
     *
     * @throws \RuntimeException
     */
    final public function __construct(array $expressions)
    {
        foreach ($expressions as $expr) {
            if ( ! ($expr instanceof Expression)) {
                throw new \RuntimeException('No expression given to Composition.');
            }

            $this->expressions[] = $expr;
        }
    }

    /**
     * @param Filterable $selection
     */
    final public function applyTo(Filterable $selection)
    {
        $joinedSelection = $this->joinBy($selection);
        foreach ($this->expressions as $expression) {
            $expression->applyTo($joinedSelection);
        }
    }

    /**
     * @param Composable|Filterable $selection
     * @return Filterable
     */
    abstract protected function joinBy(Composable $selection);
}
