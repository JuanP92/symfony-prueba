<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;

/** @ODM\Document */
class Pedido
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="int") */
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private $user_id;

    /** @ODM\Field(type="int") */
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private $cantidad;

    /** @ODM\Field(type="int") */
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private $precio_unitario;

    /** @ODM\Field(type="string") */
    #[Assert\NotBlank]
    private $nombre_producto;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param mixed $cantidad
     */
    public function setCantidad($cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return mixed
     */
    public function getPrecioUnitario()
    {
        return $this->precio_unitario;
    }

    /**
     * @param mixed $precio_unitario
     */
    public function setPrecioUnitario($precio_unitario): void
    {
        $this->precio_unitario = $precio_unitario;
    }

    /**
     * @return mixed
     */
    public function getNombreProducto()
    {
        return $this->nombre_producto;
    }

    /**
     * @param mixed $nombre_producto
     */
    public function setNombreProducto($nombre_producto): void
    {
        $this->nombre_producto = $nombre_producto;
    }

    public function getTotal(): int{
        return $this->cantidad*$this->precio_unitario;
    }


}