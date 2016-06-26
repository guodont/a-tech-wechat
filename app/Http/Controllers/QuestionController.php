<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Http\Requests;

class QuestionController extends Controller
{
    //
    public function questions()
    {
        $client = new Client();
        $response = $client->get('http://sxnk110.workerhub.cn:9000/api/v1/questions');
        $questions = json_decode($response->getBody());
        return view('questions', ['questions' => $questions]);
    }

    public function questions2()
    {
        return view('questions2');
    }

    public function question($questionId)
    {
        $client = new Client();
        $response = $client->get('http://sxnk110.workerhub.cn:9000/api/v1/question/'.$questionId);
        $question = json_decode($response->getBody());
        return view('question', compact('question'));
    }
}
