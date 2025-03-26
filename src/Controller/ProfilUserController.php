<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProfilUserController extends AbstractController
{
    #[Route('/profilUser', name: 'app_profil_user')]
    public function index(UserInterface $user, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findEventsByUser($user->getId());

        return $this->render('profil_user/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/profilUser/createEvent', name: 'app_create_event')]
    public function createEvent(Request $request, EntityManagerInterface $entityManager, ArtistRepository $artistRepository, EventRepository $eventRepository, UserInterface $user): Response
    {
        // Récupère les événements créés par l'utilisateur connecté
        $events = $eventRepository->findBy(['creator' => $user]);

        $event = new Event();

        // Créer le formulaire pour l'événement
        $form = $this->createForm(EventFormType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'artiste sélectionné dans le formulaire
            $artist = $event->getArtist();  // On récupère l'artiste de l'événement

            if (!$artist) {
                // Si aucun artiste n'est sélectionné, afficher un message d'erreur
                $this->addFlash('error', 'Veuillez sélectionner un artiste.');
                return $this->redirectToRoute('app_create_event');
            }

            // Associer l'artiste et l'utilisateur à l'événement
            $event->setArtist($artist);
            $event->setCreator($user);
            $event->addParticipant($user);  // Ajouter l'utilisateur comme participant par défaut

            // Persister l'événement dans la base de données
            $entityManager->persist($event);
            $entityManager->flush();

            // Redirection après la création de l'événement
            return $this->redirectToRoute('app_profil_user');
        }

        return $this->render('profil_user/createEvent.html.twig', [
            'form' => $form->createView(),
            'events' => $events,  // Passer les événements créés à la vue
        ]);
    }

    #[Route('/profilUser/unsubscribe/{eventId}', name: 'app_unsubscribe_event')]
    public function unsubscribeEvent(int $eventId, UserInterface $user, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($eventId);

        if (!$event) {
            $this->addFlash('error', 'Event not found.');
            return $this->redirectToRoute('app_profil_user');
        }

        if ($event->getParticipants()->contains($user)) {
            $event->removeParticipant($user);
            $user->removeEvent($event);

            $entityManager->flush();

            $this->addFlash('success', 'You have successfully unsubscribed from the event.');
        } else {
            $this->addFlash('error', 'You are not participating in this event.');
        }

        return $this->redirectToRoute('app_profil_user');
    }

    #[Route('/profilUser/editEvent/{eventId}', name: 'app_edit_event')]
    public function editEvent(int $eventId, Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, ArtistRepository $artistRepository): Response
    {
        $event = $eventRepository->find($eventId);

        if ($event->getCreator() !== $this->getUser()) {
            return $this->redirectToRoute('app_profil_user');
        }

        $form = $this->createForm(EventFormType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artist = $artistRepository->find(1);
            $event->setArtist($artist);

            $entityManager->flush();

            return $this->redirectToRoute('app_create_event');
        }

        return $this->render('profil_user/editEvent.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    #[Route('/profilUser/deleteEvent/{eventId}', name: 'app_delete_event')]
    public function deleteEvent(int $eventId, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($eventId);

        if ($event->getCreator() !== $this->getUser()) {
            return $this->redirectToRoute('app_profil_user');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'Event deleted successfully.');

        return $this->redirectToRoute('app_create_event');
    }

}
