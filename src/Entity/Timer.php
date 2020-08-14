<?php

namespace App\Entity;

use App\Repository\TimerRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
        $this->createdAt = new DateTime();
    }

    /**
     * Run Timer
     */
    public function run()
    {
        if (empty($this->getStart())) {
            $this->setStatus(true);
            $this->setStart(new DateTime());
        }
    }

    /**
     * Stop Timer
     */
    public function stop()
    {
        if (empty($this->getEnd())) {
            $this->setStatus(false);
            $this->setEnd(new DateTime());
        }
    }

    /**
     * Get
     * @return mixed
     */
    public function getTime()
    {
        if (empty($this->start))
        {
            return "";
        }

        $time = $this->start->diff($this->end);

        return $time->format('%H:%I:%S');
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
            $status = 'EN COUR';
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
