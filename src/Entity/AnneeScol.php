<?php

namespace App\Entity;

use App\Repository\AnneeScolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeScolRepository::class)]
class AnneeScol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $anneeScol = null;

    #[ORM\OneToMany(mappedBy: 'anneeScol', targetEntity: Group::class)]
    private Collection $idGroup;

    public function __construct()
    {
        $this->idGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeScol(): ?string
    {
        return $this->anneeScol;
    }

    public function setAnneeScol(string $anneeScol): static
    {
        $this->anneeScol = $anneeScol;

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
            $idGroup->setAnneeScol($this);
        }

        return $this;
    }

    public function removeIdGroup(Group $idGroup): static
    {
        if ($this->idGroup->removeElement($idGroup)) {
            // set the owning side to null (unless already changed)
            if ($idGroup->getAnneeScol() === $this) {
                $idGroup->setAnneeScol(null);
            }
        }

        return $this;
    }
}
