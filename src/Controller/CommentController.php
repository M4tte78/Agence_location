<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Vehicle; // Entité associée
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/vehicle/{id}/comment', name: 'comment_new', methods: ['GET', 'POST'])]
    public function newComment(
        Request $request,
        Vehicle $vehicle,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = new Comment();
        $comment->setVehicle($vehicle);
        $comment->setAuthor($this->getUser()); // L'utilisateur connecté
        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_show', ['id' => $vehicle->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
            'vehicle' => $vehicle, // Passe l'objet Vehicle au template
        ]);
        
    }
}


