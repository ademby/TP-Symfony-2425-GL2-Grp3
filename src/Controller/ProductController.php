<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/product")
 */
final class ProductController extends AbstractController
{
    /**
     * @Route("/add", name="prod_add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(): Response
    {
        return $this->render('product/add.html.twig');
    }

    /**
     * @Route("/update", name="prod_update")
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(): Response
    {
        return $this->render('product/update.html.twig');
    }

    /**
     * @Route("/delete", name="prod_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(): Response
    {
        return new Response("Wanna Deleted Product ? ..");
    }

    /**
     * @Route("/", name="prod_show_all")
     */
    public function show_all(): Response
    {
        return $this->render('product/show_all.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * @Route("/id/{id}", name="prod_show_prod", requirements={"id"="\d+"})
     */
    public function show(int $id): Response
    {
        return $this->render('product/show_prod.html.twig', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/category/{cat_name}", name="prod_show_cat")
     */
    public function show_cat(string $cat_name): Response
    {
        return $this->render('product/show_cat.html.twig', [
            'cat_name' => $cat_name
        ]);
    }
}
