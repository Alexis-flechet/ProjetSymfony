<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    /**
     * @var Collection<int, Participe>
     */
    #[ORM\ManyToMany(targetEntity: Participe::class, mappedBy: 'id_event')]
    private Collection $participes;

    public function __construct()
    {
        $this->participes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection<int, Participe>
     */
    public function getParticipes(): Collection
    {
        return $this->participes;
    }

    public function addParticipe(Participe $participe): static
    {
        if (!$this->participes->contains($participe)) {
            $this->participes->add($participe);
            $participe->addIdEvent($this);
        }

        return $this;
    }

    public function removeParticipe(Participe $participe): static
    {
        if ($this->participes->removeElement($participe)) {
            $participe->removeIdEvent($this);
        }

        return $this;
    }
}
