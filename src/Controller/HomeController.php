<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HomeController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

//    #[Route('/login', name: 'login')]
//    public function login(AuthenticationUtils $authenticationUtils): Response
//    {
//        if ($this->getUser()) {
//            $this->addFlash('warning', 'You are already logged in.');
//            return $this->redirectToRoute('home');
//        }
//        $error = $authenticationUtils->getLastAuthenticationError();
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
//    }

    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('warning', 'You are already registered and logged in.');
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstName($request->request->get('firstName'));
            $user->setLastName($request->request->get('lastName'));
            $user->setAge($request->request->get('age'));
            $user->setRegion($request->request->get('region'));
            $user->setPhone($request->request->get('phone'));


            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            );
            $user->setPassword($hashedPassword);


            $user->setRoles(['ROLE_USER']);


            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error->getMessage());
                }
            } else {
                // Save the user
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Registration successful! You can now login.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('home/register.html.twig');
    }

//    #[Route('/logout', name: 'logout')]
//    public function logout(): void
//    {
//    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'You must be logged in to access your profile.');
            return $this->redirectToRoute('login');
        }

        return $this->render('home/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
