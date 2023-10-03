<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportBug extends Model
{
    use HasFactory;

    protected $fillable =['node_url','current_node', 'description', 'user_id', 'status'];

    protected $casts =[
        'created_at'=> 'datetime:F-d-Y g:i a',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
