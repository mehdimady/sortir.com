<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank (message: "Veuillez indiquer le nom d'une ville")]
    #[Assert\Length (
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[Á-ÿA-Za-z \-]{3,50}$/",
        message: "Merci d'utiliser uniquement des lettres, des tirets et des espaces !")]
    private $nom;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank (message: "Veuillez indiquer le code postal d'une ville !")]
    #[Assert\Regex(pattern: "/^\d+$/",
        message: "Veuillez entrez que des chiffres !")]
    private $codePostal;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Lieu::class)]
    private $lieus;

    public function __construct()
    {
        $this->lieus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }
    public function __toString() {
        return $this->nom;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieus(): Collection
    {
        return $this->lieus;
    }

    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieus->contains($lieu)) {
            $this->lieus[] = $lieu;
            $lieu->setVille($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): self
    {
        if ($this->lieus->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getVille() === $this) {
                $lieu->setVille(null);
            }
        }

        return $this;
    }

}
