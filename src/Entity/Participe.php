<?php

namespace App\Entity;

use App\Repository\ParticipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipeRepository::class)]
class Participe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'participes')]
    private Collection $id_event;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participes')]
    private Collection $id_user;

    public function __construct()
    {
        $this->id_event = new ArrayCollection();
        $this->id_user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getIdEvent(): Collection
    {
        return $this->id_event;
    }

    public function addIdEvent(Event $idEvent): static
    {
        if (!$this->id_event->contains($idEvent)) {
            $this->id_event->add($idEvent);
        }

        return $this;
    }

    public function removeIdEvent(Event $idEvent): static
    {
        $this->id_event->removeElement($idEvent);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->id_user;
    }

    public function addIdUser(User $idUser): static
    {
        if (!$this->id_user->contains($idUser)) {
            $this->id_user->add($idUser);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): static
    {
        $this->id_user->removeElement($idUser);

        return $this;
    }
}
