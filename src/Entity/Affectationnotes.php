<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Affectationnotes
 *
 * @ORM\Table(name="affectationnotes", indexes={@ORM\Index(name="critere_id", columns={"critere_id"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class Affectationnotes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    #[Assert\NotBlank(message: 'Note obligatoire!')]
    #[Assert\Type(type:'float',message: 'Libelle obligatoire!')]

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float", precision=10, scale=0, nullable=false)
     */
    private $note;
    #[Assert\NotBlank(message: 'Critère obligatoire!')]

    /**
     * @var \Critere
     *
     * @ORM\ManyToOne(targetEntity="Critere")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="critere_id", referencedColumnName="id")
     * })
     */
    private $critere;
 /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;
    #[Assert\NotBlank(message: 'Utilisateur obligatoire!')]

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCritere(): ?Critere
    {
        return $this->critere;
    }

    public function setCritere(?Critere $critere): static
    {
        $this->critere = $critere;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }


}
