<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'function_name',
        'execution_time',
    ];
}


