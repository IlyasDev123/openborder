<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Response;

function sendApiSuccess($message, $data)
{
    return Response::json(['status' => true, 'message' => $message, 'data' => $data], 200);
}

function sendApiError($message, $data)
{
    return Response::json(['status' => false, 'message' => $message, 'data' => $data], 400);
}

function sendSuccess($message, $data)
{
    return array('status' => true, 'message' => $message, 'data' => $data);
}

function sendError($message, $data)
{
    return array('status' => false, 'message' => $message, 'data' => $data);
}


function addFile($file, $path)
{
    if ($file) {
        if ($file->getClientOriginalExtension() != 'exe') {
            $type = $file->getClientMimeType();
            if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp') {
                $destination_path = $path;
                $root = public_path();
                $dir = $root . '/' . $destination_path;
                if (!file_exists($dir)) {
                    mkdir($dir, 0775, true);
                }
                $extension = $file->getClientOriginalExtension();
                $fileName =  Str::random(15) . '.' . $extension;
                $img = Image::make($file);
                if (($img->filesize() / 1000) > 2000) {
                    Image::make($file)->save($destination_path . $fileName, 30);
                    $file_path = $destination_path . $fileName;
                } else {
                    $file->move($dir, $fileName);
                    $file_path = $destination_path . $fileName;
                }
                return $file_path;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}
function getCurrentDate()
{
    return date('Y-m-d', strtotime(now()));
}

function formateDate($d)
{
    return date('Y-m-d', strtotime($d));
}

function formateTime($t)
{
    return date('H:i:s', strtotime($t));
}

function getCurrentTime()
{
    return date('H:i:s', strtotime(now()));
}

function user_img_storage($user_image)
{

    $img_path = '' . asset('assets/images/user-dummy-img.jpg') . '';
    if (isset($user_image) && !empty($user_image) && Storage::disk('local')->exists($user_image))
        $img_path = Storage::url('app/' . $user_image);
    return env('APP_URL') . $img_path;
}

function myEach($arr)
{
    $key = key($arr);
    $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
    next($arr);
    return $result;
}
function questionnaireSummarySerlizeData($qs, $request)
{

    $questionnaire = json_decode($qs);
    $factorsOptions = [];

    foreach ($questionnaire as $question) {

        if (isset($question->id)) {
            $question = json_encode($question);
        }
        $question = json_decode($question, true);
        // $description_eu = $question['description'];
        if ($request->appointmentTypeID != 22885774) {
            $description = strip_tags(html_entity_decode($question['description']));
            $description =  str_replace(array("\n", "\r",), '', $description);
        } else {
            $description = strip_tags(html_entity_decode($question['description_ES']));
            $description = str_replace(array("\n", "\r"), '', $description);
        }
        preg_replace("/\n/", "", $description);
        switch ($question['category']) {
            case ('factorsOptions'):
                array_push($factorsOptions, $description);
                break;
            default:
                break;
        }
    }
    $questionnaires = [];

    // "What kind of immigration help do you need" => $request->description
    array_push($questionnaires, ["factorsOptions" => $factorsOptions]);
    $questionnaires = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($questionnaires, true), ENT_NOQUOTES));
    return str_replace('"', '',str_replace(array('{', '}'),'',$questionnaires));
}

function createDebugLogFile($title='', $path, $data = null)
{
    return Log::build(['driver' => 'single',  'path' => storage_path('logs/' . $path . '.log'),])
        ->debug($title, array($data));
}
