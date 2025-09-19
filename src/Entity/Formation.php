<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(min: 10, max: 200, minMessage: "Le titre doit faire au moins {{ limit }} caractères", maxMessage: "Le titre ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "La durée doit être 0 ou un nombre positif")]
    private ?int $dureeHeures = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['Débutant', 'Intermédiaire', 'Avancé'], message: "Le niveau de difficulté doit être 'Débutant', 'Intermédiaire' ou 'Avancé'")]
    private ?string $niveauDifficulte = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    private ?bool $estPubliee = null;

    #[ORM\Column(nullable: true)]
    private ?int $capaciteMax = null;

    /**
     * @var Collection<int, Module>
     */
    #[ORM\OneToMany(targetEntity: Module::class, mappedBy: 'formation', orphanRemoval: true)]
    private Collection $modules;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'formationsEnseignees')]
    private Collection $instructeurs;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->estPubliee = false;
        $this->modules = new ArrayCollection();
        $this->instructeurs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDureeHeures(): ?int
    {
        return $this->dureeHeures;
    }

    public function setDureeHeures(int $dureeHeures): static
    {
        $this->dureeHeures = $dureeHeures;

        return $this;
    }

    public function getNiveauDifficulte(): ?string
    {
        return $this->niveauDifficulte;
    }

    public function setNiveauDifficulte(string $niveauDifficulte): static
    {
        $this->niveauDifficulte = $niveauDifficulte;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function isEstPubliee(): ?bool
    {
        return $this->estPubliee;
    }

    public function setEstPubliee(bool $estPubliee): static
    {
        $this->estPubliee = $estPubliee;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(?int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setFormation($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getFormation() === $this) {
                $module->setFormation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getInstructeurs(): Collection
    {
        return $this->instructeurs;
    }

    public function addInstructeur(User $instructeur): static
    {
        if (!$this->instructeurs->contains($instructeur)) {
            $this->instructeurs->add($instructeur);
        }

        return $this;
    }

    public function removeInstructeur(User $instructeur): static
    {
        $this->instructeurs->removeElement($instructeur);

        return $this;
    }
}
