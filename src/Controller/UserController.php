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
    #[Route('/user/list', name: 'app_user_list',methods: ['GET'])]
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
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        $errors = $validator->validate($user);
        if($errors->count()>0){
            $msg=[];
            foreach ($errors as $error){
                $msg[]=$error->getMessage();
            }
            return $this->json(['errors'=>$msg],400);
        }

        $repository->add($user, true);

        return $this->json(['success'=>true,'data'=>$user]);
    }

    #[Route('/user/update/{id}', name: 'app_user_update',methods: ['PUT'])]
    public function update(
        int $id,
        ManagerRegistry $doctrine,
        UserRepository $repository,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $manager = $doctrine->getManager();
        $user = $repository->find($id);
        if(!$user){
            return $this->json(['message'=>'User not found'], 400);
        }

        $data = $request->toArray();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        $errors = $validator->validate($user);
        if(count($errors) > 0){
            $msg=[];
            foreach ($errors as $error){
                $msg[]=$error->getMessage();
            }
            return $this->json([
                    'errors'=>$msg],400);
        }

        $manager->flush();

        return $this->json(['success'=>true,'user'=>$user]);
    }

    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function delete(int $id, UserRepository $repository): JsonResponse
    {
        $user = $repository->find($id);
        if(!$user){
            return $this->json([
                'message'=>'User not found'], 400);
        }

        $repository->remove($user, true);

        return $this->json(['success'=>true,'user'=>$user]);
    }

    #[Route('/user/detail/{email}', name: 'app_user_get',methods: ['GET'])]
    public function get(
        string $email,
        UserRepository $repository
    ): JsonResponse
    {
        $user = $repository->findOneBy(['email'=>$email]);
        if(!$user){
            return $this->json([
                'message'=>'User not found'], 400);
        }

        return $this->json($user);
    }
}
