<?php

namespace App\Service;

use App\Entity\Users;

class MessagesService
{

    public function showUnreadMessage($user): int
    {
        if (!$user) return false;

        foreach ($user->getReceived() as $message ) {
            if ($message->getIsRead() == false && $message->getSender() != $user) return $message->getId();

            foreach ($message->getChildren() as $child) {
                //dd($child);
                if ($child->getIsRead() == false && $child->getSender() != $user) return $message->getId();
            }
        }
        foreach ($user->getSent() as $message) {
            if ($message->getIsRead() == false  && $message->getSender() != $user) return $message->getId();

            foreach ($message->getChildren() as $child) {
                if ($message->getIsRead() == false && $child->getSender() != $user) return $message->getId();
            }
        }
        return false;
    }
}
