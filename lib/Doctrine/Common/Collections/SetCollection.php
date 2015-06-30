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

/**
 * A SetCollection is a Collection implementation that wraps a regular PHP array containing unique items.
 *
 * @author Steffen Roßkamp <sr@blackblizzard.org>
 */
class SetCollection extends ArrayCollection
{
    /**
     * Initializes a new SetCollection.
     *
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        parent::__construct(array());
        foreach ($elements as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        if (!$this->contains($value)) {
            parent::set($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return bool TRUE if the item could be added, FALSE if it already existed.
     */
    public function add($value)
    {
        if (!$this->contains($value)) {
            return parent::add($value);
        }

        return false;
    }
}
