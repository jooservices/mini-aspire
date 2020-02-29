<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="users_idx", columns={"email"})})
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=125)
     */
    private $full_name;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $last_name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"Total amount (left) of user"})
     */
    private $loans = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"comment":"Determine if user be blocked or not"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UsersLoans", mappedBy="user")
     */
    private $usersLoans;

    /**
     * Users constructor.
     */
    public function __construct()
    {
        $this->created    = new \DateTime();
        $this->usersLoans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getLoans(): ?float
    {
        return $this->loans;
    }

    public function setLoans(float $loans): self
    {
        $this->loans = $loans;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
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

//    /**
//     * @return Collection|UsersLoans[]
//     */
//    public function getUsersLoans(): Collection
//    {
//        return $this->usersLoans;
//    }
//
//    public function addUsersLoan(UsersLoans $usersLoan): self
//    {
//        if (!$this->usersLoans->contains($usersLoan)) {
//            $this->usersLoans[] = $usersLoan;
//            $usersLoan->setUser($this);
//        }
//
//        return $this;
//    }
//
//    public function removeUsersLoan(UsersLoans $usersLoan): self
//    {
//        if ($this->usersLoans->contains($usersLoan)) {
//            $this->usersLoans->removeElement($usersLoan);
//            // set the owning side to null (unless already changed)
//            if ($usersLoan->getUser() === $this) {
//                $usersLoan->setUser(null);
//            }
//        }
//
//        return $this;
//    }
}
