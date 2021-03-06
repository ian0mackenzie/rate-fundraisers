<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Author
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 *@ORM\HasLifecycleCallbacks
  */
//* @UniqueEntity(fields={"email"}, message="Note: author already exists")

class Author extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="author")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="Fundraiser", mappedBy="author")
     */
    private $fundraisers;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->fundraisers = new ArrayCollection();
    }

     /**
     * @ORM\PrePersist()
     * 
     */

    public function prePersist()
    {
        $this->createdDate = new \DateTime;
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
     * Set email
     *
     * @param string $email
     *
     * @return Author
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Author
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Author
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Author
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
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return Author
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
     * Add fundraiser
     *
     * @param \AppBundle\Entity\Fundraiser $fundraiser
     *
     * @return Author
     */
    public function addFundraiser(\AppBundle\Entity\Fundraiser $fundraiser)
    {
        $this->fundraisers[] = $fundraiser;

        return $this;
    }

    /**
     * Remove fundraiser
     *
     * @param \AppBundle\Entity\Fundraiser $fundraiser
     */
    public function removeFundraiser(\AppBundle\Entity\Fundraiser $fundraiser)
    {
        $this->fundraisers->removeElement($fundraiser);
    }

    /**
     * Get fundraisers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFundraisers()
    {
        return $this->fundraisers;
    }

    /**
     *Return boolean depending on if the author has already reviewed the fundraiser
     * @param \AppBundle\Entity\Author $author
     * @return bool
     */
    public function hasFundraiser(\AppBundle\Entity\Fundraiser $fundraiser)
    {
        return $this->getFundraisers()->contains($fundraiser);
    }

    public function __toString() {
        return $this->email;
    }

}
