<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response{
        //Cv
        return $this->redirect('home');
    }
    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        //Cv
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'You are already logged in.');
            return $this->redirectToRoute('home');
        }

        return $this->render('home/login.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'You are already registered and logged in.');
            return $this->redirectToRoute('home');
        }

        return $this->render('home/register.html.twig');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {

        throw new \LogicException('Logout is handled by Symfony firewall.');
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'You must be logged in to access your profile.');
            return $this->redirectToRoute('login');
        }
        return $this->render('home/profile.html.twig');
    }
}
