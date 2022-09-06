<?php

declare(strict_types=1);

namespace Tests\Ikuzo\SyliusColishipPlugin\Application\Entity;

use Sylius\Component\Core\Model\Shipment as BaseShipment;
use Ikuzo\SyliusColishipPlugin\Model\ShippingExportTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipment")
 */
class Shipment extends BaseShipment
{
    use ShippingExportTrait;
}