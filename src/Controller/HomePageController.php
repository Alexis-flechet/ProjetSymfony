<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(EventRepository $eventRepository, ?UserInterface $user): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('home_page/index.html.twig', [
            'events' => $events,
            'user' => $user,
        ]);
    }

    #[Route('/event/{id}/subscribe', name: 'app_event_subscribe')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function subscribe(Event $event, EntityManagerInterface $entityManager, UserInterface $user): Response
    {

        if (!$event->getParticipants()->contains($user)) {
            $event->addParticipant($user);
            $user->addEvent($event);
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes inscrit à cet événement.');
        } else {
            $this->addFlash('info', 'Vous êtes déjà inscrit à cet événement.');
        }

        return $this->redirectToRoute('app_home_page');
    }

    #[Route('/join/{eventId}', name: 'app_join_event')]
    public function joinEvent(int $eventId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        if (!$event->getParticipants()->contains($user)) {
            $event->addParticipant($user);
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes maintenant inscrit à cet événement.');
        } else {
            $this->addFlash('info', 'Vous participez déjà à cet événement.');
        }

        return $this->redirectToRoute('app_home_page');
    }
}
