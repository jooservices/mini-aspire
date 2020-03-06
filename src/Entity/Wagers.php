<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WagersRepository")
 */
class Wagers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"comment":"The string to show in the dropdown"})
     */
    private $total_wager_value;

    /**
     * @ORM\Column(type="integer", options={"comment":"No idea yet"})
     */
    private $odds;

    /**
     * @ORM\Column(type="integer", options={"comment":"Percent of wager want to sell"})
     */
    private $selling_percentage;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"How much for selling above percent"})
     */
    private $selling_price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"How many price left after sold"})
     */
    private $current_selling_price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"No idea yet"})
     */
    private $percentage_sold;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount_sold;

    /**
     * @ORM\Column(type="integer")
     */
    private $placed_at;

    /**
     * Wagers constructor.
     */
    public function __construct()
    {
        $this->placed_at       = time();
        $this->percentage_sold = 0.0;
        $this->amount_sold     = 0.0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalWagerValue(): ?int
    {
        return $this->total_wager_value;
    }

    public function setTotalWagerValue(int $total_wager_value): self
    {
        $this->total_wager_value = $total_wager_value;

        return $this;
    }

    public function getOdds(): ?int
    {
        return $this->odds;
    }

    public function setOdds(int $odds): self
    {
        $this->odds = $odds;

        return $this;
    }

    public function getSellingPercentage(): ?int
    {
        return $this->selling_percentage;
    }

    public function setSellingPercentage(int $selling_percentage): self
    {
        $this->selling_percentage = $selling_percentage;

        return $this;
    }

    public function getSellingPrice(): ?float
    {
        return $this->selling_price;
    }

    public function setSellingPrice(float $selling_price): self
    {
        $this->selling_price = $selling_price;

        return $this;
    }

    public function getCurrentSellingPrice(): ?float
    {
        return $this->current_selling_price;
    }

    public function setCurrentSellingPrice(float $current_selling_price): self
    {
        $this->current_selling_price = $current_selling_price;

        return $this;
    }

    public function getPercentageSold(): ?float
    {
        return $this->percentage_sold;
    }

    public function setPercentageSold(?float $percentage_sold): self
    {
        $this->percentage_sold = $percentage_sold;

        return $this;
    }

    public function getAmountSold(): ?float
    {
        return $this->amount_sold;
    }

    public function setAmountSold(?float $amount_sold): self
    {
        $this->amount_sold = $amount_sold;

        return $this;
    }

    public function getPlacedAt(): ?int
    {
        return $this->placed_at;
    }

    public function setPlacedAt(int $placed_at): self
    {
        $this->placed_at = $placed_at;

        return $this;
    }
}
