<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Categories;
use App\Repository\AnnoncesRepository;
use App\Service\AnnoncesService;
use App\Service\PaginationService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(
        '/{page}',
        name: 'app_home',
        defaults: ['page' => 1],
        requirements: ['page' => '\d+'],
    )]

    public function index(
        int $page,
        PaginationService $paginService,
        AnnoncesService $annoncesService,
        AnnoncesRepository $annoncesRepo,
        Request $request
    ): Response {

        // Counting pages without search situation
        $limit = 8;
        $totalAnnonces = $annoncesRepo
            ->count(['active' => 1]);
        $page = $paginService
            ->setPageMinAndMax(
                $page,
                $totalAnnonces,
                $limit
            );

        // Generating form & opening the entity
        $annoncesSearch = new Annonces;
        $form = $this
            ->createSearchForm($annoncesSearch);
        $form->handleRequest($request);

        // In case the user has made a field search
        if ($form->isSubmitted()) {
            // Set valid values for category
            if ($annoncesSearch->getCategories() == null)
                $categoriesValue = null;
            else $categoriesValue = $annoncesSearch
                ->getCategories()
                ->getId();

            // Get the total count of announces in this search context
            $totalAnnonces = $annoncesRepo
                ->countOnSearch(
                    $annoncesSearch->getTitle(),
                    $categoriesValue
                );

            // Get the matching announces
            $annonces = $annoncesRepo->search(
                $annoncesSearch->getTitle(),
                $categoriesValue,
                $page,
                $limit
            );
        } else $annonces = $annoncesRepo
            ->getPaginatedAnnonces($page, $limit);

        
        $userId = $this->getUser() ?
            $this->getUser()->getId() :
            null;
            
        // Get all the details on announces
        $annoncesService->getAnnoncesDetails(
            $annonces,
            $userId
        );

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'annonces' => $annonces,
            'actual_page' => $page,
            'limit' => $limit,
            'total_annonces' => $totalAnnonces,
        ]);
    }


    /**
     * Create a form with parameters to search annonces.
     *
     * @param array $annoncesSearch
     * @return object
     */
    private function createSearchForm($annoncesSearch)
    {
        return $this->createFormBuilder($annoncesSearch)
            ->add('title', SearchType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'required'   => false,
                'empty_data' => 'null',
                'placeholder' => 'Catégorie',
            ])
            ->getForm();
    }
}
