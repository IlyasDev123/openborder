<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionnaireState extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'last_question',
        'prev_selections',
        'current_summary',
        'questions_order'
    ];

    
    /**
     * user
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
