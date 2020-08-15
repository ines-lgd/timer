<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @UniqueEntity(fields={"name"}, message="Ce nom de groupe existe déjà.")
 * @ORM\Table(name="`team`")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le nom du groupe ne peut être vide")
     * @Assert\Length(min=2, minMessage="le nom du groupe est trop court",
     *     max=100, maxMessage="le nom du groupe est trop long")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="teams")
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->users = new ArrayCollection();
    }

    /**
     * Get id Team
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name Team
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name Team
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Users Team
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Add User Team
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * Remove User Team
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * Get create date Team
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get creator
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * Set creator
     * @param User $creator
     * @return Team
     */
    public function setCreatedBy(User $creator): self
    {
         $this->createdBy = $creator;

         return $this;
    }
}
