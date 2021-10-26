<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Categories;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use App\Service\AnnoncesService;
use App\Service\MessagesService;
use App\Service\PaginationService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
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
        CategoriesRepository $categoriesRepository,
        Request $request,
    ): Response {
        // Get all categories to feed the search form
        $categories = $categoriesRepository->findAll();
        // Search Filter
        /////////////////////////////////
        // Get all filters
        $title = $request->get('title');
        $category = $request->get('categories');
        // Get pagination details
        $limit = 8;

        $totalAnnonces = $annoncesRepo
            ->countOnSearch($title, $category);
        //dd($totalAnnonces);
        if ($request->get('ajax') && $totalAnnonces < 1) {
            return new JsonResponse([
                'content' => $this->renderView(
                    'main/_content.html.twig',
                    [
                        'annonces' => [],
                        'categories' => $categories,
                        'actual_page' => 1,
                        'limit' => $limit,
                        'total_annonces' => 1,
                    ]
                )
            ]);
        }
        // Protect page and set min and max value
        $page = $paginService
            ->setPageMinAndMax(
                $page,
                $totalAnnonces,
                $limit
            );
        // Get all announces
        $annonces = $annoncesRepo->getPaginatedAnnonces($page, $limit, $title, $category);



        // Handling data
        /////////////////////////////////
        // Get user
        $userId = $this->getUser() ?
            $this->getUser()->getId() :
            null;
        // Get all the details on announces
        $annoncesService->getAnnoncesDetails(
            $annonces,
            $userId
        );


        // We the page have been launched with an ajax;
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView(
                    'main/_content.html.twig',
                    [
                        'annonces' => $annonces,
                        'categories' => $categories,
                        'actual_page' => $page,
                        'limit' => $limit,
                        'total_annonces' => $totalAnnonces,
                    ]
                )
            ]);
        }

        return $this->render(
            'main/index.html.twig',
            [
                'annonces' => $annonces,
                'categories' => $categories,
                'actual_page' => $page,
                'limit' => $limit,
                'total_annonces' => $totalAnnonces,
            ]
        );
    }

    #[Route('/contact', name: 'app_home_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->request->all()) {
            $email = new TemplatedEmail();
            $email
                ->from(new Address($this->getUser()->getEmail()))
                ->to(new Address("galliot.arthur@gmail.com"))
                ->subject($request->request->get('object'))
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                    'message' => $request->request->get('content'),
                ]);
            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre message à bien été envoyé'
            );
            $this->redirectToRoute('app_home');
        }

        return $this->render('main/contact.html.twig', []);
    }
}
