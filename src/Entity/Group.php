<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nomGroup = null;

    #[ORM\ManyToOne(inversedBy: 'idGroup')]
    private ?Filiere $filiere = null;

    #[ORM\ManyToOne(inversedBy: 'idGroup')]
    private ?SousNiveauScol $niveau = null;

    #[ORM\OneToMany(mappedBy: 'idGroup', targetEntity: Eleve::class)]
    private Collection $eleves;

    #[ORM\ManyToMany(targetEntity: Enseignant::class, mappedBy: 'groupe')]
    private Collection $enseignants;

    #[ORM\ManyToOne(inversedBy: 'idGroup')]
    private ?AnneeScol $anneeScol = null;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
        $this->enseignants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGroup(): ?string
    {
        return $this->nomGroup;
    }

    public function setNomGroup(string $nomGroup): static
    {
        $this->nomGroup = $nomGroup;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): static
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getNiveau(): ?SousNiveauScol
    {
        return $this->niveau;
    }

    public function setNiveau(?SousNiveauScol $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): static
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setIdGroup($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getIdGroup() === $this) {
                $elefe->setIdGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enseignant>
     */
    public function getEnseignants(): Collection
    {
        return $this->enseignants;
    }

    public function addEnseignant(Enseignant $enseignant): static
    {
        if (!$this->enseignants->contains($enseignant)) {
            $this->enseignants->add($enseignant);
            $enseignant->addGroupe($this);
        }

        return $this;
    }

    public function removeEnseignant(Enseignant $enseignant): static
    {
        if ($this->enseignants->removeElement($enseignant)) {
            $enseignant->removeGroupe($this);
        }

        return $this;
    }
    public function __toString(){
        return $this->nomGroup;
    }

    public function getAnneeScol(): ?AnneeScol
    {
        return $this->anneeScol;
    }

    public function setAnneeScol(?AnneeScol $anneeScol): static
    {
        $this->anneeScol = $anneeScol;

        return $this;
    }
}
