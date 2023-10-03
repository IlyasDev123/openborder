<?php


namespace App\CommanFunctions;


class Constants
{


    /***************Roles ******************/
    const ADMIN_ROLE_ID = 1;
    const RECEPTIONISTS_ROLE_ID = 2;
    const DOCTOR_ROLE_ID = 3;
    const PATIENT_ROLE_ID = 4;
    /***************Clinic id******************/

    const CLINIC_ID = 1;

    /*************** User status ******************/
    const ACTIVE_USER = 0;
    const BLOCKED_USER = 1;

    /***************consultation status******************/
    const APPOINTMENT_REJECT = 0;
    const APPOINTMENT_PENDING = 1;
    const APPOINTMENT_ACCEPT = 2;

    /***************treatment status******************/
    const TREATMENT_PENDING = 0;
    const TREATMENT_START = 1;

    /***************physical status******************/
    const PHYSICAL_CHECKUP = 'Physical';
    const ONLINE_CHECKUP = 'Online';


    /*************** User status ******************/
    /*************** User status ******************/
    const STATUS = 1;
    const UNBLOCKED_USER = 0;


    /***************Language******************/
    const ENGLISH = 1;
    const SPANISH = 2;

    // const MEDICATION_A = 'medication_a' ;
    // const MEDICATION_B = 'medication_b' ;

    // const DISPENSE_UNIT_A = "dispense_unit_a" ;
    // const DISPENSE_UNIT_b = "dispense_unit_b" ;

    /***************Pharmacy******************/



    /***************** Chat Type *************************/
    const CHAT_TYPE_SINGLE = 'single';
    const CHAT_TYPE_GROUP = 'group';

    /***************** Default Image *************************/
    const DEFAULT_IMAGE = 'profile_images/default.png';
    const DEFAULT_IMAGE_PROFILE = 'admin-profile/';
    /***************** User Notifications *************************/

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_DURATION = 60;
    const EMAIL_SUBJECT = "🔐 Your login code from Open Borders";
    const BUG_REPORT_EMAIL_SUBJECT = "🐜 Bug Reported";

    // const CONSULTATION = "consultation" ;
    // const NOTIFICATION_TYPE_TREATMENT = "treatment" ;
    // const NOTIFICATION_TYPE_MESSAGE = "message" ;

    /***************** Payment Methods *************************/
    // Const PAYPAL = "paypal" ;
    // Const STRIPE = "stripe" ;
    // const PRICE = 30;

}
