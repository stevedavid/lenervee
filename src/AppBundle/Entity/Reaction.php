<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reaction
 *
 * @ORM\Table(name="reaction")
 * @ORM\Entity()
 */
class Reaction
{
    const STATUS_ACCEPTED = 0;
    const STATUS_PENDING = 1;
    const STATUS_MODERATED = 2;
    const STATUS_TRASHED = 3;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="reaction", type="text")
     */
    private $reaction;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true))
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * Thread of this comment
     *
     * @var Courrier
     * @ORM\ManyToOne(targetEntity="Courrier", inversedBy="reactions")
     * @ORM\JoinColumn(name="courrier_id", referencedColumnName="id")
     */
    private $courrier;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->date = new \Datetime();
        $this->status = self::STATUS_PENDING;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Reaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Reaction
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
     * Set reaction
     *
     * @param string $reaction
     * @return Reaction
     */
    public function setReaction($reaction)
    {
        $this->reaction = $reaction;

        return $this;
    }

    /**
     * Get reaction
     *
     * @return string
     */
    public function getReaction()
    {
        return $this->reaction;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Reaction
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Reaction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set courrier
     *
     * @param \AppBundle\Entity\Courrier $courrier
     * @return Reaction
     */
    public function setCourrier(\AppBundle\Entity\Courrier $courrier = null)
    {
        $this->courrier = $courrier;

        return $this;
    }

    /**
     * Get courrier
     *
     * @return \AppBundle\Entity\Courrier
     */
    public function getCourrier()
    {
        return $this->courrier;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Reaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
