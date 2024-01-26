<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Form\AvailabilityType;
use App\Repository\AvailabilityRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/availability')]
class AvailabilityController extends AbstractController
{
    #[Route('/', name: 'app_availability_index', methods: ['GET'])]
    public function index(AvailabilityRepository $availabilityRepository): Response
    {
        return $this->render('availability/index.html.twig', [
            'availabilities' => $availabilityRepository->findAll(),
        ]);
    }

    #[Route('/group/{id}', name: 'app_availability_group', methods: ['GET'])]
    public function groupAvail(AvailabilityRepository $availabilityRepository, GroupRepository $groupRepository, $id): Response
    {

        $groups = $groupRepository->findOneBy(['id' => $id]);
        $availabilities = $availabilityRepository->findGroupAvailabilities($groups);
        
        return $this->render('availability/index.html.twig', [
            'availabilities' => $availabilities
        ]);
    }

    #[Route('/new', name: 'app_availability_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository
        
        ): Response
    {
        $availability = new Availability();

        $groups = $groupRepository->findOwnGroups($this->getUser());
        foreach($groups as $g) {
            $availability->addRelatedGroup($g);
        }

        $form = $this->createForm(AvailabilityType::class, $availability);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($availability);
            $entityManager->flush();

            return $this->redirectToRoute('app_availability_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availability/new.html.twig', [
            'availability' => $availability,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availability_show', methods: ['GET'])]
    public function show(Availability $availability): Response
    {
        return $this->render('availability/show.html.twig', [
            'availability' => $availability,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_availability_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Availability $availability, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvailabilityType::class, $availability);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_availability_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availability/edit.html.twig', [
            'availability' => $availability,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availability_delete', methods: ['POST'])]
    public function delete(Request $request, Availability $availability, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$availability->getId(), $request->request->get('_token'))) {
            $entityManager->remove($availability);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_availability_index', [], Response::HTTP_SEE_OTHER);
    }
}
