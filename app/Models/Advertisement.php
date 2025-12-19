<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    // KHAI BÁO CÁC CỘT ĐƯỢC PHÉP LƯU (FILLABLE)
    protected $fillable = [
        'title',
        'image_path',
        'display_location',
        'start_date',
        'end_date',
        'status',
    ];
}