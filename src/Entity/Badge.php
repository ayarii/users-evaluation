<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Badge
 *
 * @ORM\Table(name="badge")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\BadgeRepository")
 */
class Badge
{
      /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     */
    private $id;

     /**
     * Undocumented variable
     *
     * @var string
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

     /**
     * Undocumented variable
     *
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

     /**
     * Undocumented variable
     *
     * @var \DateTimeInterface
     * 
     * @ORM\Column(name = "created_at", type = "datetime")
     * 
     */
    private $createdAt;

      /**
     * Undocumented variable
     *
     * @var \DateTimeInterface
     * 
     * @ORM\Column(name = "updated_at", type = "datetime")
     * 
     */
    private $updatedAt;

     /**
     * Undocumented variable
     *
     * @var bool
     * 
     * @ORM\Column(name = "enabled", type = "boolean")
     * 
     */
    private $enabled;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}

