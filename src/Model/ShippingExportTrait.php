<?php

declare(strict_types=1);

namespace Ikuzo\SyliusColishipPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ShippingExportTrait {

    /**
     * @ORM\Column(name="weight", type="float", nullable=true)
     **/
    protected $weight = null;

    /**
     * @ORM\Column(name="coliship_pickup_raw", type="json", nullable=true)
     */
    private $colishipPickupRaw = [];

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight = null): void
    {
        $this->weight = $weight;
    }

    public function getColishipPickupRaw(): ?array
    {
        return $this->colishipPickupRaw;
    }

    public function setColishipPickupRaw(?array $colishipPickupRaw): self
    {
        $this->colishipPickupRaw = $colishipPickupRaw;

        return $this;
    }
}
