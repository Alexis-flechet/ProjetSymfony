<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistFormType;
use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


final class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'app_artists')]
    public function index(ArtistRepository $artistRepository,Request $request): Response
    {
        $search = $request->get('search');

        if ($search) {
            $artists = $artistRepository->findBySearch($search);
        } else {
            $artists = $artistRepository->findAll();
        }

        return $this->render('artist/index.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/artist/create', name: 'app_create_artist')]
    public function createArtist(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $artist = new Artist();
        $form = $this->createForm(ArtistFormType::class, $artist);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_create_artist');
                }


                $artist->setPicture($newFilename);
            }

            $entityManager->persist($artist);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste créé avec succès !');
            return $this->redirectToRoute('app_artists');
        }

        return $this->render('artist/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/artists/edit/{id}', name: 'app_edit_artist')]
    public function edit(int $id, ArtistRepository $artistRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $artist = $artistRepository->find($id);

        if (!$artist) {
            $this->addFlash('error', 'Artiste non trouvé.');
            return $this->redirectToRoute('app_artists');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès interdit.');
            return $this->redirectToRoute('app_home_page');
        }

        $form = $this->createForm(ArtistFormType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                if ($artist->getPicture()) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $artist->getPicture();

                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_create_artist');
                }


                $artist->setPicture($newFilename);
            }

            $entityManager->persist($artist);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste modifié avec succès.');

            return $this->redirectToRoute('app_artists');
        }

        return $this->render('artist/editArtist.html.twig', [
            'form' => $form->createView(),
            'artist' => $artist,
        ]);
    }
    #[Route('/artists/delete/{id}', name: 'app_delete_artist', methods: ['POST'])]
    public function delete(int $id, ArtistRepository $artistRepository, EntityManagerInterface $entityManager, EventRepository $eventRepository, ): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Accès interdit.');
            return $this->redirectToRoute('app_artists');
        }

        $artist = $artistRepository->find($id);

        if (!$artist) {
            $this->addFlash('error', 'Artiste non trouvé.');
            return $this->redirectToRoute('app_artists');
        }

        $events = $eventRepository->findBy(['artist' => $artist]);

        foreach ($events as $event) {
            $entityManager->remove($event);
        }
        $imagePath = $this->getParameter('images_directory') . '/' . $artist->getPicture();

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $entityManager->remove($artist);
        $entityManager->flush();

        $this->addFlash('success', 'Artiste supprimé avec succès.');

        return $this->redirectToRoute('app_artists');
    }

    #[Route('/artists/{id}', name: 'app_artist_detail')]
    public function show(int $id, ArtistRepository $artistRepository, EventRepository $eventRepository): Response
    {
        $artist = $artistRepository->find($id);

        if (!$artist) {
            $this->addFlash('error', 'Artiste non trouvé.');
            return $this->redirectToRoute('app_artists');
        }

        $events = $eventRepository->findBy(['artist' => $artist]);

        return $this->render('artist/detailArtist.html.twig', [
            'artist' => $artist,
            'events' => $events,
        ]);
    }
}
