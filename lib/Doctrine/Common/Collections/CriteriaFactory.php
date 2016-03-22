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

namespace Doctrine\Common\Collections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Exception;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CriteriaFactory
{
    public function create(array $criteria)
    {
        $expression = $orderings = $firstResult = $maxResults = $page = null;

        if (isset($criteria['expression'])) {
            $expression = $this->buildExpression($criteria['expression']);
        }

        if (isset($criteria['orderings'])) {
            if (! is_array($criteria['orderings'])) {
                throw Exception\InvalidCriteriaArrayException::fromInvalidOrderings($criteria['orderings']);
            }

            $orderings = $criteria['orderings'];
        }

        if (isset($criteria['first_result'])) {
            $firstResult = $criteria['first_result'];
        }

        if (isset($criteria['max_results'])) {
            $maxResults = $criteria['max_results'];
        }

        return new Criteria($expression, $orderings, $firstResult, $maxResults);
    }

    protected function buildExpression($expression)
    {
        if (! is_array($expression)) {
            throw Exception\InvalidCriteriaArrayException::fromInvalidExpression($expression);
        }

        if (
            isset($expression['fld'])
            && isset($expression['op'])
            && isset($expression['val'])
        ) {
            return new Comparison($expression['fld'], $expression['op'], $expression['val']);
        }

        $expressionsArray = $expression;
        $compositeExpressionType = CompositeExpression::TYPE_AND;

        if (isset($expression['$or'])) {
            if (! is_array($expression['$or'])) {
                throw Exception\InvalidCriteriaArrayException::fromInvalidCompositeExpression($expression['$or']);
            }

            $expressionsArray = $expression['$or'];
            $compositeExpressionType = CompositeExpression::TYPE_OR;
        } elseif (isset($expression['$and'])) {
            if (! is_array($expression['$and'])) {
                throw Exception\InvalidCriteriaArrayException::fromInvalidCompositeExpression($expression['$and']);
            }

            $expressionsArray = $expression['$and'];
        }

        $expressions = array();
        foreach ($expressionsArray as $expr) {
            $expressions[] = $this->buildExpression($expr);
        }

        return new CompositeExpression($compositeExpressionType, $expressions);
    }
}
