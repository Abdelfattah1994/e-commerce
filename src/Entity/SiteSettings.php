<?php

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
#[ORM\HasLifecycleCallbacks]    
class SiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favicon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroSubtitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroDescription = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroImage = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactAddress = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    public function getHeroDescription(): ?string
    {
        return $this->heroDescription;
    }

    public function setHeroDescription(?string $heroDescription): self
    {
        $this->heroDescription = $heroDescription;
        return $this;
    }

    public function getHeroImage(): ?string
    {
        return $this->heroImage;
    }

    public function setHeroImage(?string $heroImage): self
    {
        $this->heroImage = $heroImage;
        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function setFavicon(?string $favicon): self
    {
        $this->favicon = $favicon;
        return $this;
    }

    public function getHeroTitle(): ?string
    {
        return $this->heroTitle;
    }

    public function setHeroTitle(string $heroTitle): self
    {
        $this->heroTitle = $heroTitle;
        return $this;
    }

    public function getHeroSubtitle(): ?string
    {
        return $this->heroSubtitle;
    }

    public function setHeroSubtitle(string $heroSubtitle): self
    {
        $this->heroSubtitle = $heroSubtitle;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    public function getContactAddress(): ?string
    {
        return $this->contactAddress;
    }

    public function setContactAddress(?string $contactAddress): self
    {
        $this->contactAddress = $contactAddress;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
