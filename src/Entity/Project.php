<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @UniqueEntity(fields={"name"}, message="Ce nom de projet existe déjà.")
 * @ORM\Table(name="`project`")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le nom du projet ne peut être vide")
     * @Assert\Length(min=2, minMessage="le nom du projet est trop court",
     *     max=100, maxMessage="le nom du projet est trop long")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="projects")
     */
    private $team;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $leader;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    /**
     * Get id Project
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name Project
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    /**
     * Set name Project
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description Project
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description Project
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Team User
     * @return Team
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * Set Team Project
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get create date Project
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
     * @return Project
     */
    public function setCreatedBy(User $creator): self
    {
        $this->createdBy = $creator;

        return $this;
    }

    /**
     * Get leader
     * @return User
     */
    public function getLeader(): ?User
    {
        return $this->leader;
    }

    /**
     * Set creator
     * @param User $leader
     * @return Project
     */
    public function setLeader(User $leader): self
    {
        $this->leader = $leader;

        return $this;
    }
}
