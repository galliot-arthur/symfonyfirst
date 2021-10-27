<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Regions;
use App\Form\CategoriesType;
use App\Form\RegionsType;
use App\Repository\RegionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @route("/admin/regions", name="admin_regions_")
 * @package App\Controller\Admin
 */
class RegionsController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(RegionsRepository $regionsRepository)
    {
        return $this->render(
            'admin/regions/index.html.twig',
            [
                'regions' => $regionsRepository->findAll(),
            ]
        );
    }


    /**
     * @Route("/ajout", name="ajout")
     */    public function ajoutRegion(Request $request): Response
    {
        $region = new Regions;

        $form = $this->createForm(RegionsType::class, $region);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            return $this->redirectToRoute('admin_regions_home');
        }

        return $this->render(
            'admin/regions/ajout.html.twig',
            [
                'form' => $form->createView()
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
    public function supprimer(Regions $region)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($region);
        $em->flush();

        $this->addFlash(
            'success',
            'Région supprimée avec succès'
        );
        return $this
            ->redirectToRoute('admin_regions_home');
    }
}
