<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response{
        return $this->home(); // A reviser
    }
    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {if ($this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        return $this->render('home/login.html.twig', []);
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {if ($this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        return $this->render('home/register.html.twig', []);
    }



    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {if (!$this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        return new Response("Bye");
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    { if (!$this->getUser()) {
        throw $this->createAccessDeniedException();
    }
        return $this->render('home/profile.html.twig', []);
    }
}
