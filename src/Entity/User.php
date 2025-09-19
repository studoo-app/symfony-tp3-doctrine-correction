<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message:"L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    #[Assert\Length(min: 2,max: 100, minMessage: "L'email doit faire au moins {{ limit }} caractères", maxMessage: "L'email ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nomAffichage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column]
    private ?bool $estActif = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'instructeurs')]
    private Collection $formationsEnseignees;

    public function __construct()
    {
        $this->dateInscription = new \DateTime();
        $this->estActif = true;
        $this->formationsEnseignees = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getNomAffichage(): ?string
    {
        return $this->nomAffichage;
    }

    public function setNomAffichage(string $nomAffichage): static
    {
        $this->nomAffichage = $nomAffichage;

        return $this;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function isEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): static
    {
        $this->estActif = $estActif;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormationsEnseignees(): Collection
    {
        return $this->formationsEnseignees;
    }

    public function addFormationsEnseignee(Formation $formationsEnseignee): static
    {
        if (!$this->formationsEnseignees->contains($formationsEnseignee)) {
            $this->formationsEnseignees->add($formationsEnseignee);
            $formationsEnseignee->addInstructeur($this);
        }

        return $this;
    }

    public function removeFormationsEnseignee(Formation $formationsEnseignee): static
    {
        if ($this->formationsEnseignees->removeElement($formationsEnseignee)) {
            $formationsEnseignee->removeInstructeur($this);
        }

        return $this;
    }
}
