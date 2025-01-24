<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Vehicle;
use App\Entity\Comment;
use App\Form\VehicleType;
use App\Form\CommentType;
use App\Repository\VehicleRepository;
use App\Repository\CommentRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/vehicle')]
class VehicleController extends AbstractController
{
    #[Route('/', name: 'app_vehicle_index', methods: ['GET', 'POST'])]
    public function index(Request $request, VehicleRepository $vehicleRepository, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $currentDate = new \DateTimeImmutable();

        // Récupérer tous les véhicules
        $vehicles = $vehicleRepository->findAll();

        foreach ($vehicles as $vehicle) {
            $vehicle->updateAvailabilityStatus($currentDate);
        }

        // Sauvegarder les changements
        $entityManager->flush();

        // Recherche des véhicules
        $search = $request->query->get('search', '');
        $vehicles = $vehicleRepository->findBySearch($search);

        return $this->render('vehicle/index.html.twig', [
            'vehicles' => $vehicles,
            'search' => $search,
        ]);
    }




    #[Route('/new', name: 'app_vehicle_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($vehicle->getPricePerDay() < 20 || $vehicle->getPricePerDay() > 300) {
                $this->addFlash('danger', 'Le prix par jour doit être compris entre 20 € et 300 €.');
                return $this->render('vehicle/new.html.twig', [
                    'vehicle' => $vehicle,
                    'form' => $form->createView(),
                ]);
            }

            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );
                    $vehicle->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($vehicle);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès.');
            return $this->redirectToRoute('app_vehicle_index');
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vehicle/{id}', name: 'app_vehicle_show', methods: ['GET', 'POST'])]
    public function show(
        Vehicle $vehicle,
        CommentRepository $commentRepository,
        ReservationRepository $reservationRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Mise à jour automatique de la disponibilité
        $currentDate = new \DateTimeImmutable();
        $vehicle->updateAvailabilityStatus($currentDate);

        // Sauvegarde de l'état mis à jour
        $entityManager->flush();

        // Récupération des commentaires et des réservations
        $comments = $commentRepository->findBy(['vehicle' => $vehicle]);
        $totalReservations = $reservationRepository->countReservationsByVehicle($vehicle);

        // Vérification pour ajouter des commentaires
        $user = $this->getUser();
        $canComment = false;

        if ($user) {
            $canComment = $this->isGranted('ROLE_ADMIN') || 
                $reservationRepository->findOneBy(['vehicle' => $vehicle, 'customer' => $user]);
        }

        // Formulaire de commentaire
        $form = null;
        if ($canComment) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setVehicle($vehicle);
                $comment->setAuthor($user);
                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Votre commentaire a été ajouté.');
                return $this->redirectToRoute('app_vehicle_show', ['id' => $vehicle->getId()]);
            }
        }

        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
            'comments' => $comments,
            'totalReservations' => $totalReservations,
            'canComment' => $canComment,
            'commentForm' => $form ? $form->createView() : null,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Véhicule modifié avec succès.');
                return $this->redirectToRoute('app_vehicle_index');
            } catch (\LogicException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
            'hasActiveReservations' => $vehicle->hasActiveReservations(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicle->getId(), $request->request->get('_token'))) {
            foreach ($vehicle->getReservations() as $reservation) {
                $entityManager->remove($reservation);
            }
            foreach ($vehicle->getComments() as $comment) {
                $entityManager->remove($comment);
            }
            $entityManager->remove($vehicle);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule et données associées supprimés avec succès.');
        }

        return $this->redirectToRoute('app_vehicle_index');
    }
}