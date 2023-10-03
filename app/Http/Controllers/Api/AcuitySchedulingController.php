<?php

namespace App\Http\Controllers\Api;


use DateTimeZone;
use Carbon\Carbon;
use AcuityScheduling;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
use App\Http\Controllers\Controller;
use App\Models\TimeZone;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AcuitySchedulingController extends Controller
{

    public function getTimeZone()
    {

        $timezone = TimeZone::get();

        return $timezone->map(function($zone){
            return [
                "time_zone" => $zone->time_zone_name,
                "time_zone_value" => $zone->time_zone,
            ];
        });

        // $zones_array = array();
        // $timestamp = time();
        // foreach(timezone_identifiers_list() as $key => $zone) {
        //   date_default_timezone_set($zone);

        //  $zoneValue = explode("/", $zone);
        //  if($zoneValue[0] != 'UTC'){
        //     $zones_array[$key]['time_zone'] = '('.'GMT ' . date('P', $timestamp).')'.' '. $zoneValue[1];
        //     $zones_array[$key]['time_zone_value'] = $zone;
        //  }
        // }
        // return $zones_array;
    }

    public static  function configAcuityScheduling()
    {
        // require_once('vendor/autoload.php');
        $acuity = new AcuityScheduling(array(
            'userId' => 16375787,
            'apiKey' => '52b059c8cdf72bce88be9994a10f79dc,wwww'
        ));

        return $acuity;
    }

    /**
     * getAcuityDate
     *
     * @param  mixed $request
     * @return void
     */
    public function getAcuityDate(Request $request)
    {
        try {
            $validate = $request->validate([
                "appointmentTypeID" => "required",
                "month" => "required|date",
                "timezone" => "required",
                "calendarID" => "required",
            ]);

            if (!$validate) {
                return sendError($validate->getMessage(), null);
            }

            $appointmentTypeID = $request->appointmentTypeID;
            $month = $request->month;
            $calendarID = $request->calendarID;
            $timezone = $request->timezone;
            $url = "availability/dates?appointmentTypeID={$appointmentTypeID}&month={$month}&calendarID={$calendarID}&timezone={$timezone}";
            $response = $this->curlGETRequest($url);

            // return $response;
            // $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$month}&calendarID={$calendarID}&timezone={$timezone}");
            return sendSuccess('success', $response);
        } catch (\Throwable $th) {
            return sendError($th->getMessage(), null);
        }
    }

    /**
     * getAcuityTime
     *
     * @param  mixed $request
     * @return void
     */
    public function getAcuityTime(Request $request)
    {
        try {
            $validate = $request->validate([
                "appointmentTypeID" => "required|integer",
                "date" => "required|date",
                "calendarID" => "required |integer",
            ]);

            if (!$validate) {
                return sendError($validate->getMessage(), null);
            }
            $appointmentTypeID = $request->appointmentTypeID;
            $date = $request->date;
            $calendarID = $request->calendarID;
            $url = "availability/times?appointmentTypeID={$appointmentTypeID}&date={$date}&calendarID={$calendarID}&timezone={$request->timezone}";
            $response = $this->curlGETRequest($url);
            // $checkTimes = $this->configAcuityScheduling()->request("/availability/times?appointmentTypeID={$appointmentTypeID}&date={$date}&calendarID={$calendarID}");
            return sendSuccess('success', $response);
        } catch (\Throwable $th) {
            return sendError($th->getMessage(), null);
        }
    }

    //   $checkTimes = $this->configAcuityScheduling()->request('/availability/check-times', array(
    //     'method' => 'POST',
    //     'data' => array(
    //         'datetime' => $date,
    //         'appointmentTypeID' => $appointmentTypeID,
    //         'calendarID' => $calendarID
    //     )
    // ));
    // dd($checkTimes);

    /**
     * getAcuityAppointmentType
     *
     * @return void
     */
    public function getAcuityAppointmentType()
    {
        try {
            $url = "appointment-types";
            $checkDates = $this->curlGETRequest($url);

            // $checkDates = $this->configAcuityScheduling()->request("/appointment-types");
            return sendSuccess('success', $checkDates);
        } catch (\Throwable $th) {
            return sendError($th->getMessage(), null);
        }
    }

    /**
     * getAcuityCalenders
     *
     * @return void
     */
    public function getAcuityCalenders()
    {
        try {
            $url = "calendars";
            $response = $this->curlGETRequest($url);
            // $response = $this->configAcuityScheduling()->request("/calendars");
            return sendSuccess('success', $response);
        } catch (\Throwable $th) {
            return sendError($th->getMessage(), null);
        }
    }

    public function curlGETRequest($url)
    {

        // $userID = 16375787;
        // $key = '52b059c8cdf72bce88be9994a10f79dc(234444sdfdfd)';

        $url = "https://acuityscheduling.com/api/v1/{$url}";
        // Initiate curl:
        // GET request, so no need to worry about setting post vars:
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Grab response as string:
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // HTTP auth:
        curl_setopt($ch, CURLOPT_USERPWD, "$userID:$key");

        // Execute request:
        $result = curl_exec($ch);

        // Don't forget to close the connection!
        curl_close($ch);

        $data = json_decode($result, true);
        return $data;
    }


    /**
     * getAcuityDate
     *
     * @param  mixed $request
     * @return void
     */
    // public function getAcuityDateTime(Request $request)
    // {
    //     try {
    //         $validate = $request->validate([
    //             "appointmentTypeID" => "required",
    //             "month" => "required|date",
    //             "timezone" => "required",
    //             "calendarID" => "required",
    //         ]);

    //         if (!$validate) {
    //             return sendError($validate->getMessage(), null);
    //         }
    //         if(isset($request->is_previous) && $request->is_previous == 1) {
    //            $timeSlotAcuity = $this->getAcuityPreviousDateTime($request);
    //             return sendSuccess('success', $timeSlotAcuity);
    //         }
    //         $date = $request->month;
    //         $date_array = array();
    //         $i = 0;
    //         while ($i < 5) {
    //             $today = Carbon::parse($date);
    //             array_push($date_array, $today->addDays($i)->format('Y-m-d'));
    //             $i++;
    //         }
    //         $appointmentTypeID = $request->appointmentTypeID;
    //         $calendarID = $request->calendarID;
    //         $timezone = $request->timezone;
    //         $timeSlotAcuity = [];

    //         foreach ($date_array as $key => &$date) {
    //             $slot = $this->configAcuityScheduling()->request("/availability/times?appointmentTypeID={$appointmentTypeID}&date={$date}&calendarID={$calendarID}&timezone={$timezone}");
    //             if ($slot) {
    //                 array_push($timeSlotAcuity, $slot);
    //             } else {
    //                 array_push($date_array, $today->addDays(1)->format('Y-m-d'));
    //             }
    //         }

    //         return sendSuccess('success', $timeSlotAcuity);
    //     } catch (\Throwable $th) {
    //         return sendError($th->getMessage(), null);
    //     }
    // }


    // public function getAcuityPreviousDateTime($request)
    // {
    //     $date = $request->month;
    //     $date_array = array();
    //     $i = 1;
    //     while ($i < 6) {
    //         $today = Carbon::parse($date);
    //         array_push($date_array, $today->subDays($i)->format('Y-m-d'));
    //         $i++;
    //     }
    //     $appointmentTypeID = $request->appointmentTypeID;
    //     $calendarID = $request->calendarID;
    //     $timezone = $request->timezone;
    //     $timeSlotAcuity = [];

    //     foreach ($date_array as $key => &$date) {
    //         $slot = $this->configAcuityScheduling()->request("/availability/times?appointmentTypeID={$appointmentTypeID}&date={$date}&calendarID={$calendarID}&timezone={$timezone}");
    //         if ($slot) {
    //             array_push($timeSlotAcuity, $slot);
    //         } else {
    //             $currentDate = now()->subDay(1)->format('Y-m-d');
    //             if ($currentDate == $date) {
    //                 break;
    //             }
    //             array_push($date_array, $today->subDays(1)->format('Y-m-d'));
    //         }
    //     }

    //     return $timeSlotAcuity;
    // }

    public function acuityForm()
    {
        $getForm = $this->configAcuityScheduling()->request("/forms");

        return sendSuccess('success', $getForm);
    }


    public function getAcuityDateTime(Request $request)
    {
        try {
            $validate = $request->validate([
                "appointmentTypeID" => "required",
                "month" => "required|date",
                "timezone" => "required",
                "calendarID" => "required",
            ]);

            if (!$validate) {
                return sendError($validate->getMessage(), null);
            }
            if (isset($request->is_previous) && $request->is_previous == 1) {
                $timeSlotAcuity = $this->getAcuityPreviousDateTime($request);
                return sendSuccess('success', $timeSlotAcuity);
            }
            $date = $request->month;
            $appointmentTypeID = $request->appointmentTypeID;
            $calendarID = $request->calendarID;
            $timezone = $request->timezone;
            $checkDates = [];
            $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$date}&calendarID={$calendarID}&timezone={$timezone}");

            
            if(sizeof($checkDates) == 0) {
                $oldDate = $date;

                $date = Carbon::parse($date)->addDays(1)->format('Y-m-d');

                while(true) {

                    if (Carbon::parse($date)->format('m') == Carbon::parse($oldDate)->format('m')) {
                        $date = Carbon::parse($date)->addDays(1)->format('Y-m-d');
                    }
                    else{
                        break;
                    }
                }
                $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$date}&calendarID={$calendarID}&timezone={$timezone}");

            }
            $dateArray = array();
            $last = end($checkDates);
            // return $last["date"];
            $count = 0;

            foreach ($checkDates as $key => $d) {
                if ($d["date"] >= $date) {
                    // if ($d["date"] != $date) {
                        array_push($dateArray, $d["date"]);
                    // }

                    if ($count == 4) {
                        break;
                    } else {
                        if ($last["date"] == $d["date"]) {
                            $date = Carbon::parse($last["date"])->addMonth(1)->format('Y-m-d');

                            $count = 0;
                            while(true) {
  
                                if (Carbon::parse($date)->format('m') - 1 == Carbon::parse($last['date'])->format('m')) {
                                    break;
                                }
                                else{
                                    $date = Carbon::parse($date)->subDays(1)->format('Y-m-d');
                                    $count++;
                                }
                            }

                            $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$date}&calendarID={$calendarID}&timezone={$timezone}");                            
                            foreach ($checkDates as $key => $dates) {
                                array_push($dateArray, $dates["date"]);
                                $count++;
                                if ($count == 4) {
                                    break;
                                }
                            }
                        }
                        $count++;
                    }
                }
            }

            $responses = $this->asyncRequest($dateArray, $appointmentTypeID, $calendarID, $timezone);

            return sendSuccess('success', $responses);
        } catch (\Throwable $th) {
            return sendError($th->getMessage(), null);
        }
    }

    public function getAcuityPreviousDateTime($request)
    {
        $date = $request->month;
        $date = $request->month;
        $appointmentTypeID = $request->appointmentTypeID;
        $calendarID = $request->calendarID;
        $timezone = $request->timezone;
        $checkDates = [];
        $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$date}&calendarID={$calendarID}&timezone={$timezone}");
        krsort($checkDates);
        $dateArray = array();
        $first = $checkDates[0];
        $count = 0;
        foreach ($checkDates as $index => $d) {
            if ($d["date"] <= $date) {
                if ($d["date"] != $date) {
                    array_push($dateArray, $d["date"]);
                }
                if ($count == 5) {
                    break;
                } else {
                    if ($first["date"] == $d["date"]) {
                        $date = Carbon::parse($first["date"])->subMonth(1)->format('Y-m-d');
                        $checkDates = $this->configAcuityScheduling()->request("/availability/dates?appointmentTypeID={$appointmentTypeID}&month={$date}&calendarID={$calendarID}&timezone={$timezone}");
                        krsort($checkDates);
                        foreach ($checkDates as $key => $dates) {
                            array_push($dateArray, $dates["date"]);
                            $count++;
                            if ($count == 5) {
                                break;
                            }
                        }
                    }
                    $currentDate = now()->subDay(1)->format('Y-m-d');
                    if ($currentDate == $date) {
                        break;
                    }
                    $count++;
                }
            }
        }
        return $this->asyncRequest($dateArray, $appointmentTypeID, $calendarID, $timezone);
    }

    public function asyncRequest($dateArray, $appointmentTypeID, $calendarID, $timezone)
    {
        $userID = "16375787";
        $key = '52b059c8cdf72bce88be9994a10f79dc';
        $url = "https://acuityscheduling.com/api/v1/";
        $timeSlotAcuity = [];
        $dateArray[0] = $dateArray[0] ?? now()->subDay(1);
        $dateArray[1] = $dateArray[1] ?? now()->subDay(1);
        $dateArray[2] = $dateArray[2] ?? now()->subDay(1);
        $dateArray[3] = $dateArray[3] ?? now()->subDay(1);
        $dateArray[4] = $dateArray[4] ?? now()->subDay(1);

        $responses = Http::pool(fn (Pool $pool) => [
            $pool->withBasicAuth($userID, $key)->acceptJson()->get($url . "availability/times?appointmentTypeID={$appointmentTypeID}&date={$dateArray[0]}&calendarID={$calendarID}&timezone={$timezone}")->then(
                function (Response $result) {
                    return array_push($timeSlotAcuity, $result->object());
                }
            ),
            $pool->withBasicAuth($userID, $key)->get($url . "availability/times?appointmentTypeID={$appointmentTypeID}&date={$dateArray[1]}&calendarID={$calendarID}&timezone={$timezone}")
                ->then(function (Response $result) {
                    return array_push($timeSlotAcuity, $result->object());
                }),
            $pool->withBasicAuth($userID, $key)->get($url . "availability/times?appointmentTypeID={$appointmentTypeID}&date={$dateArray[2]}&calendarID={$calendarID}&timezone={$timezone}")
                ->then(function (Response $result) {
                    return array_push($timeSlotAcuity, $result->object());
                }),
            $pool->withBasicAuth($userID, $key)->get($url . "availability/times?appointmentTypeID={$appointmentTypeID}&date={$dateArray[3]}&calendarID={$calendarID}&timezone={$timezone}")
                ->then(function (Response $q) {
                    array_push($timeSlotAcuity, $q);
                }),
            $pool->withBasicAuth($userID, $key)->get($url . "availability/times?appointmentTypeID={$appointmentTypeID}&date={$dateArray[4]}&calendarID={$calendarID}&timezone={$timezone}")
                ->then(function (Response $q) {
                    array_push($timeSlotAcuity, $q);
                }),
        ]);

        foreach ($responses as $key => $res) {
            if ($responses[$key]->object() != null) {
                array_push($timeSlotAcuity, $responses[$key]->object());
            } else {
                break;
            }
        }
        // array_push($timeSlotAcuity, $responses[0]->object(), $responses[1]->object(), $responses[2]->object(), $responses[3]->object(), $responses[4]->object());

        return $timeSlotAcuity;
    }

    public function getAppointmentById($id)
    {
        $data = $this->configAcuityScheduling()->request("/appointments/{$id}");
        preg_match_all('/(https?|ssh|ftp):\/\/[^\s"]+/', $data['formsText'], $url);
        return $url[0];
    }

    public function getAppointmentAcuity()
    {
        $data = $this->configAcuityScheduling()->request("/appointments");
        return $data;
    }
}
