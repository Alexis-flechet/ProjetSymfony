<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(EventRepository $eventRepository, ?UserInterface $user, Request $request): Response
    {
        $sort = $request->query->get('sort', 'asc');

        $events = $eventRepository->findBy([], ['date' => $sort]);

        return $this->render('home_page/index.html.twig', [
            'events' => $events,
            'user' => $user,
            'sort' => $sort,
        ]);
    }

    #[Route('/event/{id}/subscribe', name: 'app_subscribe_event')]
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

    // src/Controller/EventController.php

    #[Route('/event/{id}', name: 'app_event_detail')]
    public function eventDetail(int $id, EventRepository $eventRepository, UserInterface $user): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            $this->addFlash('error', 'Événement non trouvé.');
            return $this->redirectToRoute('app_home_page');
        }

        $isParticipant = $event->getParticipants()->contains($user);

        return $this->render('home_page/detail.html.twig', [
            'event' => $event,
            'isParticipant' => $isParticipant,
            'user' => $user,
        ]);
    }


}
