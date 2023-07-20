<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use DateTime;

/**
 * Badge
 *
 * @ORM\Table(name="badge")
 * @ORM\Entity
 *
 */
#[UniqueEntity(fields: ['libelle'], message: 'ce badge existe déjà!')]
class Badge
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    #[Assert\NotBlank(null, "Libelle Obligatoire!")]
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    #[Assert\NotBlank(null, "Description Obligatoire!")]
    private $description;

    /**
     * Undocumented variable
     *
     * @var string
     * 
     * @ORM\Column(name ="image",type = "string", length=255, nullable=false)
     * 
     * @Assert\NotBlank(message="Image Obligatoire!")
     */
    private $image;

    /**
     * Undocumented variable
     *
     * @var string
     * 
     * @ORM\Column(name ="action",type = "string", length=255, nullable=false)
     * 
     * 
     */
    #[Assert\NotBlank(null, "Action Obligatoire!")]
    private $action;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }
    public function setAction(?string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
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
    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }
}
