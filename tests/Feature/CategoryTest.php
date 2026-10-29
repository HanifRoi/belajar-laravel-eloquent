<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Scopes\isActiveScope;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class CategoryTest extends TestCase
{
    public function testInsert()
    {
        $category = new Category();
        $category->id = 'GADGET';
        $category->name = 'Gadget';
        $result = $category->save();

        self::assertTrue($result);
    }

    public function testInsertManyCategories()
    {
        $categories = [];
        for ($i=0; $i < 10 ; $i++) { 
            $categories[] = [
                'id'=> "ID $i",
                'name'=>"Name $i"
            ];
        }
    
        // $result = Category::query()->insert($categories);
        $result = Category::insert($categories);
        self::assertTrue($result);

        // $total = Category::query()->count();
        $total = Category::count();
        self::assertEquals(10, $total);

    }

    public function testFind()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::find("FOOD");

        self::assertNotNull($category);
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
    }

    public function testUpdate()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::find("FOOD");

        $category->name = "Food Updated";

        // $result = $category->update(); bisajuga pake update, tapi ujung ujungnya pake save
        $result = $category->save();

        self::assertTrue($result);
    }

    public function testSelect()
    {
        for ($i=0; $i < 5; $i++) { 
            $category = new Category();

            $category->id="ID $i";
            $category->name="Name $i";
            $category->save();
        }

        $categories = Category::whereNull("description");
        self::assertEquals(5, $categories->count());
        $categories->each(function($category){
            self::assertNull($category->description);
        });
    }

    public function testSelectUpdate()
    {
        for ($i=0; $i < 5; $i++) { 
            $category = new Category();

            $category->id="ID $i";
            $category->name="Name $i";
            $category->save();
        }

        $categories = Category::whereNull("description");
        self::assertEquals(5, $categories->count());
        $categories->each(function($category){
            $category->description = "Updated";
            $category->update();
        });
    }
    public function testUpdateMany()
    {
        $categories = [];
        for ($i=0; $i < 10 ; $i++) { 
            $categories[] = [
                'id'=> "ID $i",
                'name'=>"Name $i"
            ];
        }

        $result = Category::insert($categories);
        self::assertTrue($result);

        Category::whereNull("description")->update([
            "description"=>"Update Lagi"
        ]);

        $total = Category::where("description", "=", "Update Lagi");

        self::assertEquals(10, $total->count());
    }

    public function testDelete()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::find("FOOD");
        $result = $category->delete();
        self::assertTrue($result);

        $total = Category::count();
        self::assertEquals(0, $total);
    }

    public function testDeleteMany()
    {
        $categories = [];
        for ($i=0; $i < 10 ; $i++) { 
            $categories[] = [
                'id'=> "ID $i",
                'name'=>"Name $i"
            ];
        }

        $result = Category::insert($categories);
        self::assertTrue($result);

        $total = Category::count();
        self::assertEquals(10, $total);

        Category::whereNull("description")->delete();
        $total = Category::count();
        self::assertEquals(0, $total);


    }

    public function testCreate()
    {
        $request = [
            'id'=>"FOOD",
            'name'=>"Food",
            'description'=>"Description Food"
        ];

        $category = new Category($request);
        $category->save();
        self::assertNotNull($category->id);

    }

    public function testCreateUsingQueryBuilder()
    {
        $request = [
            'id'=>"FOOD",
            'name'=>"Food",
            'description'=>"Description Food"
        ];

        $category = Category::create($request);
        $category->save();
        self::assertNotNull($category->id);

    }

    public function testUpdateMassal()
    {
        $this->seed(CategorySeeder::class);

        $request = [
            'name'=>"Food",
            'description'=>"Description Food Updated"
        ];

        $category = Category::find("FOOD");
        $category->fill($request);
        $category->save();
        self::assertNotNull($category->id);
    }

    public function testGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description="Food Category";
        $category->is_active=false;
        $category->save();

        $category = Category::find("FOOD");
        self::assertNull($category);

        $category = Category::withoutGlobalScopes([isActiveScope::class])->find("FOOD");
        self::assertNotNull($category);//tanpa global scope

    }

    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $category = Category::find('FOOD');
        self::assertNotNull($category);

        $product = $category->products;
        self::assertNotNull(1, $product->count());
        
    }

    public function testOneToManyQuery()
    {
        $category = new Category();

        $category->id="FOOD";
        $category->name="Food";
        $category->description="Food Category";
        $category->is_active=true;
        $category->save();

        $product = new Product();
        $product->id='1';
        $product->name='Product 1';
        $product->description='Description 1';
        $category->products()->save($product);

        self::assertNotNull($product->id);
    }

    public function testRelationshipQuery()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find('FOOD');
        $product = $category->products;
        self::assertCount(2, $product);
        $outOfStockProduct = $category->products()->where('stock', '<=', '0')->get();
        self::assertCount(2, $outOfStockProduct);

    }

    public function testHasManyThrough()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, CustomerSeeder::class, ReviewSeeder::class]);

        $category = Category::find('FOOD');
        self::assertNotNull($category);

        $review=$category->reviews;
        self::assertNotNull($review);
        self::assertCount(2, $review);

    }

    public function testQueryRelations()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find('FOOD');
        $product = $category->products()->where('price','=','200')->get();
        assertCount(1, $product);
        assertEquals("2", $product[0]->id);
    }

    public function testAggregatingRelations()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find('FOOD');

        $product = $category->products()->count();
        assertEquals(2, $product);

        $product = $category->products()->where('price','=','200')->count();
        assertEquals(1, $product);
    }
}
