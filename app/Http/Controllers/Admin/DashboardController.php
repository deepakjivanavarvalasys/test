<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    private $data;

    public function __construct()
    {
        $this->data = array();
    }

    public function index()
    {
        return view('admin.dashboard', $this->data);
    }

    public function test()
    {
        //$response = Http::get('http://localhost/api_portal/api/get-token');
        //$token = $response->json()['data'];

        $details = array(
            'campaign_name' => 'This is campaign name',
            'download_link' => 'public/storage/campaigns/'
        );

        $email_data = array(
            'to' => 'sagar@valasys.com',
            'subject' => 'VPM Test mail',
            'body' => $html,
        );

        //$response = Http::post('http://localhost/api_portal/api/email/send', $email_data);
        $api_response = Http::post('https://api.valasysb2bmarketing.com/api/email/send', $email_data);

        dd($api_response->status(), $api_response->json(), $api_response->body(), $api_response);

        $response = Http::withHeaders([
            'Authorization' => 'token'
        ])->post('http://localhost/api_portal/api/email/send', [
            '_token' => $token,
        ]);

        dd($response->status(), $response->json(), $response->body(), $response);



        dd($response->status(), $response->json(), $response->body());

        /*$response->body() : string;
        $response->json() : array|mixed;
        $response->object() : object;
        $response->collect() : Illuminate\Support\Collection;
        $response->status() : int;
        $response->ok() : bool;
        $response->successful() : bool;
        $response->failed() : bool;
        $response->serverError() : bool;
        $response->clientError() : bool;
        $response->header($header) : string;
        $response->headers() : array;*/
    }
}
