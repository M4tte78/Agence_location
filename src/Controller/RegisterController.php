<?php

/// src/Controller/RegisterController.php
namespace App\Controller;

use App\Entity\AppUser;
use App\Form\AppUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')] // Assurez-vous que le nom de la route est 'app_register'
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new AppUser();

        $form = $this->createForm(AppUserType::class, $user);

        $form->remove('roles'); // On retire le champ des rôles pour une inscription standard

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']); // Rôle par défaut

            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
