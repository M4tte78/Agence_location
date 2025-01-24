<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    // Liste des réservations
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, VehicleRepository $vehicleRepository): Response
    {
        $vehicles = $vehicleRepository->findAll();

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
            'vehicles' => $vehicles,
        ]);
    }

    // Création d'une nouvelle réservation
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        VehicleRepository $vehicleRepository
    ): Response {
        $vehicleId = $request->get('vehicle_id');
        if (!$vehicleId) {
            throw $this->createNotFoundException("L'ID du véhicule est manquant.");
        }

        $vehicle = $vehicleRepository->find($vehicleId);
        if (!$vehicle) {
            throw $this->createNotFoundException("Le véhicule avec l'ID $vehicleId est introuvable.");
        }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Log pour débogage
            dump($reservation);
            exit();
        
            // Associer le véhicule et le client connecté
            $reservation->setVehicle($vehicle);
            $reservation->setCustomer($this->getUser());

            // Calcul du prix total
            $startDate = $reservation->getStartDate();
            $endDate = $reservation->getEndDate();
            if ($startDate && $endDate) {
                $days = max($endDate->diff($startDate)->days, 1); // Minimum 1 jour
                $totalPrice = $vehicle->getPricePerDay() * $days;
                $reservation->setTotalPrice($totalPrice);
            }

            // Définir un statut par défaut
            $reservation->setStatus('En attente');

            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès.');
            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $vehicle,
        ]);
    }


    // Affichage des détails d'une réservation spécifique
    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    // Modification d'une réservation existante
    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ReservationType::class, $reservation, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour du prix total si les dates sont modifiées
            $startDate = $reservation->getStartDate();
            $endDate = $reservation->getEndDate();
            if ($startDate && $endDate) {
                $days = max($endDate->diff($startDate)->days, 1);
                $totalPrice = $reservation->getVehicle()->getPricePerDay() * $days;
                $reservation->setTotalPrice($totalPrice);
            }

            // Sauvegarde des modifications
            $entityManager->flush();

            // Message de succès et redirection
            $this->addFlash('success', 'Réservation modifiée avec succès.');
            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'une réservation
    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reservation_index');
    }
}
