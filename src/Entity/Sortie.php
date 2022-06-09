<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank (message: "Veuillez donner un nom à votre sortie")]
    #[Assert\Length (
        min: 3,
        max: 50,
        minMessage: "Minimum 3 caractères s'il vous plait !",
        maxMessage: "Maximum 50 caractères s'il vous plait !"
    )]
    #[Assert\Regex(pattern: "/^[^<>{}*\:\\$!]{3,50}$/",
        message: "Certains caractères ne sont pas acceptés !")]
    private $nom;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan(propertyPath: "dateLimiteInscription",
        message: "La date du début de la sortie ne peut pas être avant celle de l'inscription !")]
    private $dateHeureDebut;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank (message: "Veuillez indiquer une durée pour la sortie")]
    #[Assert\GreaterThanOrEqual(
        value: 30,
        message: 'Les sorties doivent durer 30 minutes ou plus !'
    )]
    #[Assert\Regex(pattern: "/^\d+$/",
        message: "Veuillez entrez que des chiffres !")]
    private $duree;

    #[ORM\Column(type: 'datetime')]
    #[Assert\LessThan(propertyPath: "dateHeureDebut",
        message: "La date limite d'inscription ne peut pas dépasser celle du début de la sortie !")]
    private $dateLimiteInscription;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank (message: "Veuillez indiquer un nombre maximum de participants !")]
    #[Assert\GreaterThanOrEqual(
        value: 2,
        message: 'Il est inutile de programmer une sortie pour moins de deux personnes !'
    )]
    #[Assert\Regex(pattern: "/^\d+$/",
        message: "Veuillez entrez que des chiffres !")]
    private $nbInscriptionsMax;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank (message: "Veuillez ajouter des informations sur la sortie !")]
    #[Assert\Regex(pattern: "/^[^<>{}*\:\\$!]{3,500}$/",
        message: "Certains caractères ne sont pas acceptés !")]
    private $infosSortie;

    #[ORM\ManyToOne(targetEntity: Etat::class, inversedBy: 'sorties')]
    private $etat;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'sorties', cascade: ["persist"])]
    private $lieux;

    #[ORM\ManyToOne(targetEntity: Participant::class, inversedBy: 'organisateur', cascade: ["persist"])]
    private $organisateur;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'sorties')]
    private $campus;

    #[ORM\ManyToMany(targetEntity: participant::class, inversedBy: 'sorties', cascade: ["persist"])]
    private $participants;

    #[Assert\Regex(pattern: "/^[^<>{}*\:\\$!]{3,500}$/",
        message: "Certains caractères ne sont pas acceptés !")]
    #[ORM\Column(type: 'text', nullable: true)]
    private $motif;

    #[ORM\Column(type: 'datetime')]
    private $dateHeureFin;

    public function __construct()
    {
        $this->participants = new ArrayCollection();

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

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTime $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieux(): ?Lieu
    {
        return $this->lieux;
    }

    public function setLieux(?Lieu $lieux): self
    {
        $this->lieux = $lieux;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

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

    /**
     * @return Collection<int, participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(participant $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(\DateTimeInterface $dateHeureFin): self
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }

}
