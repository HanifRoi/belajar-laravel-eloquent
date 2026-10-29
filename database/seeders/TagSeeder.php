<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tag = new Tag();
        $tag->id='hr';
        $tag->name='Hanif Roihan';
        $tag->save();

        $product = Product::first();
        $product->tags()->attach($tag);

        $voucher = Voucher::first();
        $voucher->tags()->attach($tag);
    }
}
