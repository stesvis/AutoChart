<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="jobs")
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id", onDelete="set null")
     */
    private $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="set null")
     */
    private $task;

    /**
     * @ORM\Column(type="integer")
     */
    private $mileage;

    /**
     * @ORM\Column(type="string")
     */
    private $mileageType;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="set null")
     */
    private $completedBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $completedAt;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

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
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task)
    {
        $this->task = $task;
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
    public function getCompletedBy()
    {
        return $this->completedBy;
    }

    /**
     * @param mixed $completedBy
     */
    public function setCompletedBy($completedBy)
    {
        $this->completedBy = $completedBy;
    }

    /**
     * @return mixed
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * @param mixed $completedAt
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;
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

}
