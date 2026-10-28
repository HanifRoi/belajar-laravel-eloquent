<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Voucher extends Model
{
    use HasUuids;

    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;

    #[Override]
    public function uniqueIds(): array
    {
        return [$this->primaryKey, "voucher_code"];
    } //Membuat voucher code menjadi uuid selain primary key
}
