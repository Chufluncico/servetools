<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;



#[Table('modalidades')]
#[Fillable([
        'aet',
        'department',
        'centre',
        'location',
        'ip',
        'syngo',
        'observations',
        'model',
        'modalidad',
        'machine',
        'station',
        'request_date',
        'extra_data',
    ])]
class Modalidad extends Model
{
    use SoftDeletes;


    protected function casts(): array
    {
        return [
            'syngo'       => 'boolean',
            'request_date'=> 'date',
            'extra_data'  => 'array',
        ];
    }
}



            