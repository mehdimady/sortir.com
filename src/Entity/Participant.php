<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte existant avec cet email !')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé !')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: "Votre email n'est pas valide !")]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank (message: "Veuillez indiquer votre nom")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[Á-ÿA-Za-z \-]{3,50}$/",
        message: "Merci d'utiliser uniquement des lettres, des tirets et des espaces !")]
    private $nom;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank (message: "Veuillez indiquer votre prenom")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[Á-ÿA-Za-z \-]{3,50}$/",
        message: "Merci d'utiliser uniquement des lettres, des tirets et des espaces !")]
    private $prenom;

    #[ORM\Column(type: 'string', length: 14)]
    #[Assert\NotBlank (message: "Veuillez indiquer votre numéro de téléphone")]
    #[Assert\Length(
        min: 10,
        max: 14,
        minMessage:
        "Votre numéro de téléphone doit comporter 10 chiffres séparés ou non par des espaces, points ou tirets ! exemple : 06.**.**.**.**",
        maxMessage:
        "Votre numéro de téléphone doit comporter 10 chiffres séparés ou non par des espaces, points ou tirets ! exemple : 06.**.**.**.**"
    )]
    #[Assert\Regex(pattern: "/^(0)*[0-9]([ .-]*[0-9]{2}){4}$/",
        message: "Merci d'utiliser uniquement des chiffres, des points, des tirets et des espaces ! exemple : 06.**.**.**.**")]
    private $telephone;

    #[ORM\Column(type: 'boolean')]
    private $administrateur = 0;

    #[ORM\Column(type: 'boolean')]
    private $actif = 1;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'participants')]
    private $campus;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, cascade: ["persist", "remove"])]
    private $organisateur;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants', cascade: ["persist"])]
    private $sorties;

    public function __construct()
    {
        $this->organisateur = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }
    public function __toString() {
        return $this->nom;
    }

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank (message: "Veuillez indiquer votre pseudo")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[Á-ÿA-Za-z0-9_-]{3,50}$/",
        message: "Merci d'utiliser uniquement des lettres, des chiffres, des tirets et des underscores  !")]
    private $pseudo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageFilename;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'favoris')]
    private $favoris;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getOrganisateur(): Collection
    {
        return $this->organisateur;
    }

    public function addOrganisateur(Sortie $organisateur): self
    {
        if (!$this->organisateur->contains($organisateur)) {
            $this->organisateur[] = $organisateur;
            $organisateur->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOrganisateur(Sortie $organisateur): self
    {
        if ($this->organisateur->removeElement($organisateur)) {
            // set the owning side to null (unless already changed)
            if ($organisateur->getOrganisateur() === $this) {
                $organisateur->setOrganisateur(null);
            }
        }

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
            $sorty->addParticipant($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            $sorty->removeParticipant($this);
        }

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(self $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(self $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }
}
