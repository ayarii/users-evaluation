<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\GroupeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity
 */
class Groupe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
  
    #[Assert\NotBlank(message: 'Libelle obligatoire!')]
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
