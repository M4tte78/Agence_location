<?php

// src/Command/UpdateVehicleAvailabilityCommand.php
namespace App\Command;

use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:update-vehicle-availability',
    description: 'Met à jour automatiquement le statut de disponibilité des véhicules.'
)]
class UpdateVehicleAvailabilityCommand extends Command
{
    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->vehicleRepository = $vehicleRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vehicles = $this->vehicleRepository->findAll();
        foreach ($vehicles as $vehicle) {
            $vehicle->updateAvailabilityStatus();
        }
        $this->entityManager->flush();

        $output->writeln('Les statuts des véhicules ont été mis à jour.');
        return Command::SUCCESS;
    }
}
