<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(ArtistRepository $artistRepository, EventRepository $eventRepository): JsonResponse
    {
        $artists = $artistRepository->findAll();
        $events = $eventRepository->findAll();

        $data = [
            'artists' => array_map(fn($artist) => [
                'id' => $artist->getId(),
                'name' => $artist->getName(),
                'description' => $artist->getDescription()

            ], $artists),

            'events' => array_map(fn($event) => [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'date' => $event->getDate()->format('Y-m-d'),
                'artist' => [
                    'id' => $event->getArtist()->getId(),
                    'name' => $event->getArtist()->getName(),
                ],
            ], $events),
        ];

        return JsonResponse::fromJsonString(json_encode($data), Response::HTTP_OK);

    }
    
    #[Route('/api/artists', name: 'artists', methods: ['GET'])]
    public function getArtists(ArtistRepository $artistRepository): JsonResponse
    {
        $artists = $artistRepository->findAll();

        $data = array_map(fn($artist) => [
            'id' => $artist->getId(),
            'name' => $artist->getName(),
            'description' => $artist->getDescription(),
            'picture' => $artist->getPicture(),
        ], $artists);

        return JsonResponse::fromJsonString(json_encode($data), Response::HTTP_OK);

    }

    #[Route('/api/artists/{id}', name: 'artist_detail', methods: ['GET'])]
    public function getArtist(int $id, ArtistRepository $artistRepository): JsonResponse
    {
        $artist = $artistRepository->find($id);

        if (!$artist) {
            return new JsonResponse(['error' => 'Artist not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $artist->getId(),
            'name' => $artist->getName(),
            'description' => $artist->getDescription(),
        ];

        return JsonResponse::fromJsonString(json_encode($data), Response::HTTP_OK);

    }

    #[Route('/api/events', name: 'events', methods: ['GET'])]
    public function getEvents(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();

        $data = array_map(fn($event) => [
            'id' => $event->getId(),
            'name' => $event->getName(),
            'date' => $event->getDate()->format('Y-m-d'),
            'artist' => [
                'id' => $event->getArtist()->getId(),
                'name' => $event->getArtist()->getName(),
            ],
        ], $events);

        return JsonResponse::fromJsonString(json_encode($data), Response::HTTP_OK);

    }

    #[Route('/api/events/{id}', name: 'event_detail', methods: ['GET'])]
    public function getEvent(int $id, EventRepository $eventRepository): JsonResponse
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $event->getId(),
            'name' => $event->getName(),
            'date' => $event->getDate()->format('Y-m-d'),
            'artist' => [
                'id' => $event->getArtist()->getId(),
                'name' => $event->getArtist()->getName(),
            ],
        ];

        return JsonResponse::fromJsonString(json_encode($data), Response::HTTP_OK);

    }
}
