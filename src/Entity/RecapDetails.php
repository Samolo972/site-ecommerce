<?php

namespace App\Entity;

use App\Repository\RecapDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecapDetailsRepository::class)]
class RecapDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recapDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderProduct = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $product = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $totalRecap = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderProduct(): ?Order
    {
        return $this->orderProduct;
    }

    public function setOrderProduct(?Order $orderProduct): static
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTotalRecap(): ?string
    {
        return $this->totalRecap;
    }

    public function setTotalRecap(string $totalRecap): static
    {
        $this->totalRecap = $totalRecap;

        return $this;
    }
}
