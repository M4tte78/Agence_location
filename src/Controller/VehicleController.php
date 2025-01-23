<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
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
    public function index(Request $request, VehicleRepository $vehicleRepository): Response
    {
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
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le dossier public/images/vehicles
                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors du téléchargement de l\'image.');
                }

                // Mettre à jour la propriété `image` du véhicule
                $vehicle->setImage($newFilename);
            }

            $entityManager->persist($vehicle);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès.');
            return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): Response
    {
        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le dossier public/images/vehicles
                try {
                    $imageFile->move(
                        $this->getParameter('vehicle_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors du téléchargement de l\'image.');
                }

                // Mettre à jour la propriété `image` du véhicule
                $vehicle->setImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Véhicule modifié avec succès.');
            return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vehicle);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule supprimé avec succès.');
        }

        return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
    }
}
