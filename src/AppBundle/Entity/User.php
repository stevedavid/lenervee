<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Courrier
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Admin\Session", mappedBy="user")
     */
    private $sessions;

    public function __construct()
    {
        parent::__construct();
        $this->sessions = new ArrayCollection();
    }
}