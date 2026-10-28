<?php

namespace App\Models;

use App\Models\Scopes\isActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description'
    ]; // membuat agar bisa di ubah atau di isi langsung, selain ini gak bisa di ubah atau di tambah secara langsung secara massal

    #[Override]
    protected static function booted():void
    {
        parent::booted();
        self::addGlobalScope(new isActiveScope());
    }
}
