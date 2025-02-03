<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ResetDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = now()->format('Y-m-d');

        $products = Product::whereNotNull('discount_end')->get();

        foreach($products as $product){

            if($product->discount_end < $currentDate){
                $product->update(['discount_end'=>null,"discount_start"=>null,"discount"=>0]);
                $this->info("Discount for product ID {$product->id} has been reset to zero.");
            }

        }

        $this->info('Discount reset process completed.');

    }
}
