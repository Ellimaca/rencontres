<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $sexe;

    /**
     * @ORM\Column(type="integer")
     */
    private $CodePostal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Ville;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="profil", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PhotoProfil::class, mappedBy="profil")
     */
    private $photosProfil;

    /**
     * @ORM\ManyToOne(targetEntity=Critere::class, inversedBy="profil")
     */
    private $criteres;

    /**
     * @ORM\Column(type="boolean")
     */
    private $coeur;

    public function __construct()
    {
        $this->photosProfil = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->CodePostal;
    }

    public function setCodePostal(int $CodePostal): self
    {
        $this->CodePostal = $CodePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): self
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getProfil() !== $this) {
            $user->setProfil($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|PhotoProfil[]
     */
    public function getPhotosProfil(): Collection
    {
        return $this->photosProfil;
    }

    public function addPhotosProfil(PhotoProfil $photosProfil): self
    {
        if (!$this->photosProfil->contains($photosProfil)) {
            $this->photosProfil[] = $photosProfil;
            $photosProfil->setProfil($this);
        }

        return $this;
    }

    public function removePhotosProfil(PhotoProfil $photosProfil): self
    {
        if ($this->photosProfil->removeElement($photosProfil)) {
            // set the owning side to null (unless already changed)
            if ($photosProfil->getProfil() === $this) {
                $photosProfil->setProfil(null);
            }
        }

        return $this;
    }

    public function getCriteres(): ?Critere
    {
        return $this->criteres;
    }

    public function setCriteres(?Critere $criteres): self
    {
        $this->criteres = $criteres;

        return $this;
    }

    public function getCoeur(): ?bool
    {
        return $this->coeur;
    }

    public function setCoeur(bool $coeur): self
    {
        $this->coeur = $coeur;

        return $this;
    }
}
