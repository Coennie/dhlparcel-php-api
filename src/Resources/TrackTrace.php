<?php

namespace Mvdnbrk\DhlParcel\Resources;

class TrackTrace extends BaseResource
{
    /** @var string */
    public $barcode;

    /** @var bool */
    public $isDelivered;

    /** @var string */
    public $status;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->isDelivered = collect($attributes)->has('deliveredAt');

        $phases = ['DATA_RECEIVED', 'UNDERWAY', 'IN_DELIVERY', 'DELIVERED'];
        foreach ($phases as $phase) {
            $key = array_search($phase, array_column($attributes['view']->phaseDisplay, 'phase'));
            if ($key !== false && $attributes['view']->phaseDisplay[$key]->completed === true) {
                $this->status = $phase;
            }
        }
    }
}
