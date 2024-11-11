<?php

namespace App\Observers;

use App\Models\Travel;

class TravelObserver
{

    public function creating(Travel $travel)
    {
        $travel->slug = str($travel->name)->slug();
    }


}
