<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AccessType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use Doctrine\DBAL\Types\DateTimeType;
use DateTime;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * 
 */
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * 
     */
    #[Assert\Regex(
        pattern: '/^\d{3}[A-Z]{3}\d{4}$/',
        message: 'Le format de l\'ID doit être de 3 chiffres, 3 lettres majuscules, 4 chiffres.'
    )]
    private $id;

    /**
     * Undocumented variable
     *
     * @var string
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    #[Assert\Length(min:4, max:15)]
    #[Assert\NotBlank(message: 'Nom obligatoire!')]
    private $nom;


    /**
     * Undocumented variable
     *
     * @var string
     * @ORM\Column(name="prenom", type="string", length=255, nullable=false)
     */
    #[Assert\Length(min:4, max:15)]
    #[Assert\NotBlank(message: 'Prénom obligatoire!')]
    private $prenom;

    /**
     * Undocumented variable
     *
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    #[Assert\Length(min:4, max:30)]
    #[Assert\NotBlank(message: 'Email obligatoire!')]
    #[Assert\Email(message: 'Email Invalide!')]
    private $email;

    /**
     * @ORM\Column(name="roles",type = "array")
     */
    #[Assert\NotBlank(message: 'Role obligatoire!')]
    private $roles = [];

    /**
     * @var string The hashed password
     * 
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    #[Assert\Length(min:4, max:15)]
    #[Assert\NotBlank(message: 'Mot de passe obligatoire!')]
    private $password;


    /**
     * Undocumented variable
     *
     * @var string
     * 
     * @ORM\Column(name ="image",type = "string", length=255, nullable=false)
     * 
     * 
     */
    private $image;

    /**
     * Undocumented variable
     *
     * @var \DateTimeInterface
     * 
     * @ORM\Column(name = "created_at", type = "datetime", options={"default": "CURRENT_TIMESTAMP"})
     * 
     */
    private $createdAt;

    /**
     * Undocumented variable
     *
     * @var \DateTimeInterface
     * 
     * @ORM\Column(name = "updated_at", type = "datetime", options={"default": "CURRENT_TIMESTAMP"})
     * 
     */
    private $updatedAt;

            /**
     * @var string|null
     *
     * @ORM\Column(name="resetToken", type="text", length=0, nullable=false)
     */
    private $resetToken;

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
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

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
