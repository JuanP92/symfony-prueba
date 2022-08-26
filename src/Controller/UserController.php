<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/user/list', name: 'app_user_list', methods: ['GET'])]
    public function index(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();

        return $this->json($users);
    }

    #[Route('/user/create', name: 'app_user_create', methods: ['POST'])]
    public function create(
        Request        $request,
        UserRepository $repository
    ): JsonResponse
    {
        $data = $request->toArray();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            $msg = [];
            foreach ($errors as $error) {
                $msg[] = $error->getMessage();
            }
            return $this->json(['errors' => $msg], 400);
        }

        $repository->add($user, true);

        return $this->json(['success' => true, 'data' => $user]);
    }

    #[Route('/user/update/{id}', name: 'app_user_update', methods: ['PUT'])]
    public function update(
        User            $user,
        ManagerRegistry $doctrine,
        Request         $request
    ): JsonResponse
    {
        $manager = $doctrine->getManager();
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $data = $request->toArray();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $form->getErrors(true);
            $msg = [];
            foreach ($errors as $error) {
                $msg[] = $error->getMessage();
            }

            return $this->json(['errors' => $msg], 400);
        }

        $manager->flush();

        return $this->json(['success' => true, 'user' => $user]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(
        User           $user,
        UserRepository $repository
    ): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'message' => 'User not found'], 404);
        }

        $repository->remove($user, true);

        return $this->json(['success' => true, 'user' => $user]);
    }

    #[Route('/user/detail/{email}', name: 'app_user_get', methods: ['GET'])]
    public function get(
        User $user
    ): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'message' => 'User not found'], 404);
        }

        return $this->json($user);
    }
}
