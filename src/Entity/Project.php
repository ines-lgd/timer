<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use DateTime;
use DateTimeInterface;
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
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $leader;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="projects")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity=Timer::class, mappedBy="project")
     */
    private $timers;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
        $this->updatedAt = new DateTime();
        $this->createdAt = new DateTime();
        $this->timers = new ArrayCollection();
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
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
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

    /**
     * Get last update date
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set last update date
     * @return $this
     */
    public function setUpdatedAt(): self
    {
        date_default_timezone_set('Europe/Paris');
        $this->updatedAt = new DateTime();

        return $this;
    }

    /**
     * @return Collection|Timer[]
     */
    public function getTimers(): Collection
    {
        return $this->timers;
    }

    /**
     * Add new Timer in Project
     * @param Timer $timer
     * @return $this
     */
    public function addTimer(Timer $timer): self
    {
        if (!$this->timers->contains($timer)) {
            $this->timers[] = $timer;
            $timer->setProject($this);
        }

        return $this;
    }

    /**
     * Remove Timer in Project
     * @param Timer $timer
     * @return $this
     */
    public function removeTimer(Timer $timer): self
    {
        if ($this->timers->contains($timer)) {
            $this->timers->removeElement($timer);
            // set the owning side to null (unless already changed)
            if ($timer->getProject() === $this) {
                $timer->setProject(null);
            }
        }

        return $this;
    }
}
