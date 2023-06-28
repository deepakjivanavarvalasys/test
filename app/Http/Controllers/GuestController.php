<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

use Excel;

class GuestController extends Controller
{
    private $data;

    public function __construct()
    {
        $this->data = array();
    }

    public function forgot_password()
    {
        if(Auth::id()) {
            return redirect()->route('login');
        } else {
            return view('forgot_password');
        }
    }

    public function send_reset_link(Request $request)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again later.');
        try {
            //Validate Email
            $resultUser = User::where('email', $request->email)->first();

            if(isset($resultUser) && !empty($resultUser->id)) {
                if($resultUser->status) {
                    //Generate token
                    $link = $resultUser->email.'_'.time();
                    $token = Crypt::encryptString($link);

                    //Save token
                    $password_reset = new PasswordReset();
                    $password_reset->email = $resultUser->email;
                    $password_reset->token = $token;
                    $password_reset->created_at = date('Y-m-d H:i:s');
                    $password_reset->save();

                    if($password_reset->email) {
                        $details = array(
                            'reset_link' => secured_url(route('reset_password', $token))
                        );
                        //Send reset link by email
                        $api_response = send_mail(array(
                            'to' => [$resultUser->email],
                            'subject' => 'VPM | Reset Password',
                            'body' => view('email.reset_link', $details)->render()
                        ));

                        $response = array('status' => TRUE, 'message' => 'Reset link sent successfully');
                    } else {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }

                } else {
                    $response = array('status' => FALSE, 'message' => 'Account suspended, contact admin.');
                }
            } else {
                $response = array('status' => FALSE, 'message' => 'Invalid email address');
            }
        } catch (\Exception $exception) {
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again later.');
        }

        if($response['status'] == TRUE) {
            return redirect()->route('login')->with('success', 'Reset link sent successfully.');
        } else {
            return back()->withInput()->with('error', $response['message']);
        }

    }

    public function reset_password($token, Request $request)
    {
        $tokenArray = explode('_', Crypt::decryptString($token));
        $attributes['email'] = $tokenArray[0];
        $attributes['date'] = date('Y-m-d H:i:s', $tokenArray[1]);
        $resultPasswordReset = PasswordReset::where('email', $attributes['email'])->where('token', $token)->first();

        if(isset($resultPasswordReset) && !empty($resultPasswordReset->email)) {
            if(strtotime($attributes['date']) > strtotime("-30 minutes")) {
                return redirect()->route('create_new_password', $token)->with('success', 'Please create new password');
            } else {
                //Delete Link
                PasswordReset::where('email', $attributes['email'])->where('token', $token)->delete();
                return redirect()->route('forgot_password')->with('error', 'Link Expired!');
            }
        } else {
            return redirect()->route('forgot_password')->with('error', 'Invalid request or link expired!');
        }

    }

    public function create_new_password($token)
    {
        $this->data['token'] = $token;
        return view('create_new_password', $this->data);
    }

    public function store_new_password(Request $request)
    {
        try {
            $attributes = $request->all();
            if($attributes['password'] == $attributes['confirm_password']) {

                $password = Hash::make($attributes['confirm_password']);
                $tokenArray = explode('_', Crypt::decryptString($attributes['token']));
                $resultUser = User::where('email', $tokenArray[0])->where('status', 1)->first();
                $resultUser->password = $password;
                if($resultUser->save()) {
                    //Delete Reset Link
                    PasswordReset::where('email', $tokenArray[0])->delete();
                    return redirect()->route('login')->with('success', 'Password changed, login to continue.');
                } else {
                    return redirect()->route('create_new_password', $attributes['token'])->with('error', 'Something went wrong, please try again later.');
                }
            } else {
                return redirect()->route('create_new_password', $attributes['token'])->with('error', 'New password mismatch!');
            }
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', 'Something went wrong, please try again.');
        }
    }


    public function excelToArray()
    {
        return view('pages.extras.excel_to_array');
    }

    public function convertExcelToArray(Request $request)
    {
        $attributes = $request->all();
        $excelData = Excel::toArray('', $attributes['excel_file']);
        array_shift($excelData[0]);
        $my_string = '';
        foreach ($excelData[0] as $key => $row) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]);
            $vm_delivery_date = date_format($date, 'mdYHis');
            $my_string .= '
{
    "email": "' . $row[3] . '",
    "properties":
    [
        {
            "property": "vm_uid",
            "value": "VM-' . $vm_delivery_date . '"
        },
        {
            "property": "firstname",
            "value": "' . $row[1] . '"
        },
        {
            "property": "lastname",
            "value": "' . $row[2] . '"
        },
        {
            "property": "website",
            "value": "' . $row[4] . '"
        },
        {
            "property": "company",
            "value": "' . $row[5] . '"
        },
        {
            "property": "phone",
            "value": "' . $row[6] . '"
        },
        {
            "property": "address",
            "value": "' . $row[7] . '"
        },
        {
            "property": "state",
            "value": "' . $row[8] . '"
        },
        {
            "property": "zip",
            "value": "' . $row[9] . '"
        }
    ]
},';
        }
        echo '<pre>';
        echo $my_string;die;
        dd($my_string, $excelData[0]);
    }

    public function extractNumber(Request $request)
    {
        if($request->method() == 'GET') {
            return view('pages.extras.extract_number');
        } else {

            $string = $request->string_to_extract;

            $position = strpos($string, 'result');

            $string_to_extract = substr($string, $position+1, strlen($string));

            preg_match_all('!\d+!', $string_to_extract, $matches);

            $this->data['result'] = $matches[0];

            return view('pages.extras.extract_number', $this->data);
        }
    }

    public function arrayToObject(Request $request)
    {
        if($request->method() == 'GET') {
            return view('pages.extras.array_to_object');
        } else {

            $string = $request->string_to_covert;

            $new_string = str_replace('array (','(object) array (',$string);
            $position = strpos($new_string, '(object) array (');

            $new_string = substr_replace($new_string , '$finalLeadsArray = array (', 0,strlen('(object) array ('));

            $this->data['result'] = $new_string;

            return view('pages.extras.array_to_object', $this->data);
        }
    }
}
