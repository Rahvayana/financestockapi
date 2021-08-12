<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


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
        // {
        //     "contact": {
        //       "email_address": "cool_alpaca25@example.com",
        //       "phone_number": "555-666-7788",
        //       "street_address": ["20 N San Mateo Dr"],
        //       "city": "San Mateo",
        //       "state": "CA",
        //       "postal_code": "94401",
        //       "country": "USA"
        //     },
        //     "identity": {
        //       "given_name": "John",
        //       "family_name": "Doe",
        //       "date_of_birth": "1990-01-01",
        //       "tax_id": "666-55-4321",
        //       "tax_id_type": "USA_SSN",
        //       "country_of_citizenship": "USA",
        //       "country_of_birth": "USA",
        //       "country_of_tax_residence": "USA",
        //       "funding_source": ["employment_income"]
        //     },
        //     "disclosures": {
        //       "is_control_person": false,
        //       "is_affiliated_exchange_or_finra": false,
        //       "is_politically_exposed": false,
        //       "immediate_family_exposed": false
        //     },
        //     "agreements": [
        //       {
        //         "agreement": "margin_agreement",
        //         "signed_at": "2020-09-11T18:09:33Z",
        //         "ip_address": "185.13.21.99"
        //       },
        //       {
        //         "agreement": "account_agreement",
        //         "signed_at": "2020-09-11T18:13:44Z",
        //         "ip_address": "185.13.21.99"
        //       },
        //       {
        //         "agreement": "customer_agreement",
        //         "signed_at": "2020-09-11T18:13:44Z",
        //         "ip_address": "185.13.21.99"
        //       }
        //     ],
        //     "documents": [
              
        //     ],
        //     "trusted_contact": {
        //       "given_name": "Jane",
        //       "family_name": "Doe",
        //       "email_address": "jane.doe@example.com"
        //     }
        //   }
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => '/v1/accounts',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
        dd($client);
    }
}
