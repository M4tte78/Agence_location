<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Form\AppUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/app/user')]
class AppUserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $appUsers = $entityManager->getRepository(AppUser::class)->findAll();

        return $this->render('app_user/index.html.twig', [
            'app_users' => $appUsers,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appUser = new AppUser();
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $this->passwordHasher->hashPassword($appUser, $appUser->getPassword());
            $appUser->setPassword($hashedPassword);

            $entityManager->persist($appUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('app_user/new.html.twig', [
            'app_user' => $appUser,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(AppUser $appUser): Response
    {
        return $this->render('app_user/show.html.twig', [
            'app_user' => $appUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AppUser $appUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password only if it has been changed
            if ($plainPassword = $form->get('password')->getData()) {
                $hashedPassword = $this->passwordHasher->hashPassword($appUser, $plainPassword);
                $appUser->setPassword($hashedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('app_user/edit.html.twig', [
            'app_user' => $appUser,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, AppUser $appUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $appUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($appUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
