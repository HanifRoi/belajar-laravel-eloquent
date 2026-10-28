<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    public function testCreateVoucher()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code="62873364124";
        $voucher->save();

        self::assertNotNull("id");
    }

    public function testCreateVoucherUUID()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->save();

        self::assertNotNull("id");
        self::assertNotNull("voucher_code");
    }

    public function testSoftDeletes()
    {
        $this->seed(VoucherSeeder::class);

        $voucher =Voucher::where('name', '=', 'Sample Voucher')->first();
        $voucher->delete();

        $voucher=Voucher::where('name', '=', 'Sample Voucher')->first();
        self::assertNull($voucher);

        $voucher=Voucher::withTrashed()->where('name', '=', 'Sample Voucher')->first();
        self::assertNotNull($voucher);
    }

    

    
}
