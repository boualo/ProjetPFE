<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $devoire1 = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateNote = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $remarque = null;

    #[ORM\Column(nullable: true)]
    private ?float $devoire2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $devoire3 = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Matiere $matiere = null;

    #[ORM\Column]
    private ?int $semester = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevoire1(): ?float
    {
        return $this->devoire1;
    }

    public function setDevoire1(float $devoire1): static
    {
        $this->devoire1 = $devoire1;

        return $this;
    }

    public function getDateNote(): ?\DateTimeInterface
    {
        return $this->dateNote;
    }

    public function setDateNote($dateNote): static
    {
        $this->dateNote = $dateNote;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(string $remarque): static
    {
        $this->remarque = $remarque;

        return $this;
    }

    public function getDevoire2(): ?float
    {
        return $this->devoire2;
    }

    public function setDevoire2(?float $devoire2): static
    {
        $this->devoire2 = $devoire2;

        return $this;
    }

    public function getDevoire3(): ?float
    {
        return $this->devoire3;
    }

    public function setDevoire3(?float $devoire3): static
    {
        $this->devoire3 = $devoire3;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): static
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): static
    {
        $this->semester = $semester;

        return $this;
    }

}
