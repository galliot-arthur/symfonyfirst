<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessagesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractController
{
    #[Route('/users/messages', name: 'users_messages_home')]
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'MessagesController',
        ]);
    }

    #[Route('/users/messages/envoyer', name: 'users_messages_send')]
    public function send(Request $request): Response
    {
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre message à bien été envoyé'
            );
            $this->redirectToRoute('users_messages_home');
            //dd("message sent");
        }

        return $this->render('messages/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('users/messages/{id}', name: 'users_message_read')]
    public function read(Messages $message): Response
    {
        $message->setIsRead(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();


        return $this->render('messages/read.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('users/messages/supprimer/{id}', name: 'users_message_delete')]
    public function delete(Messages $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();


        return $this->redirectToRoute('users_messages_home');
    }

}
