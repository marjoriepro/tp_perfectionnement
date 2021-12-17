<?php

namespace App\Controller\front;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\MediaRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{

    // Créer les deux fonctions qui affichent la liste des produits avec leur name,
    // prix et le name de leur type et le name de leur brand et celle qui affiche
    // grâce à l'id un produit en particulier et tous les informations de ce produit
    // (name, price, stock, description, name du type, name de brand et toutes 
    // les images du produits.)


    #[Route("front/medias", name: "front_media_list")]
    public function listMedia(MediaRepository $mediaRepository)
    {
        $medias = $mediaRepository->findAll();

        return $this->render("front/medias.html.twig", ['medias' => $medias]);
    }

    #[Route("front/media/{id}", name:"front_media_show")]
    public function showMedia(MediaRepository $mediaRepository, $id)
    {
        $media= $mediaRepository->find($id);

        return $this->render("front/media.html.twig", ['media' => $media]);
    }

    #[Route("/front/search/", name:"front_search")]
    public function frontSearch(MediaRepository $mediaRepository, Request $request)
    {
        // Récupérer les données rentrées dans le formulaire
        $term = $request->query->get('term');

        $medias = $mediaRepository->searchByTerm($term);

        return $this->render('front/search.html.twig', ['medias' => $medias]);
    }

}