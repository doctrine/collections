<?php

namespace Doctrine\Tests\Entity;

use Doctrine\Tests\Entity\Address;

class Person
{
    private $name;
    private $surname;
    private $address;

    public function __construct($name = null, $surname = null, Address $address = null)
    {
        $this->setName($name);
        $this->setSurname($surname);
        $this->setAddress($address);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setAddress(Address $address = null)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function composeFullName()
    {
        return "{$this->getName()} {$this->getSurname()}";
    }

    public function hasName()
    {
        return (strlen($this->getName()) > 0);
    }

    public function hasSurname()
    {
        return (strlen($this->getSurname()) > 0);
    }
}
