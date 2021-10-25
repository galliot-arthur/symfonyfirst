<?php

namespace App\Service;

class AnnoncesService
{

    /**
     * Get first picture, category and owner of each Annonces in a array.
     *
     * @param array $annonces 
     * @return void
     */
    public function getAnnoncesDetails($annonces, $userId) {
        foreach ($annonces as $key => $annonce) {
            $annonces[$key]->category = $annonce
                ->getCategories()
                ->getName();
            $annonces[$key]->image = $annonce
                ->getImages()['0']
                ->getName();
            $annonces[$key]->owner = $annonce
                ->getUsers()->getFirstname() . ' ' . $annonce->getUsers()->getName();
            $annonces[$key]->heart = $this->doILikeThis($annonce, $userId);
        }
        return $annonces;
    }

    public function doILikeThis($annonce, $userId) {
        if ($userId == null) return false;
        foreach($annonce->getFavoris() as $user) {
            if ($user->getId() == $userId) 
                return true;
        } 
        return false;
    }
}
