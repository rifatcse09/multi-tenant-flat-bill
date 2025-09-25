<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flat;
use App\Models\BillCategory;
use App\Models\Bill;
use Carbon\Carbon;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $month = Carbon::now()->startOfMonth()->format('Y-m-01');

        foreach (Flat::all() as $flat) {
            $categories = BillCategory::where('owner_id',$flat->owner_id)->get();
            foreach ($categories as $cat) {
                Bill::firstOrCreate([
                    'owner_id'=>$flat->owner_id,
                    'flat_id'=>$flat->id,
                    'bill_category_id'=>$cat->id,
                    'month'=>$month,
                ],[
                    'amount'=>rand(500,1500),
                    'status'=>'unpaid',
                    'due_carry_forward'=>0,
                ]);
            }
        }
    }
}