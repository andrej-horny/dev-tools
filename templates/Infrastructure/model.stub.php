<?php

namespace {{ namespace }};

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use {{ ulidCastClassPath }};
use {{ ulidTraitClassPath }};

class Eloquent{{ entityName }} extends Model
{
    use SoftDeletes;
    use HasBinaryUlid;

    protected $casts = [
        'id' => UlidBinary::class,
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        // 'uri',
        // 'title',       
    ];

  
}