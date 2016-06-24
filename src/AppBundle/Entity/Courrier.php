<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Tag;
use Eko\FeedBundle\Item\Writer\RoutedItemInterface;


/**
 * Courrier
 *
 * @ORM\Table(name="courrier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourrierRepository")
 */
class Courrier implements RoutedItemInterface
{
    const PRIVATE_PREFIX = 'Privé:';
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
     * @var \DateTime
     *
     * @ORM\Column(name="envoi", type="datetime")
     */
    private $envoi;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="text")
     */
    private $intro;

    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="courrier", type="text")
     */
    private $courrier;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse", type="text", nullable=true)
     */
    private $reponse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var integer
     *
     * @ORM\Column(name="like_count", type="integer", nullable=true)
     */
    private $likeCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recu", type="datetime", nullable=true)
     */
    private $recu;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="courriers")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     */
    private $categorie;

    /**
     *
     * @ORM\OneToMany(targetEntity="Reaction", mappedBy="courrier", cascade={"remove"})
     */
    private $reactions;

    /**
     * @var Tags
     *
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="courriers", cascade={"persist", "remove"})
     */
    private $tags;

    /**
     * @var string
     */
    private $url;

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
     * @return Courrier
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
     * Set intro
     *
     * @param string $intro
     * @return Courrier
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * Get intro
     *
     * @return string
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Courrier
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Courrier
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
     * Set categorie
     *
     * @param \AppBundle\Entity\Categorie $categorie
     * @return Courrier
     */
    public function setCategorie(\AppBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Get reponse
     *
     * @return \AppBundle\Entity\Reponse
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set envoi
     *
     * @param \DateTime $envoi
     * @return Courrier
     */
    public function setEnvoi($envoi)
    {
        $this->envoi = $envoi;

        return $this;
    }

    /**
     * Get envoi
     *
     * @return \DateTime
     */
    public function getEnvoi()
    {
        return $this->envoi;
    }

    /**
     * Set courrier
     *
     * @param string $courrier
     * @return Courrier
     */
    public function setCourrier($courrier)
    {
        $this->courrier = $courrier;

        return $this;
    }

    /**
     * Get courrier
     *
     * @return string
     */
    public function getCourrier()
    {
        return $this->courrier;
    }

    /**
     * Set recu
     *
     * @param \DateTime $recu
     * @return Courrier
     */
    public function setRecu($recu)
    {
        $this->recu = $recu;

        return $this;
    }

    /**
     * Get recu
     *
     * @return \DateTime
     */
    public function getRecu()
    {
        return $this->recu;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set reponse
     *
     * @param string $reponse
     * @return Courrier
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags= new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reactions
     *
     * @param \AppBundle\Entity\Reaction $reactions
     * @return Courrier
     */
    public function addReaction(\AppBundle\Entity\Reaction $reactions)
    {
        $this->reactions[] = $reactions;

        return $this;
    }

    /**
     * Remove reactions
     *
     * @param \AppBundle\Entity\Reaction $reactions
     */
    public function removeReaction(\AppBundle\Entity\Reaction $reactions)
    {
        $this->reactions->removeElement($reactions);
    }

    /**
     * Get reactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReactions()
    {
        return $this->reactions;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\Tag $tags
     * @return Courrier
     */
    public function addTag(\AppBundle\Entity\Tag $tags)
    {
        if (!$this->tags->contains($tags)) {
            $this->tags->add($tags);
            $tags->addCourrier($this);
        }

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\Tag $tags
     */
    public function removeTag(\AppBundle\Entity\Tag $tags)
    {
        if ($this->tags->contains($tags)) {
            $this->tags->removeElement($tags);
            $tags->removeCourrier($this);
        }
        return $this;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function resetTags()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Courrier
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set likeCount
     *
     * @param integer $likeCount
     * @return Courrier
     */
    public function setLikeCount($likeCount)
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    /**
     * Get likeCount
     *
     * @return integer
     */
    public function getLikeCount()
    {
        return $this->likeCount;
    }

    public function getFormattedLikes()
    {
        $n = $this->likeCount;

        $d = $n < 1000000 ? 1000 : 1000000;

        $f = round($n / $d, 1);

        return
            $n < 1000 ?
            number_format($n, 0, ',', ' ') :
            number_format($f, $f - intval($f) ? 1 : 0, ',', ' ') . ($d == 1000 ? 'k' : 'M')
        ;
    }

    public function getFeedItemTitle()
    {
        return $this->name;
    }

    public function getFeedItemDescription()
    {
        return $this->intro;
    }

    public function getFeedItemPubDate()
    {
        return $this->envoi;
    }

    public function getFeedItemRouteName()
    {
        return 'courrier_voir';
    }

    public function getFeedItemRouteParameters()
    {
        return [
            'slugCourrier' => $this->slug,
            'slugCategorie' => $this->categorie->getSlug(),
        ];
    }

    public function getFeedItemUrlAnchor()
    {
        return '';
    }
}
