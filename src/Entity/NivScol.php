<?php

namespace App\Entity;

use App\Repository\NivScolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NivScolRepository::class)]
class NivScol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $labelle = null;

    #[ORM\OneToMany(mappedBy: 'niveauScol', targetEntity: SousNiveauScol::class)]
    private Collection $sousNiveauScols;

    public function __construct()
    {
        $this->sousNiveauScols = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelle(): ?string
    {
        return $this->labelle;
    }

    public function setLabelle(string $labelle): static
    {
        $this->labelle = $labelle;

        return $this;
    }

    /**
     * @return Collection<int, SousNiveauScol>
     */
    public function getSousNiveauScols(): Collection
    {
        return $this->sousNiveauScols;
    }

    public function addSousNiveauScol(SousNiveauScol $sousNiveauScol): static
    {
        if (!$this->sousNiveauScols->contains($sousNiveauScol)) {
            $this->sousNiveauScols->add($sousNiveauScol);
            $sousNiveauScol->setNiveauScol($this);
        }

        return $this;
    }

    public function removeSousNiveauScol(SousNiveauScol $sousNiveauScol): static
    {
        if ($this->sousNiveauScols->removeElement($sousNiveauScol)) {
            // set the owning side to null (unless already changed)
            if ($sousNiveauScol->getNiveauScol() === $this) {
                $sousNiveauScol->setNiveauScol(null);
            }
        }

        return $this;
    }
}
