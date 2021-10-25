<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use App\Service\AnnoncesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @route("/admin/annonces", name="admin_annonces_")
 * @package App\Controller\Admin
 */
class AnnoncesController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepo, AnnoncesService $annoncesService)
    {
        $annonces =  $annoncesRepo->findBy([], ['id' => 'DESC']);
        $annoncesService->getAnnoncesDetails($annonces, $this->getUser()->getId());
        return $this->render(
            'admin/annonces/index.html.twig', [
                'annonces' => $annonces,
            ]
        );
    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonce)
    {
        $annonce->setActive(($annonce->getActive()) ? false : true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return new Response("true");
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimer(Annonces $annonce)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();

        $this->addFlash('success', 'Annonce supprimée avec succès');
        return $this->redirectToRoute('admin_annonces_home');
    }
}
