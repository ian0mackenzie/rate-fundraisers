<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: "fundraiser")]
#[ORM\Entity(repositoryClass: "AppBundle\Repository\FundraiserRepository")]
#[UniqueEntity(fields: ["name"], message: "Note: That fundraiser already existed.")]
class Fundraiser
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $id;

    #[ORM\Column(name: "name", type: "string", length: 255, unique: true)]
    #[Assert\NotBlank()]
    private $name;

    #[ORM\Column(name: "description", type: "text", nullable: true)]
    #[Assert\NotBlank()]
    private $description;

    #[ORM\Column(name: "thumbnail", type: "string", length: 255, nullable: true)]
    private $thumbnail;

    #[ORM\Column(name: "created_date", type: "datetime")]
    private $createdDate;

    #[ORM\ManyToOne(targetEntity: "Author", inversedBy: "fundraisers", cascade: ["persist"])]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id")]
    #[Assert\Valid()]
    private $author;

    #[ORM\OneToMany(targetEntity: "Review", mappedBy: "fundraiser")]
    private $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Fundraiser
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
     * Set description
     *
     * @param string $description
     *
     * @return Fundraiser
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     *
     * @return Fundraiser
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Fundraiser
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }



    /**
     * Set author
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return Fundraiser
     */
    public function setAuthor(\AppBundle\Entity\Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return Fundraiser
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Calculate and return the average rating for this fundraiser
     *
     * @return float
     */
    public function getAvgRating(): float
    {
        $reviews = $this->getReviews();
        
        if ($reviews->isEmpty()) {
            return 0.0;
        }
        
        $total = 0;
        foreach ($reviews as $review) {
            $total += $review->getRating();
        }
        
        return round($total / $reviews->count(), 1);
    }

    public function __toString() {
        return $this->name;
    }

}
