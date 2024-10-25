<?php

namespace App\Entity;

use App\Repository\VirtualCardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: VirtualCardRepository::class)]
#[ORM\HasLifecycleCallbacks]
class VirtualCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'virtualCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 16)]
    private ?string $cardNumber = null;

    #[ORM\Column(length: 5)]
    private ?string $expirationDate = null;

    #[ORM\Column(length: 3)]
    private ?string $cvv = null;

    #[ORM\Column]
    private float $balance = 0.0;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'virtualCard', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }


    public function getName(): ?string
    {
        return $this->user->getFullName();
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setExpirationDate(string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }


    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setVirtualCard($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getVirtualCard() === $this) {
                $transaction->setVirtualCard(null);
            }
        }

        return $this;
    }
}
