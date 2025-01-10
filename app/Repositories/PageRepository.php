<?php

namespace App\Repositories;

use App\Models\page;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageRepository extends BasicFunctions{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = page::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function updateADS(string $data){

        $page = $this->model::find(1);

        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Page",
                "action" => "Update a page ads ".
                $this->compareDiff("ads",$page->ads,$data)
            ]);

            $page->update([
                "ads" => $data
            ]);

            DB::commit();
            return $page;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

}
