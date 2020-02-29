<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersLoansRepository")
 */
class UsersLoans
{
    const FREQUENCY_DAILY = 1;
    const FREQUENCY_MONTHLY = 2;
    const FREQUENCY_YEARLY = 3;

    const MINIMUM_AMOUNT = 0.0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="usersLoans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount;

    /**
     * @ORM\Column(type="smallint")
     */
    private $frequency;

    /**
     * @ORM\Column(type="float")
     */
    private $interest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extra_fees;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = 1;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finished;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transactions", mappedBy="loan")
     */
    private $transactions;

    /**
     * @ORM\Column(type="float")
     */
    private $amount_left;

    /**
     * Users constructor.
     */
    public function __construct()
    {
        $this->created      = new \DateTime();
        $this->interest     = 0.0;
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(int $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getInterest(): ?float
    {
        return $this->interest;
    }

    public function setInterest(float $interest): self
    {
        $this->interest = $interest;

        return $this;
    }

    public function getExtraFees(): ?array
    {
        return json_decode($this->extra_fees, true);
    }

    public function setExtraFees(?string $extra_fees): self
    {
        $this->extra_fees = $extra_fees;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getFinished(): ?\DateTimeInterface
    {
        return $this->finished;
    }

    public function setFinished(\DateTimeInterface $finished): self
    {
        $this->finished = $finished;

        return $this;
    }


    public function getAmountLeft(): ?float
    {
        return $this->amount_left;
    }

    public function setAmountLeft(float $amount_left): self
    {
        $this->amount_left = $amount_left;

        return $this;
    }
}
