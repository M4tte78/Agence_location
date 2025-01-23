<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $registration = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $pricePerDay = null;

    #[ORM\Column]
    private ?bool $availabilityStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }


   

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Brand
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    // Model
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    // Registration
    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(string $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    // Type
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    // Price per day
    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(float $pricePerDay): self
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }

    // Availability status
    public function isAvailabilityStatus(): ?bool
    {
        return $this->availabilityStatus;
    }

    public function setAvailabilityStatus(bool $availabilityStatus): self
    {
        $this->availabilityStatus = $availabilityStatus;

        return $this;
    }

    // Image
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    // Comments
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setVehicle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // Set the owning side to null (unless already changed)
            if ($comment->getVehicle() === $this) {
                $comment->setVehicle(null);
            }
        }

        return $this;
    }

    // Reservations
    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setVehicle($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // Set the owning side to null (unless already changed)
            if ($reservation->getVehicle() === $this) {
                $reservation->setVehicle(null);
            }
        }

        return $this;
    }
}
