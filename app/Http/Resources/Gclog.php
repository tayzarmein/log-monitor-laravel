<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Gclog extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'datetime' => $this->datetime,
            'logtype' => $this->logtype,
            'oldGenTotal' => $this->heap_maximum,
            'oldGen' => $this->oldgen_current,
        ];
    }
}
