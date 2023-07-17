<?php

namespace App\Entity;

use App\Repository\SousNiveauScolRepository;
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
}
