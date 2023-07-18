<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'filieres')]
    private ?SousNiveauScol $niveau = null;

    #[ORM\OneToMany(mappedBy: 'filiere', targetEntity: Group::class)]
    private Collection $idGroup;

    public function __construct()
    {
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
            $idGroup->setFiliere($this);
        }

        return $this;
    }

    public function removeIdGroup(Group $idGroup): static
    {
        if ($this->idGroup->removeElement($idGroup)) {
            // set the owning side to null (unless already changed)
            if ($idGroup->getFiliere() === $this) {
                $idGroup->setFiliere(null);
            }
        }

        return $this;
    }
}
