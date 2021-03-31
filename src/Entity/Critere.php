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
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="critere")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCritere($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCritere($this);
        }

        return $this;
    }

}
