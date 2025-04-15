<?php

declare(strict_types=1);

namespace Ikuzo\SyliusColishipPlugin\Api;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Riverline\MultiPartParser\StreamedPart;

class ShippingLabelFetcher implements ShippingLabelFetcherInterface
{
    private RequestStack $requestStack;
    private WebClientInterface $webClient;
    private SoapClientInterface $soapClient;

    public function __construct(
        RequestStack $requestStack,
        WebClientInterface $webClient,
        SoapClientInterface $soapClient
    ) {
        $this->requestStack = $requestStack;
        $this->webClient = $webClient;
        $this->soapClient = $soapClient;
    }

    public function createShipment($shippingGateway, $shipment, float $weight): void
    {
        try {
            $this->webClient->setShippingGateway($shippingGateway);
            $this->webClient->setShipment($shipment);

            $requestData = $this->webClient->getRequestData($weight);

            $this->response = $this->soapClient->createShipment($requestData, $weight);

            if ($this->response->return->messages->type == "ERROR") {
                throw new \Exception($this->response->return->messages->messageContent, 1);
            }
        } catch (\SoapFault $exception) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request !== null) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    sprintf(
                        'Colissimo Service for #%s order: %s',
                        $shipment->getOrder()->getNumber(),
                        $exception->getMessage()
                    )
                );
            }
        }
    }

    public function getLabelContent(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            if (!isset($this->response->return->labelV2Response->label)) {
                $request->getSession()->getFlashBag()->add('error', $this->response->return->messages->messageContent);
                return null;
            }

            $request->getSession()->getFlashBag()->add('success', 'bitbag.ui.shipment_data_has_been_exported');
        }

        return [
            'parcelNumber' => $this->response->return->labelV2Response->parcelNumber,
            'label' => $this->response->return->labelV2Response->label,
            'cn23' => (isset($this->response->return->labelV2Response->cn23)) ? $this->response->return->labelV2Response->cn23 : null
        ];
    }
}
