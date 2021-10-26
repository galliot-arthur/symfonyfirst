<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Categories;
use App\Repository\AnnoncesRepository;
use App\Service\AnnoncesService;
use App\Service\MessagesService;
use App\Service\PaginationService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
        Request $request,
        MessagesService $messagesService,
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

        $newMessage = $messagesService->showUnreadMessage($this->getUser());

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'annonces' => $annonces,
            'actual_page' => $page,
            'limit' => $limit,
            'total_annonces' => $totalAnnonces,
            'newMessage' => $newMessage,
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

    #[Route('/contact', name: 'app_home_contact')]
    public function contact(Request $request, MailerInterface $mailer):Response
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

        return $this->render('main/contact.html.twig', [

        ]);
    }
}
