<?php

namespace AppBundle\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Admin\SessionRepository")
 */
class Session
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="session_token", type="string", length=255, unique=true)
     */
    private $sessionToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_date", type="datetime")
     */
    private $loginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=255)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     */
    private $ip;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="sessions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->loginDate = new \DateTime();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sessionToken
     *
     * @param string $sessionToken
     * @return Session
     */
    public function setSessionToken($sessionToken)
    {
        $this->sessionToken = $sessionToken;

        return $this;
    }

    /**
     * Get sessionToken
     *
     * @return string 
     */
    public function getSessionToken()
    {
        return $this->sessionToken;
    }

    /**
     * Set loginDate
     *
     * @param \DateTime $loginDate
     * @return Session
     */
    public function setLoginDate($loginDate)
    {
        $this->loginDate = $loginDate;

        return $this;
    }

    /**
     * Get loginDate
     *
     * @return \DateTime 
     */
    public function getLoginDate()
    {
        return $this->loginDate;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     * @return Session
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Session
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return Session
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }


}
