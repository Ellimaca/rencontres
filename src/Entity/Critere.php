<?php

namespace App\Entity;

use App\Repository\CritereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CritereRepository::class)
 */
class Critere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $sexesRecherches;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $departementsRecherches;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ageRecherches;

    /**
     * @ORM\OneToMany(targetEntity=Profil::class, mappedBy="criteres")
     */
    private $profil;

    public function __construct()
    {
        $this->profil = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSexesRecherches(): ?string
    {
        return $this->sexesRecherches;
    }

    public function setSexesRecherches(?string $sexesRecherches): self
    {
        $this->sexesRecherches = $sexesRecherches;

        return $this;
    }

    public function getDepartementsRecherches(): ?int
    {
        return $this->departementsRecherches;
    }

    public function setDepartementsRecherches(?int $departementsRecherches): self
    {
        $this->departementsRecherches = $departementsRecherches;

        return $this;
    }

    public function getAgeRecherches(): ?int
    {
        return $this->ageRecherches;
    }

    public function setAgeRecherches(?int $ageRecherches): self
    {
        $this->ageRecherches = $ageRecherches;

        return $this;
    }

    /**
     * @return Collection|Profil[]
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
            $profil->setCriteres($this);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profil->removeElement($profil)) {
            // set the owning side to null (unless already changed)
            if ($profil->getCriteres() === $this) {
                $profil->setCriteres(null);
            }
        }

        return $this;
    }
}
