<?php

namespace App\Entity;

use App\Repository\TimerRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TimerRepository::class)
 */
class Timer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du Timer ne peut être vide")
     * @Assert\Length(min=2, minMessage="Le nom est trop court",
     *      max=255, maxMessage="Le n est trop long")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="timers")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="timer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->status = false;
        date_default_timezone_set("Europe/Paris");
        $this->createdAt = new DateTime();
    }

    /**
     * Run Timer
     */
    public function run()
    {
        if (empty($this->getStart())) {
            date_default_timezone_set("Europe/Paris");
            $this->setStatus(true);
            $this->setStart(new DateTime());
            $this->getProject()->setUpdatedAt();
        }
    }

    /**
     * Stop Timer
     */
    public function stop()
    {
        if (empty($this->getEnd())) {
            date_default_timezone_set("Europe/Paris");
            $this->setStatus(false);
            $this->setEnd(new DateTime());
            $this->getProject()->setUpdatedAt();
        }
    }

    /**
     * Reset Timer
     */
    public function reset()
    {
        if(!empty($this->start)) {
            date_default_timezone_set("Europe/Paris");
            $this->setStatus(false);
            $this->setStart(null);
            $this->setEnd(null);
            $this->getProject()->setUpdatedAt();
        }
    }

    /**
     * Get
     * @return mixed
     */
    public function getTime()
    {
        if (!$this->end)
        {
            return "";
        }

        $time = $this->end->diff($this->start);

        return $time->format('%H:%i:%s');
    }

    /**
     * Get id Timer
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get status Timer in string
     * @return string|null
     */
    public function getStatusToString(): ?string
    {

        if ($this->getStatus()) {
            $status = 'EN COURS';
        }
        else if (!$this->getStatus() && $this->getEnd()){
            $status = 'TERMINÉ';
        }
        else {
            $status = 'PRÊT';
        }

        return $status;
    }

    /**
     * Get status Timer
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set status Timer
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get User Timer
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set User Timer
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get start Timer
     * @return DateTimeInterface|null
     */
    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    /**
     * Set start Timer
     * @param DateTimeInterface|null $start
     * @return $this
     */
    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get end Timer
     * @return DateTimeInterface|null
     */
    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    /**
     * Set end Timer
     * @param DateTimeInterface|null $end
     * @return $this
     */
    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get create date Timer
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set create date Timer
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get Timer name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set Timer name
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Timer description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set Timer description
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Project Timer
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * Set Project Timer
     * @param Project|null $project
     * @return $this
     */
    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
