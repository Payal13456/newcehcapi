<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseSummary extends Model
{
    use HasFactory;

    protected $table = "case_summaries";
    protected $fillable=[
        'case_summary',
        'appointment_id',
    ];
}
