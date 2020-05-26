<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={'email'}, message='Un compte existe déjà avec cette adresse e-mail')
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type='integer')
     */
    private $id;

    /**
     * @ORM\Column(type='string', length=255)
     * @Assert\NotBlank(message='Le nom ne peut être vide')
     * @Assert\Length(min=2, minMessage='le nom est trop court',
     *     max=100, maxMessage='le nom est trop long')
     */
    private $lastName;

    /**
     * @ORM\Column(type='string', length=255)
     * @Assert\NotBlank(message='Le prénom ne peut être vide')
     * @Assert\Length(min=2, minMessage='le prénom est trop court',
     *     max=100, maxMessage='le prénom est trop long')
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message='Le pseudo ne peut être vide')
     * @Assert\Length(min=2, minMessage='le pseudo est trop court',
     *     max=100, maxMessage='le pseudo est trop long')
     * @ORM\Column(type='string', length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type='string', length=255, unique=true)
     * @Assert\NotBlank(message='L’adresse e-mail ne peut être vide')
     * @Assert\Email(message='L’adresse e-mail doit être valide')
     * @Assert\Length(min=5, minMessage='L’adresse e-mail est trop court',
     *     max=255, maxMessage='L’adresse e-mail est trop long')
     */
    private $email;

    /**
     * @ORM\Column(type='string', length=255)
     * @Assert\NotBlank(message='Le mot de passe ne peut être vide')
     * @Assert\Length(min=5, minMessage='Le mot de passe est trop court',
     *     max=255, maxMessage='Le mot de passe est trop long')
     */
    private $password;

    /**
     * @ORM\Column(type='datetime')
     */
    private $createdAt;

    /**
     * @ORM\Column(type='simple_array')
     */
    private $roles;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id User
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get last name User
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Set last name User
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get first name User
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Set first name User
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get pseudo User
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Set pseudo User
     * @param string $pseudo
     * @return $this
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get email User
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email User
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get create date User
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set create date User
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get roles User
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get password User
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get last name and first name User
     * @return string
     */
    public function getUsername()
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }
}
