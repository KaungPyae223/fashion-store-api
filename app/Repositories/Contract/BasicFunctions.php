<?php

namespace App\Repositories\Contract;

use App\Models\AdminMonitoring;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BasicFunctions {

    
    public function deletePhoto($image){

        $imagePath = str_replace(asset('storage'), '', $image);

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

    }

    public function storePhoto($image,$directory){

        $imageName = $directory . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs("images/".$directory, $imageName,"public");
        return  asset('storage/' . $imagePath);

    }

    public function compareDiff ($column,$originalData,$updateData){
        if($originalData != $updateData){
            return $column." ".$originalData." to ".$updateData." ";
        }
        return;
    }

    public function addAdminActivity (array $data){
        $activity = AdminMonitoring::class;

        $activity::create([
            "admin_id" => $data["admin_id"],
            "method" => $data["method"],
            "type" => $data["type"],
            "action" => $data["action"],
        ]);

    }

}
