<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Comments;
use App\Form\CommentsType;
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

        $comment = new Comments;
        $form = $this->createForm(
            CommentsType::class,
            $comment
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment
                ->setAuthor($this->getUser())
                ->setAnnonces($annonce);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // re-init $comment & form
            $comment = new Comments;
            $form = $this->createForm(
                CommentsType::class,
                $comment
            );

            $this->addFlash(
                'success',
                'Vos commentaire à bien été posté.'
            );
            $this->redirectToRoute('annonces_view', ['id' => $annonce->getId()]);
        }

        $annonce->username = $annonce->getUsers()->getFirstname() . ' ' . $annonce->getUsers()->getName();

        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
            'images' => $annonce->getImages(),
            'form' => $form->createView(),
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
