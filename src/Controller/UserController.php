<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('api/user/search', name: 'app_user_search', methods: ["POST", "GET"])]
    public function search(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        Request $request

    ) : JsonResponse
    {
        $query = $request->query->get('q');
        $matchedUsers = $userRepository->searchUsers($query);
        $data = [];
        foreach($matchedUsers as $mu) {
            $user = [];
            $user['id'] = $mu->getId();
            $user['email'] = $mu->getEmail();
            $user["firstName"] = $mu->getProfile()->getFirstName();
            $user["lastName"] = $mu->getProfile()->getLastName();
            $user["picture"] = $mu->getProfile()->getPicture()->getSource();
            $data[] = $user;
        }
        $data = $serializer->serialize($data, 'json');
        // dd($data);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
