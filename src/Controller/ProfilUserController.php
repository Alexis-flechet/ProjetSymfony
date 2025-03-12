<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfilUserController extends AbstractController
{
    #[Route('/profilUser', name: 'app_profil_user')]
    public function index(): Response
    {
        return $this->render('profil_user/index.html.twig', [
            'controller_name' => 'ProfilUserController',
        ]);
    }

    #[Route('/profilUser/createEvent', name: 'app_create_event')]
    public function createEvent(Request $request, EntityManagerInterface $em): Response

    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $event->setCreator($this->getUser());
            $event->addAttendee($this->getUser());

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès !');

            return $this->redirectToRoute('app_profil_user');
        }


        return $this->render('profil_user/createEvent.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
