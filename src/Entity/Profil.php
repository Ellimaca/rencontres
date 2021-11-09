<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $sexe;

    /**
     * @Assert\Length(
     *     min=5,
     *     max=5
     * )
     * @ORM\Column(type="integer")
     */
    private $CodePostal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Ville;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="profil", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PhotoProfil::class, mappedBy="profil")
     */
    private $PhotoProfils;

    /**
     * @ORM\ManyToOne(targetEntity=Critere::class, inversedBy="profils")
     */
    private $criteres;

    public function __construct()
    {
        $this->PhotoProfils = new ArrayCollection();
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
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|PhotoProfil[]
     */
    public function getPhotoProfils(): Collection
    {
        return $this->PhotoProfils;
    }

    public function addPhotoProfil(PhotoProfil $photoProfil): self
    {
        if (!$this->PhotoProfils->contains($photoProfil)) {
            $this->PhotoProfils[] = $photoProfil;
            $photoProfil->setProfil($this);
        }

        return $this;
    }

    public function removePhotoProfil(PhotoProfil $photoProfil): self
    {
        if ($this->PhotoProfils->removeElement($photoProfil)) {
            // set the owning side to null (unless already changed)
            if ($photoProfil->getProfil() === $this) {
                $photoProfil->setProfil(null);
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
    
}
