<?php

namespace App\Controller\admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminMediaController extends AbstractController
{


     #[Route("/admin/medias/", name: "admin_media_list")]
    public function listMedias(MediaRepository $mediaRepository, Request $request){
        $medias = $mediaRepository->findAll();
        return $this->render('admin/medias.html.twig',['medias' => $medias]);
    }

    #[Route("admin/media/{id}", name: "admin_media_show")]

    public function showMedia($id, MediaRepository $mediaRepository)
    {
        $media = $mediaRepository->find($id);

        return $this->render('admin/media.html.twig', ['media' => $media]);
    }

    #[Route("admin/create/media", name:"admin_media_create")]
        public function createMedia(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $media = new Media();

        $mediaForm = $this->createForm(MediaType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

            $mediaFile = $mediaForm->get('src')->getData();

            if ($mediaFile) {
                // On créé un nom unique avec le nom original de l'image pour éviter
                // tout problème
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // on utilise slug sur le nom original de l'image pour avoir un nom valide
                $safeFilename = $sluggerInterface->slug($originalFilename);
                // on ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();


                // On déplace le fichier dans le dossier public/media
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml
                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }

            $media->setAlt($mediaForm->get('title')->getData());

            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_media_list");
        }

        return $this->render('admin/mediaform.html.twig', ['mediaForm' => $mediaForm->createView()]);
    }

    #[Route("/admin/update/media/{id}", name: "admin_media_update")]

    public function updateMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $media = $mediaRepository->find($id);
        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if($mediaForm->isSubmitted() && $mediaForm->isValid())
        {
            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_media_list");
        }

        return $this->render('admin/mediaform.html.twig', ['mediaForm' => $mediaForm->createView()]);

    }

    #[Route("/admin/delete/media/{id}", name: "admin_media_delete")]

    public function deleteMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManagerInterface)
    {
    $media = $mediaRepository->find($id);
    $entityManagerInterface->remove($media);
    $entityManagerInterface->flush();

    return $this->redirectToRoute("admin_media_list");

    }
}