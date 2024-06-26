<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $siret = null;

    #[ORM\Column(length: 255)]
    private ?string $cle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleurPrimaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleurSecondaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleurTertiaire = null;

    #[ORM\Column]
    private ?bool $paiementCheck = null;

    #[ORM\Column]
    private ?bool $messageCheck = null;

    #[ORM\Column]
    private ?bool $galerieCheck = null;

    #[ORM\Column]
    private ?bool $evenementCheck = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'asso')]
    private Collection $users;

    #[ORM\Column(type: Types:: JSON)]
    private array $listeUsers = [];

    /**
     * @var Collection<int, Galerie>
     */
    #[ORM\OneToMany(targetEntity: Galerie::class, mappedBy: 'asso')]
    private Collection $galeries;

    /**
     * @var Collection<int, Cotisation>
     */
    #[ORM\OneToMany(targetEntity: Cotisation::class, mappedBy: 'asso')]
    private Collection $cotisations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'asso')]
    private Collection $messages;

    #[ORM\Column]
    private ?bool $isActivated = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->galeries = new ArrayCollection();
        $this->cotisations = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

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

    public function getSiret(): ?int
    {
        return $this->siret;
    }

    public function setSiret(int $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(string $cle): static
    {
        $this->cle = $cle;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): static
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getCouleurPrimaire(): ?string
    {
        return $this->couleurPrimaire;
    }

    public function setCouleurPrimaire(?string $couleurPrimaire): static
    {
        $this->couleurPrimaire = $couleurPrimaire;

        return $this;
    }

    public function getCouleurSecondaire(): ?string
    {
        return $this->couleurSecondaire;
    }

    public function setCouleurSecondaire(?string $couleurSecondaire): static
    {
        $this->couleurSecondaire = $couleurSecondaire;

        return $this;
    }

    public function getCouleurTertiaire(): ?string
    {
        return $this->couleurTertiaire;
    }

    public function setCouleurTertiaire(?string $couleurTertiaire): static
    {
        $this->couleurTertiaire = $couleurTertiaire;

        return $this;
    }

    public function isPaiementCheck(): ?bool
    {
        return $this->paiementCheck;
    }

    public function setPaiementCheck(bool $paiementCheck): static
    {
        $this->paiementCheck = $paiementCheck;

        return $this;
    }

    public function isMessageCheck(): ?bool
    {
        return $this->messageCheck;
    }

    public function setMessageCheck(bool $messageCheck): static
    {
        $this->messageCheck = $messageCheck;

        return $this;
    }

    public function isGalerieCheck(): ?bool
    {
        return $this->galerieCheck;
    }

    public function setGalerieCheck(bool $galerieCheck): static
    {
        $this->galerieCheck = $galerieCheck;

        return $this;
    }

    public function isEvenementCheck(): ?bool
    {
        return $this->evenementCheck;
    }

    public function setEvenementCheck(bool $evenementCheck): static
    {
        $this->evenementCheck = $evenementCheck;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAsso($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAsso() === $this) {
                $user->setAsso(null);
            }
        }

        return $this;
    }

    public function getListUsers(): array
    {
        return $this->listeUsers;
    }

    public function countListUsers(): int
    {
        return count($this->listeUsers);
    }

    public function setListUsers(array $listeUsers): static
    {
        $this->listeUsers = $listeUsers;

        return $this;
    }

    public function addListeUsers(int $listeUsers): static
    {
        $this->listeUsers[] = $listeUsers;

        return $this;
    }

    /**
     * @return Collection<int, Galerie>
     */
    public function getGaleries(): Collection
    {
        return $this->galeries;
    }

    public function addGalery(Galerie $galery): static
    {
        if (!$this->galeries->contains($galery)) {
            $this->galeries->add($galery);
            $galery->setAsso($this);
        }

        return $this;
    }

    public function removeGalery(Galerie $galery): static
    {
        if ($this->galeries->removeElement($galery)) {
            // set the owning side to null (unless already changed)
            if ($galery->getAsso() === $this) {
                $galery->setAsso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }

    public function addCotisation(Cotisation $cotisation): static
    {
        if (!$this->cotisations->contains($cotisation)) {
            $this->cotisations->add($cotisation);
            $cotisation->setAsso($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): static
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getAsso() === $this) {
                $cotisation->setAsso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setAsso($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAsso() === $this) {
                $message->setAsso(null);
            }
        }

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setActivated(bool $isActivated): static
    {
        $this->isActivated = $isActivated;

        return $this;
    }
}
