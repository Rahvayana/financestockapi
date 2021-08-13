<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $alpaca_id=User::select('alpaca_id')->where('email',$credentials['email'])->pluck('alpaca_id')->first();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }else{
            return response()->json(['message' => 'Authorized','alpaca_id'=>$alpaca_id], 200);
        }

    }

    public function register(Request $request)
    {

        $exist=User::where('email',$request->contact['email_address'])->first();
        if($exist){
            return [
                'status' => 401,
                'message' => 'Email Exists, Try Another One'
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://broker-api.sandbox.alpaca.markets/v1/accounts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "contact": {
            "email_address": "'.$request->contact['email_address'].'",
            "phone_number": "'.$request->contact['phone_number'].'",
            "street_address": '.json_encode($request->contact['street_address']).',
            "city": "'.$request->contact['city'].'",
            "state": "'.$request->contact['state'].'",
            "postal_code": "'.$request->contact['postal_code'].'",
            "country": "'.$request->contact['country'].'"
        },
        "identity": {
            "given_name": "'.$request->identity['given_name'].'",
            "family_name": "'.$request->identity['family_name'].'",
            "date_of_birth": "'.$request->identity['date_of_birth'].'",
            "tax_id": "'.$request->identity['tax_id'].'", 
            "tax_id_type": "'.$request->identity['tax_id_type'].'",
            "country_of_citizenship": "'.$request->identity['country_of_citizenship'].'",
            "country_of_birth": "'.$request->identity['country_of_birth'].'",
            "country_of_tax_residence": "'.$request->identity['country_of_tax_residence'].'",
            "funding_source": '.json_encode($request->identity['funding_source']).'
        },
        "disclosures": {
            "is_control_person": false,
            "is_affiliated_exchange_or_finra": false,
            "is_politically_exposed": false,
            "immediate_family_exposed": false
        },
        "agreements": [
            {
            "agreement": "margin_agreement",
            "signed_at": "2020-09-11T18:09:33Z",
            "ip_address": "185.13.21.99"
            },
            {
            "agreement": "account_agreement",
            "signed_at": "2020-09-11T18:13:44Z",
            "ip_address": "185.13.21.99"
            },
            {
            "agreement": "customer_agreement",
            "signed_at": "2020-09-11T18:13:44Z",
            "ip_address": "185.13.21.99"
            }
        ],
        "documents": [
            {
            "document_type": "identity_verification",
            "document_sub_type": "passport",
            "content": "QWxwYWNhcyBjYW5ub3QgbGl2ZSBhbG9uZS4=",
            "mime_type": "image/jpeg"
            }
        ],
        "trusted_contact": {
            "given_name": "'.$request->trusted_contact['given_name'].'",
            "family_name": "'.$request->trusted_contact['family_name'].'",
            "email_address": "'.$request->trusted_contact['email_address'].'"
        }
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic Q0tVQzkzVFlaUUY3S0ZNTUVLNkk6ME93cTZ5ZDk3UmhIT1FyWndhR0hrbzNzNFNvS3JrZmVRTFdPUm1ySg==',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        if($response){
            $user=new User();
            $user->name=$request->identity['given_name']." ".$request->identity['family_name'];
            $user->email=$request->contact['email_address'];
            $user->password=Hash::make($request->contact['password']);
            $user->alpaca_id=json_decode($response)->id;
            $user->save();
            return response($response);
        }else{
            return response($response);
        }
    }
}
