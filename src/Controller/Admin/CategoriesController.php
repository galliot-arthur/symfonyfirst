<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Entity\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @route("/admin/categories", name="admin_categories_")
 * @package App\Controller\Admin
 */
class CategoriesController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $catsRepo)
    {
        return $this->render(
            'admin/categories/index.html.twig',
            [
                'categories' => $catsRepo->findAll(),
            ]
        );
    }


    /**
     * @Route("/ajout", name="ajout")
     */    public function ajoutCategorie(Request $request): Response
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render(
            'admin/categories/ajout.html.twig',
            [
                'form' => $form->createView(),
                'controller_name' => 'AdminController',
            ]
        );
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */    public function modifierCategorie(Categories $categorie, Request $request): Response {
        $form = $this
            ->createForm(
                CategoriesType::class,
                $categorie
            );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this
                ->redirectToRoute('admin_categories_home');
        }

        return $this->render(
            'admin/categories/ajout.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Categories $categorie)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();

        $this->addFlash(
            'success',
            'Catégorie supprimée avec succès'
        );
        return $this
            ->redirectToRoute('admin_categories_home');
    }
}
