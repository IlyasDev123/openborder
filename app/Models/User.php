<?php

namespace App\Models;

use App\Models\Address;
use App\Models\UserMeta;
use App\Models\Petitioner;
use Laravel\Cashier\Billable;
use App\Models\QuestionnaireState;
use Laravel\Passport\HasApiTokens;
use App\Models\ConsultationBooking;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'profile_image',
        'email',
        'password',
        'phone_no',
        'status',
        'strip_id',
        'apple_pay_token',
        'google_pay_token',
        'stripe_token',
        'pm_type',
        'pm_last_four',
        'blance_amount',
        'stripe_payment_method',
        'is_complete_profile',
        'card_token',
        'is_guest',
        'language',
        'card_holder_name',
        'expire_date',
    ];
    protected $with = ['user_address'];
    protected $appends = ['country_code'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'stripe_payment_method',
        // 'card_token',
        'apple_pay_token',
        'google_pay_token',
        'stripe_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * address
     *
     * @return void
     */
    public function user_address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * address
     *
     * @return void
     */
    public function userMeta()
    {
        return $this->hasOne(UserMeta::class);
    }

    /**
     * address
     *
     * @return void
     */
    public function petitionerDetail()
    {
        return $this->hasOne(Petitioner::class);
    }

    /**
     * questionnaireStates
     *
     * @return void
     */
    public function questionnaireStates()
    {
        return $this->hasMany(QuestionnaireState::class);
    }

    /**
     * usersBookedConsultation
     *
     * @return void
     */
    public function usersBookedConsultation()
    {
        return $this->hasMany(ConsultationBooking::class);
    }

    /**
     * userBySubscriptions
     *
     * @return void
     */
    public function userBySubscriptions()
    {
        return $this->hasMany(UserSubscriptionsDetail::class, 'user_id');
    }

    /**
     * questionnaireStates summary
     *
     * @return void
     */
    public function questionnaireStatesSummary()
    {
        return $this->hasOne(QuestionnaireState::class);
    }


    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function getCountryCodeAttribute($value)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $this->phone_no);

        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+' . $countryCode;
        } else if (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree . '-' . $lastFour;
        }
        return $phoneNumber;
    }
}