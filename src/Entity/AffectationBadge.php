<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\AffectationBadgeRepository;

/**
 * Affectationnotes
 *
 * @ORM\Table(name="affectationbadge", indexes={@ORM\Index(name="id_badge", columns={"id_badge"}), @ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\AffectationBadgeRepository")
 */
class AffectationBadge
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
     * @var Badge
     *
     * @ORM\ManyToOne(targetEntity="Badge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_badge", referencedColumnName="id")
     * })
     */
    #[Assert\NotBlank(message: 'Badge obligatoire!')]
    private $idbadge;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    #[Assert\NotBlank(message: 'Utilisateur obligatoire!')]
    private $iduser;

    /**
     * Undocumented variable
     *
     * @var \DateTimeInterface
     * 
     * @ORM\Column(name = "date_affectation", type = "datetime")
     * 
     */
    #[Assert\NotBlank(message: 'Date Affectation obligatoire!')]
    private $dateaffectation;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->iduser;
    }

    public function setIdUser(?User $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdBadge(): ?Badge
    {
        return $this->idbadge;
    }

    public function setIdBadge(?Badge $idbadge): static
    {
        $this->idbadge = $idbadge;

        return $this;
    }

    public function getDateaffectation(): ?\DateTimeInterface
    {
        return $this->dateaffectation;
    }

    public function setDateAffectation(\DateTimeInterface $dateaffectation): self
    {
        $this->dateaffectation = $dateaffectation;

        return $this;
    }

    public function __toString()
    {
        return "".$this->getId()  ;
    }


}
