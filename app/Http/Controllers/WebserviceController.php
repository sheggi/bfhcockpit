<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;

class WebserviceController extends Controller
{
    //

    public function fetch(){
        $url = 'https://mybfh.bfh.ch/webservice/?action=get_tt_entries&flat&_dc=' . time();
        $fetched = json_decode(file_get_contents($url), true);

        if ($fetched && $fetched['message'] === 'No problem') {
            $lessons = $fetched['data'];
            foreach ($lessons as $lessonData) {
                $lessonData['id'] = $lessonData['uid'];
                $lessonData['name'] = $lessonData['name_de'];
                unset ($lessonData['uid']);
                unset ($lessonData['name_de']);
                $lesson = Lesson::firstOrNew(['id' => $lessonData['id']], $lessonData);
                $lesson->save();
            }

        } else {
            header('HTTP/1.0 500 Internal Server Error');
            exit('There went something wrong.');
        }

        return response()->json(Lesson::all());
    }
}