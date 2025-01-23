<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix total est obligatoire.')]
    #[Assert\Positive(message: 'Le prix total doit être un nombre positif.')]
    private ?float $totalPrice = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'Le statut ne doit pas dépasser 255 caractères.')]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un client est obligatoire pour cette réservation.')]
    private ?AppUser $customer = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un véhicule est obligatoire pour cette réservation.')]
    private ?Vehicle $vehicle = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?AppUser
    {
        return $this->customer;
    }

    public function setCustomer(?AppUser $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
