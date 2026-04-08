<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;



#[Table('modalidades')]
#[Fillable(['name'])]
class Modalidad extends Model
{
    use SoftDeletes;
}
