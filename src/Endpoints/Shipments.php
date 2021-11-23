<?php

namespace Mvdnbrk\DhlParcel\Endpoints;

use Mvdnbrk\DhlParcel\Contracts\ShouldAuthenticate;
use Mvdnbrk\DhlParcel\Resources\Parcel;
use Mvdnbrk\DhlParcel\Resources\Shipment as ShipmentResource;
use Mvdnbrk\DhlParcel\Resources\ShipmentPiece;
use Ramsey\Uuid\Uuid;

class Shipments extends BaseEndpoint implements ShouldAuthenticate
{
    public function create(Parcel $parcel): ShipmentResource
    {
        $response = $this->performApiCall(
            'POST',
            'shipments',
            $this->getHttpBody($parcel)
        );

        $shipment = new ShipmentResource([
            'id' => $response->shipmentId,
            'barcode' => $response->pieces[0]->trackerCode,
            'label_id' => $response->pieces[0]->labelId,
        ]);

        $shipment = $this->getPieces($response->pieces, $shipment);
        if (isset($response->returnShipment)) {
            $shipment = $this->getPieces($response->returnShipment->pieces, $shipment);
        }

        return $shipment;
    }

    protected function getHttpBody(Parcel $parcel): string
    {
        return json_encode(array_merge([
            'shipmentId' => Uuid::uuid4()->toString(),
            'accountId' => $this->apiClient->authentication->getAccessToken()->getAccountId(),
        ], $parcel->toArray()));
    }

    private function getPieces(array $pieces, ShipmentResource $shipment): ShipmentResource
    {
        collect($pieces)->each(function ($item) use ($shipment) {
            $shipment->pieces->add(new ShipmentPiece([
                'label_id' => $item->labelId,
                'label_type' => $item->labelType,
                'parcel_type' => $item->parcelType,
                'piece_number' => $item->pieceNumber,
                'tracker_code' => $item->trackerCode,
            ]));
        });

        return $shipment;
    }
}
