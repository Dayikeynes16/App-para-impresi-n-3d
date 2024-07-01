<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Files extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'path', 'minutos', 'precio','cantidad','morphable_id', 'morphable_type'];

    protected $casts = ['precio'=>'float'];

    public function morphable()
    {
        return $this->morphTo();
    }

}
