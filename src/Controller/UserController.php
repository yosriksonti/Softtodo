<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/back/user', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        $users = $entityManager
                ->getRepository(User::class)
                ->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/back/user/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $image->setUserid($user);
                $entityManager->persist($image);
            }
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            ));
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $userRepository->add($user, true);
            if(!empty($this->user)) {
                $this->user = $usr;
                return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/back/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile( EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    #[Route('/back/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setUserid($user);
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $entityManager->persist($image);
            }
            if($form->get('password')->getData()!=null) {
                $user->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));
            }
            $user->setUpdatedAt(new \DateTime());
            $entityManager->flush();

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if($image != null) {
                $image->setUserid($user);
                $image->setCreatedAt(new \DateTime());
                $image->setUpdatedAt(new \DateTime());
                $entityManager->persist($image);
            }
            if($form->get('password')->getData()!=null) {
                $user->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));
            }
            $user->setUpdatedAt(new \DateTime());
            $entityManager->flush();

        return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/back/user/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
