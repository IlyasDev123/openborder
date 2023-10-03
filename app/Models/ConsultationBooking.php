<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationBooking extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'consultation_id', 'date', 'consultation_time',
        'consultation_end_time','consultation_with','questionnaire_summery',
        'amount','paid_amount', 'transaction_id','acuity_response','appointment_type','emigration_type'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function consultation(){
        return $this->belongsTo(Consultation::class, 'consultation_id','id');
    }
}
