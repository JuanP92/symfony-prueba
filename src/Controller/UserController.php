<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();
        return $this->json($users);
    }

    #[Route('/user/create', name: 'app_user_create')]
    public function create(
        Request $request,
        UserRepository $repository,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = $request->toArray();

        $user = new User();
        $user->setNombre($data['nombre']);
        $user->setApellido($data['apellido']);
        $user->setEmail($data['email']);
        $user->setSexo($data['sexo']);

        $errors = $validator->validate($user);

        if(count($errors) > 0){
            return new JsonResponse(['message'=>(string)$errors],400);
        }

        $repository->add($user, true);

        return $this->json($user);
    }
}
