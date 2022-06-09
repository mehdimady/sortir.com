<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank (message: "Veuillez donner un nom à votre lieu !")]
    #[Assert\Length (
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[Á-ÿA-Za-z \-']{3,50}$/",
        message: "Merci d'utiliser uniquement des lettres, des tirets et des espaces !")]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank (message: "Veuillez donner un nom à la rue !")]
    #[Assert\Length (
        min: 3,
        max: 255,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 255 caractères s'il vous plait !"
    )]

    private $rue;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank (message: "Veuillez indiquer une latitude !")]
    #[Assert\Regex(pattern: "/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/",
        message: 'Veuillez utiliser uniquement des chiffres, les signes + ou - et des points !')]
    private $latitude;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank (message: "Veuillez indiquer une longitude !")]
    #[Assert\Regex(pattern: "/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/",
        message: 'Veuillez utiliser uniquement des chiffres, les signes + ou - et des points !')]
    private $longitude;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'lieus')]
    private $ville;


    #[ORM\OneToMany(mappedBy: 'lieux', targetEntity: Sortie::class , cascade: ["persist"])]
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }
    public function __toString() {
        return $this->nom;
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setLieux($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getLieux() === $this) {
                $sorty->setLieux(null);
            }
        }

        return $this;
    }
}
