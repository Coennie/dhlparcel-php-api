<?php

namespace Mvdnbrk\DhlParcel\Endpoints;

use Mvdnbrk\DhlParcel\Resources\TrackTrace as TrackTraceResource;

class TrackTrace extends BaseEndpoint
{
    public function get($value): TrackTraceResource
    {
        $value = is_array($value) ? implode(",", $value) : (string)$value;
        $response = $this->performApiCall(
            'GET',
            'track-trace?key='.$value
        );

        return new TrackTraceResource(
            collect(collect($response)->first())->all()
        );
    }
}
