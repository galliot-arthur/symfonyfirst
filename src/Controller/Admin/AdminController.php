<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @route("/admin", name="admin_")
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        
        return $this->render(
            'admin/index.html.twig'
        );
    }


    #[Route('/categories/ajout', name: 'categories_ajout')]
    public function ajoutCategorie(Request $request): Response
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_home');
        }

        return $this->render(
            'admin/categories/ajout.html.twig',
            [
                'form' => $form->createView(),
                'controller_name' => 'AdminController',
            ]
        );
    }
}
