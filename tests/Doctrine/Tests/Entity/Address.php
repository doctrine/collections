<?php

namespace Doctrine\Tests\Entity;

class Address
{
    private $street;
    private $city;

    public function __construct($street = null, $city = null)
    {
        $this->setStreet($street);
    }

    public function setStreet($street)
    {
        $this->street = $street;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }
}