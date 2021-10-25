<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\AnnoncesTypesType;
use App\Form\EditProfileType;
use App\Repository\AnnoncesRepository;
use App\Service\AnnoncesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'users')]
    public function profile(AnnoncesService $annoncesService): Response
    {
        $annonces = $this->getUser()->getFavoris();
        $annoncesService->getAnnoncesDetails(
            $annonces,
            $this->getUser()->getId()
        );
        $user_annonces = $this->getUser()->getAnnonces();
        $annoncesService->getAnnoncesDetails(
            $user_annonces,
            $this->getUser()->getId()
        );

        return $this->render(
            'users/index.html.twig',
            [
                'annonces' => $annonces,
                'user_annonces' => $user_annonces,
            ]
        );
    }

    #[Route('/users/pass/modifier', name: 'users_pass_modifier')]
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            if ($request->request->get('pass') == $request->request->get('pass2')) {
                $user->setPassword($passwordEncoder
                    ->encodePassword(
                        $user,
                        $request->request->get('pass')
                    ));
                $em->flush();
                $this->addFlash(
                    'success',
                    'Mot de passe modifié avec succès.'
                );
                return $this->redirectToRoute('users');
            } else {
                $this->addFlash(
                    'error',
                    'Les deux mots de passes ne sont pas identiques.'
                );
            }
        }

        return $this->render(
            'users/editpass.html.twig'
        );
    }

    #[Route('/users/profil/modifier', name: 'users_profil_modifier')]
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('users');
        }


        return $this->render(
            'users/editprofile.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/users/annonces/ajout', name: 'users_annonces_ajout')]
    public function ajoutAnnonce(Request $request): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesTypesType::class, $annonce);
        $form->handleRequest($request);

        // Gestion des images
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // Nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                // On stock son nom dans la base de donnée
                $img = new Images;
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $annonce->setUsers(($this->getUser()));
            $annonce->setActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }


        return $this->render(
            'users/annonces/ajout.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
    #[Route('/users/annonces/modifier/{id}', name: 'users_annonces_modifier')]
    public function modifierAnnonce(Request $request, Annonces $annonce): Response
    {
        $form = $this->createForm(
            AnnoncesTypesType::class,
            $annonce
        );
        $form->handleRequest($request);

        // Gestion des images
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // Nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                // On stock son nom dans la base de donnée
                $img = new Images;
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $em = $this
                ->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('users');
        }


        return $this->render(
            'users/annonces/ajout.html.twig',
            [
                'form' => $form->createView(),
                'images' => $annonce->getImages(),
                'annonce' => $annonce,
            ]
        );
    }

    #[Route('/users/annonces/supprimer/{id}', name: 'users_annonces_delete')]
    public function deleteAnnonce(Annonces $annonce): Response
    {
        $images = $annonce->getImages();

        if ($images) {
            foreach ($images as $image) {
                $image_name = $this->getParameter("images_directory") . '/' . $image->getName();
                if (file_exists($image_name)) {
                    unlink($image_name);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();

        $this->addFlash(
            'success',
            'Cette annonce à bien été supprimée.'
        );

        return $this->redirectToRoute('users');
    }

    #[Route('/supprimer/image/{id}', name: 'annonces_delete_image')]
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        // On vérifie le token
        if ($this->isCsrfTokenValid(
            'delete' . $image->getId(),
            $data['_token']
        )) {
            // on delete l'image
            $name = $image->getName();
            unlink($this->getParameter('images_directory') . '/' . $name);
            // on delete l'entrée dans la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return new JsonResponse(
                ['success' => 1]
            );
        } else {
            return new JsonResponse(
                ['error' => 'Token Invalide'],
                400
            );
        }
    }



    #[Route('/users/annonces/like/{id}', name: 'users_annonces_like')]
    public function like(Annonces $annonce, AnnoncesRepository $annoncesRepo): Response
    {
        $isFavoriExists = $annoncesRepo->isFavoriExists(
            $annonce->getId(),
            $this->getUser()->getId()
        );
        if ($isFavoriExists) {
            $annonce->removeFavori($this->getUser());
        } else {
            $annonce->addFavori($this->getUser());
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        $this->addFlash(
            'success',
            "Liked Or Not"
        );

        return new Response("true");
    }
}
