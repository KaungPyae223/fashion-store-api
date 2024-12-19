<?php

namespace App\Repositories\Contract;

use Illuminate\Support\Facades\Storage;

class BasicFunctions {

    public function deletePhoto($image){

        $imagePath = str_replace(asset('storage'), '', $image);

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

    }

}
