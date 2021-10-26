<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Comments;
use App\Entity\Messages;
use App\Form\CommentsType;
use App\Form\MessagesType;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnoncesController extends AbstractController
{
    #[Route('/annonces/{id}', name: 'annonces_view')]
    public function show(Annonces $annonce, Request $request): Response
    {
        $message = new Messages;
        $messageForm = $this->createForm(MessagesType::class, $message);
        $messageForm->handleRequest($request);

        if($messageForm->isSubmitted() && $messageForm->isValid()) {
            $message->setSender($this->getUser());
            $message->setRecipient($annonce->getUsers());
            $message->setTitle($annonce->getTitle());
            $message->addObject($annonce);
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre message à bien été envoyé'
            );
            $this->redirectToRoute('annonces_view', ['id' => $annonce->getId()]);
        }

        $annonce->username = $annonce->getUsers()->getFirstname() . ' ' . $annonce->getUsers()->getName();

        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
            'images' => $annonce->getImages(),
            'form' => $messageForm->createView(),
            'comments' => $annonce->getComments(),
        ]);
    }

    #[Route('/annonces/supprimer/{id}/{id_annonce}', name: 'annonces_comment_delete')]
    public function commentDelete(Comments $comment, int $id_annonce, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        $this->addFlash(
            'success',
            'Message supprimée avec succès.'
        );

        return $this->redirectToRoute('annonces_view', ['id' => $id_annonce]);
    }
}
