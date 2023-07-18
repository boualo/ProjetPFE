<?php

namespace App\Entity;

use App\Repository\SousNiveauScolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousNiveauScolRepository::class)]
class SousNiveauScol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'sousNiveauScols')]
    private ?NivScol $niveauScol = null;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Filiere::class)]
    private Collection $filieres;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Group::class)]
    private Collection $idGroup;

    public function __construct()
    {
        $this->filieres = new ArrayCollection();
        $this->idGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNiveauScol(): ?NivScol
    {
        return $this->niveauScol;
    }

    public function setNiveauScol(?NivScol $niveauScol): static
    {
        $this->niveauScol = $niveauScol;

        return $this;
    }

    /**
     * @return Collection<int, Filiere>
     */
    public function getFilieres(): Collection
    {
        return $this->filieres;
    }

    public function addFiliere(Filiere $filiere): static
    {
        if (!$this->filieres->contains($filiere)) {
            $this->filieres->add($filiere);
            $filiere->setNiveau($this);
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): static
    {
        if ($this->filieres->removeElement($filiere)) {
            // set the owning side to null (unless already changed)
            if ($filiere->getNiveau() === $this) {
                $filiere->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getIdGroup(): Collection
    {
        return $this->idGroup;
    }

    public function addIdGroup(Group $idGroup): static
    {
        if (!$this->idGroup->contains($idGroup)) {
            $this->idGroup->add($idGroup);
            $idGroup->setNiveau($this);
        }

        return $this;
    }

    public function removeIdGroup(Group $idGroup): static
    {
        if ($this->idGroup->removeElement($idGroup)) {
            // set the owning side to null (unless already changed)
            if ($idGroup->getNiveau() === $this) {
                $idGroup->setNiveau(null);
            }
        }

        return $this;
    }
}
