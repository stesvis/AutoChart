<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="vehicles")
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Please enter the year")
     */
    private $year;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter the make")
     */
    private $make;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter the model")
     */
    private $model;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $trim;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $transmissionType;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fuelType;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $engineSize;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passengers;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mileage;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mileageType;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $color;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $purchasedAt;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $annualMileage;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
     */
    private $createdBy;
    /**
     * @ORM\Column(type="datetime")
     */
    private $modifiedAt;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
     */
    private $modifiedBy;
    /**
     * @ORM\Column(type="string")
     */
    private $status;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Service", mappedBy="vehicle")
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * @param mixed $make
     */
    public function setMake($make)
    {
        $this->make = $make;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getMileage()
    {
        return $this->mileage;
    }

    /**
     * @param mixed $mileage
     */
    public function setMileage($mileage)
    {
        $this->mileage = $mileage;
    }

    /**
     * @return mixed
     */
    public function getTrim()
    {
        return $this->trim;
    }

    /**
     * @param mixed $trim
     */
    public function setTrim($trim)
    {
        $this->trim = $trim;
    }

    /**
     * @return mixed
     */
    public function getTransmissionType()
    {
        return $this->transmissionType;
    }

    /**
     * @param mixed $transmissionType
     */
    public function setTransmissionType($transmissionType)
    {
        $this->transmissionType = $transmissionType;
    }

    /**
     * @return mixed
     */
    public function getFuelType()
    {
        return $this->fuelType;
    }

    /**
     * @param mixed $fuelType
     */
    public function setFuelType($fuelType)
    {
        $this->fuelType = $fuelType;
    }

    /**
     * @return mixed
     */
    public function getEngineSize()
    {
        return $this->engineSize;
    }

    /**
     * @param mixed $engineSize
     */
    public function setEngineSize($engineSize)
    {
        $this->engineSize = $engineSize;
    }

    /**
     * @return mixed
     */
    public function getPassengers()
    {
        return $this->passengers;
    }

    /**
     * @param mixed $passengers
     */
    public function setPassengers($passengers)
    {
        $this->passengers = $passengers;
    }

    /**
     * @return mixed
     */
    public function getMileageType()
    {
        return $this->mileageType;
    }

    /**
     * @param mixed $mileageType
     */
    public function setMileageType($mileageType)
    {
        $this->mileageType = $mileageType;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

    /**
     * @param mixed $purchasedAt
     */
    public function setPurchasedAt($purchasedAt)
    {
        $this->purchasedAt = $purchasedAt;
    }

    /**
     * @return mixed
     */
    public function getAnnualMileage()
    {
        return $this->annualMileage;
    }

    /**
     * @param mixed $annualMileage
     */
    public function setAnnualMileage($annualMileage)
    {
        $this->annualMileage = $annualMileage;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return mixed
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param mixed $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

}
