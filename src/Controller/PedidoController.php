<?php

namespace App\Controller;

use App\Document\Pedido;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PedidoController extends AbstractController
{
    #[Route('/pedido', name: 'app_pedido')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PedidoController.php',
        ]);
    }

    #[Route('/pedido/create', name: 'app_pedido_create',methods: ['POST'])]
    public function create(
        Request $request,
        ManagerRegistry $doctrine,
    ): JsonResponse
    {
        $manager = $doctrine->getManager();
        $data = $request->toArray();

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
}
