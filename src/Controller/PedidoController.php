<?php

namespace App\Controller;

use App\Document\Pedido;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PedidoController extends AbstractController
{
    #[Route('/pedido', name: 'app_pedido')]
    public function index(DocumentManager $manager): JsonResponse
    {
        $repository=$manager->getRepository(Pedido::class);

        $data = $repository->findAll();

        return $this->json($data);
    }

    #[Route('/pedido/create', name: 'app_pedido_create',methods: ['POST'])]
    public function create(
        Request $request,
        DocumentManager $manager,
        UserRepository $users
    ): JsonResponse
    {
        $data = $request->toArray();

        $user=$users->find($data['user-id']);

        if(!$user){
            return new JsonResponse([
                'success'=>false,
                'message'=>'User is not registered'], 400);
        }

        $pedido = new Pedido();
        $pedido->setNombreProducto($data['nombre-producto']);
        $pedido->setCantidad($data['cantidad']);
        $pedido->setPrecioUnitario($data['precio-unitario']);
        $pedido->setUserId($data['user-id']);


        $manager->persist($pedido);
        $manager->flush();

        return $this->json([
            'success' => true,
            'pedido'=> $pedido
        ]);
    }

    #[Route('/pedido/update/{id}', name: 'app_pedido_update',methods: ['PUT'])]
    public function update(
        string $id,
        Request $request,
        DocumentManager $manager
    ): JsonResponse
    {
        $data = $request->toArray();

        $pedido = $manager->find(Pedido::class,$id);

        if(!$pedido){
            return new JsonResponse([
                'success'=>false,
                'message'=>'Pedido not found'], 400);
        }

        $pedido->setNombreProducto($data['nombre-producto']);
        $pedido->setCantidad($data['cantidad']);
        $pedido->setPrecioUnitario($data['precio-unitario']);

        $manager->flush();

        return $this->json([
            'success' => true,
            'pedido'=> $pedido
        ]);
    }

    #[Route('/pedido/delete/{id}', name: 'app_pedido_delete')]
    public function delete(
        string $id,
        DocumentManager $manager
    ): JsonResponse
    {

        $pedido = $manager->find(Pedido::class,$id);

        if(!$pedido){
            return new JsonResponse([
                'success'=>false,
                'message'=>'Pedido not found'], 400);
        }

        $manager->remove($pedido);
        $manager->flush();

        return $this->json([
            'success' => true,
            'pedido'=> $pedido
        ]);
    }

}
