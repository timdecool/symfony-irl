<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Form\AddAvailabilityType;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        EntityManagerInterface $entityManager,
        HttpFoundationRequest $request
    ): Response
    {
        $availability = new Availability();
        $availForm = $this->createForm(AddAvailabilityType::class, $availability);
        $availForm->handleRequest($request);

        if ($availForm->isSubmitted() && $availForm->isValid()) {
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'availForm' => $availForm
        ]);
    }
}
