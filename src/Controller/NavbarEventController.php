<?php

namespace App\Controller;

use App\Service\MessagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavbarEventController extends AbstractController
{
    public function isReadAction(MessagesService $messagesService): Response
    {

        $isRead = $messagesService->showUnreadMessage($this->getUser());
        return $this->render('_partials/_eventUnread.html.twig', [
            'isRead' => $isRead
        ]);
    }
}
