<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VirtualAccountSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function testOneToOne()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class]);
        $customer = Customer::find('HANIF');
        self::assertNotNull($customer);

        $wallet = $customer->wallet;
        self::assertNotNull($wallet);

        self::assertEquals(10000000, $wallet->amount);
    }

    public function testOneToOneQuery()
    {
        $customer = new Customer();
        $customer->id="HANIF";
        $customer->name="Hanif";
        $customer->email="hanif@gans";
        $customer->save();

        $wallet = new Wallet();
        $wallet->amount=1000000;

        $customer->wallet()->save($wallet);
        self::assertNotNull($wallet->customer_id);

    }

    public function testHasOneThrough()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class, VirtualAccountSeeder::class]);

        $customer = Customer::find('HANIF');
        self::assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        self::assertNotNull($virtualAccount);
        self::assertEquals('BCA', $virtualAccount->bank);    
        
    }

    public function testManyToManyAttach()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class,ProductSeeder::class]);

        $customer = Customer::find('HANIF');
        self::assertNotNull($customer);

        $customer->likeProducts()->attach('1');// berarti ID HANIF like Product id 1.
        $product = $customer->likeProducts;
        self::assertCount(1, $product);
        self::assertEquals('1', $product[0]->id);
    }

    public function testManyToManyDetach()
    {
        $this->testManyToManyAttach();

        $customer = Customer::find('HANIF');
        $customer->likeProducts()->detach('1');

        $product = $customer->likeProducts;
        self::assertCount(0, $product);
    }

    public function testPivotAttribute()
    {
        $this->testManyToManyAttach();
        $customer = Customer::find('HANIF');
        $product = $customer->likeProducts;

        foreach($product as $prd){
            $pivot = $prd->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
        }
    }

    public function testPivotAttributeCondition()
    {
        $this->testManyToManyAttach();
        $customer = Customer::find('HANIF');
        $product = $customer->likeProductsLastWeek;

        foreach($product as $prd){
            $pivot = $prd->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
        }
    }
}
