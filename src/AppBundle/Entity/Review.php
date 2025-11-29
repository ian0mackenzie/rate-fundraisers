<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "review")]
#[ORM\Entity(repositoryClass: "AppBundle\Repository\ReviewRepository")]
class Review
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $id;

    #[ORM\Column(name: "rating", type: "smallint")]
    #[Assert\NotBlank()]
    private $rating;

    #[ORM\Column(name: "title", type: "string", length: 255)]
    #[Assert\NotBlank()]
    private $title;

    #[ORM\ManyToOne(targetEntity: "Author", inversedBy: "reviews")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id")]
    private $author;

    #[ORM\ManyToOne(targetEntity: "Fundraiser", inversedBy: "reviews")]
    #[ORM\JoinColumn(name: "fundraiser_id", referencedColumnName: "id")]
    private $fundraiser;

    #[ORM\Column(name: "review", type: "text", nullable: true)]
    private $review;


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
     * Set rating
     *
     * @param integer $rating
     *
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * Set review
     *
     * @param string $review
     *
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return Review
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
     * Set fundraiser
     *
     * @param \AppBundle\Entity\Fundraiser $fundraiser
     *
     * @return Review
     */
    public function setFundraiser(\AppBundle\Entity\Fundraiser $fundraiser = null)
    {
        $this->fundraiser = $fundraiser;

        return $this;
    }

    /**
     * Get fundraiser
     *
     * @return \AppBundle\Entity\Author
     */
    public function getFundraiser()
    {
        return $this->fundraiser;
    }


}
