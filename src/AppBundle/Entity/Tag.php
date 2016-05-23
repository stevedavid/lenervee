<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var Courrier
     *
     * @ORM\ManyToMany(targetEntity="Courrier", inversedBy="tags")
     * @ORM\JoinTable(name="courrier_tag")
     *
     */
    private $courriers;

    public function __construct() {
        $this->courriers= new \Doctrine\Common\Collections\ArrayCollection();
    }

    static public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
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
     * Set name
     *
     * @param string $name
     * @return Tag
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
     * Set slug
     *
     * @param string $slug
     * @return Tag
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add courriers
     *
     * @param \AppBundle\Entity\Courrier $courrier
     * @return Tag
     */
    public function addCourrier(\AppBundle\Entity\Courrier $courrier)
    {
        if (!$this->courriers->contains($courrier)) {
            $this->courriers->add($courrier);
            $courrier->addTag($this);
        }

        return $this;
    }

    /**
     * Remove courriers
     *
     * @param \AppBundle\Entity\Courrier $courrier
     */
    public function removeCourrier(\AppBundle\Entity\Courrier $courrier)
    {
        if ($this->courriers->contains($courrier)) {
            $this->courriers->removeElement($courrier);
            $courrier->removeTag($this);
        }
        return $this;
    }

    /**
     * Get courriers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourriers()
    {
        return $this->courriers;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
