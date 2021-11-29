<?php

namespace Mvdnbrk\DhlParcel\Endpoints;

use Mvdnbrk\DhlParcel\Resources\TrackTrace as TrackTraceResource;

class TrackTrace extends BaseEndpoint
{
    /**
     * @param string $value
     *
     * @return TrackTraceResource[]
     */
    public function get(string $value): array
    {
        $response = $this->performApiCall(
            'GET',
            'track-trace'.$this->buildQueryString(['key' => $value])
        );

        foreach(collect($response)->all() as $response) {
            $r[] = new TrackTraceResource(
                collect($response)->all()
            );
        }

        return $r ?? [];
    }
}
