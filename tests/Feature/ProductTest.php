<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class ProductTest extends TestCase
{
    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class,ProductSeeder::class]);
        $product = Product::find('1');
        self::assertNotNull($product);

        $category = $product->category;
        self::assertNotNull($category);

        self::assertEquals('FOOD', $category->id);

    }

    public function testHasOneOfMany()
    {
        $this->seed([CategorySeeder::class,ProductSeeder::class]);

        $category = Category::find('FOOD');
        self::assertNotNull($category);

        $cheapestProduct = $category->cheapestProduct;
        self::assertNotNull($cheapestProduct);
        self::assertEquals(1, $cheapestProduct->id);

        $mostExpensiveProduct = $category->mostExpensiveProduct;
        self::assertNotNull($mostExpensiveProduct);
        self::assertEquals(2, $mostExpensiveProduct->id);
    }

    public function testOneToOnePolymorphic()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);
        $product = Product::find('1');
        self::assertNotNull($product);

        $image =  $product->image;
        self::assertNotNull($image);

        self::assertEquals('https:://www.programmerzamannow.com/image/2.jpg', $image->url);
    }

    public function testOneToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class,CommentSeeder::class]);

        $product = Product::find('1');
        self::assertNotNull($product);

        $comments = $product->comments;
        foreach ($comments as $com) {
            self::assertEquals('product', $com->commentable_type);
            self::assertEquals($product->id, $com->commentable_id);
        }

    }

    public function testOneOfManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class,CommentSeeder::class]);

        $product = Product::find('1');
        self::assertNotNull($product);

        $comment = $product->latestComment;
        self::assertNotNull($comment);

        $comment = $product->oldestComment;
        self::assertNotNull($comment);
    }

    public function testManyToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, TagSeeder::class]);

        $product = Product::find('1');
        self::assertNotNull($product);

        $tags = $product->tags;
        self::assertNotNull($tags);
        self::assertCount(1, $tags);

        foreach ($tags as $tag) {
            self::assertNotNull($tag->id);
            self::assertNotNull($tag->name);

            $vouchers = $tag->vouchers;
            self::assertCount(1, $vouchers);
            self::assertNotNull($vouchers);
        }
    }

    public function testEloquentCollection()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $products = Product::query()->get();//hasilnya adalah 2 products, 1,2 idnya
        $query = $products->toQuery()->where('price', '200')->get();//where id IN(1,2)

        assertNotNull($products);
        assertEquals('2', $query[0]->id);

    }

    public function testSerialization()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::get();
        assertCount(2, $product);

        $json = $product->toJson(JSON_PRETTY_PRINT);
        Log::info($json);

    }

    public function testSerializationRelation()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);

        $product = Product::get();
        $product->load(['category', 'image']);
        assertCount(2, $product);

        $json = $product->toJson(JSON_PRETTY_PRINT);
        Log::info($json);
    }


}
