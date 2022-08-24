<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user',methods: ['GET'])]
    public function index(UserRepository $repository): JsonResponse
    {
        $users = $repository->findAll();
        return $this->json($users);
    }

    #[Route('/user/create', name: 'app_user_create',methods: ['POST'])]
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
            $msg=[];
            foreach ($errors as $error){
                $msg[]=$error->getMessage();
            }
            return new JsonResponse(
                ['success'=>false,
                'errors'=>$msg],
                400);
        }

        $repository->add($user, true);

        return $this->json(['success'=>true,'data'=>$user]);
    }

    #[Route('/user/update/{id}', name: 'app_user_update',methods: ['PUT'])]
    public function update(
        int $id,
        ManagerRegistry $doctrine,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $repository = $doctrine->getRepository(User::class);
        $manager = $doctrine->getManager();
        $user = $repository->find($id);

        if(!$user){
            return new JsonResponse([
                'success'=>false,
                'message'=>'User not found'], 400);
        }

        $data = $request->toArray();
        $user->setNombre($data['nombre']);
        $user->setApellido($data['apellido']);
        $user->setEmail($data['email']);
        $user->setSexo($data['sexo']);

        $errors = $validator->validate($user);

        if(count($errors) > 0){
            $msg=[];
            foreach ($errors as $error){
                $msg[]=$error->getMessage();
            }
            return new JsonResponse(
                ['success'=>false,
                    'errors'=>$msg],
                400);
        }

        $manager->flush();

        return $this->json(['success'=>true,'user'=>$user]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function delete(int $id, UserRepository $repository): JsonResponse {
        $user = $repository->find($id);

        if(!$user){
            return new JsonResponse([
                'success'=>false,
                'message'=>'User not found'], 400);
        }

        $repository->remove($user, true);

        return $this->json(['success'=>true,'user'=>$user]);
    }

    #[Route('/user/{email}', name: 'app_user_get',methods: ['GET'])]
    public function get(
        string $email,
        UserRepository $repository
    ): JsonResponse
    {
        $user = $repository->findOneBy(['email'=>$email]);
        if(!$user){
            return new JsonResponse([
                'success'=>false,
                'message'=>'User not found'], 400);
        }

        $json = new JsonResponse([
            'id'=>$user->getId(),
            'full_name'=>$user->getNombre().' '.$user->getApellido(),
            'sexo'=>$user->getSexo()
        ]);

        return $json;
    }

}
