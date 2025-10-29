<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Author
 */
#[ORM\Table(name: "author")]
#[ORM\Entity(repositoryClass: "AppBundle\Repository\AuthorRepository")]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ["email"], message: "Note: author already exists")]
class Author implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string")]
    private ?string $password = null;

    /**
     * @var string
     */
    #[ORM\Column(name: "first_name", type: "string", length: 255)]
    #[Assert\NotBlank]
    private $firstName;

    /**
     * @var string
     */
    #[ORM\Column(name: "last_name", type: "string", length: 255)]
    #[Assert\NotBlank]
    private $lastName;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: "created_date", type: "datetime")]
    private $createdDate;

    #[ORM\OneToMany(targetEntity: "Review", mappedBy: "author")]
    private $reviews;

    #[ORM\OneToMany(targetEntity: "Fundraiser", mappedBy: "author")]
    private $fundraisers;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->fundraisers = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdDate = new \DateTime;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Author
     */
    public function setFirstName($firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName(): ?string
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
    public function setLastName($lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName(): ?string
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
    public function setCreatedDate($createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate(): ?\DateTime
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
    public function addReview(\AppBundle\Entity\Review $review): self
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review): void
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
    public function addFundraiser(\AppBundle\Entity\Fundraiser $fundraiser): self
    {
        $this->fundraisers[] = $fundraiser;

        return $this;
    }

    /**
     * Remove fundraiser
     *
     * @param \AppBundle\Entity\Fundraiser $fundraiser
     */
    public function removeFundraiser(\AppBundle\Entity\Fundraiser $fundraiser): void
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
    public function hasFundraiser(\AppBundle\Entity\Fundraiser $fundraiser): bool
    {
        return $this->getFundraisers()->contains($fundraiser);
    }

    public function __toString(): string
    {
        return $this->email ?? '';
    }
}
