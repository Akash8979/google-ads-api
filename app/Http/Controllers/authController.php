<?php

namespace App\Http\Controllers;

use Google\Auth\CredentialsLoader;
use Illuminate\Http\Request;
use Google\Auth\OAuth2;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsClientBuilder;
use Google\AdsApi\Examples\Authentication;

class authController extends Controller
{
    public function generateAuthforuser(Request $req)
    {
        // echo "hiii";
        $authResonse = new OAuth2([
            'clientId' => "688588022308-cbfjgiprcakcfhbgoi1j2iajbo06j48e.apps.googleusercontent.com",
            'clientSecret' => "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",
            'redirectUri' => "http://localhost:8000",
            'scope' => "https://www.googleapis.com/auth/adwords",
            'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            "tokenCredentialUri" => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'state' => sha1(openssl_random_pseudo_bytes(1024))
        ]);
        // print_r($authResonse);
        // $code = "4/0AfgeXvuHnBW7pim_vsRNnC9NISHtZ9zIHYFULsKhZoC006nFdU29nK5_ooTUPf2AzjYvPA";
        $authResonse->setGrantType("authorization_code");
        // $authResonse->setGrantType("authorization_code");
        $res = $authResonse->buildFullAuthorizationUri();
        // $res1 = $authResonse->getAuthorizationUri();
        // $authResonse->setCode($code);
        // $res = $authResonse->fetchAuthToken();
        //    $res2 = $res->getRequestTarget();
        //    $res3 = CredentialsLoader::makeHttpClient($res2)->getUri();
        return response()->json(['response' => $res]);
    }

    public function adsApiResponse(Request $request)
    {
        $code = trim($request->code);
        $authResonse = new OAuth2([
            'clientId' => "688588022308-cbfjgiprcakcfhbgoi1j2iajbo06j48e.apps.googleusercontent.com",
            'clientSecret' => "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",
            'redirectUri' => "http://localhost:8000",
            'scope' => "https://www.googleapis.com/auth/adwords",
            // 'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            "tokenCredentialUri" => CredentialsLoader::TOKEN_CREDENTIAL_URI,

            'state' => sha1(openssl_random_pseudo_bytes(1024))
        ]);
        $authResonse->setCode($code);
        $res = $authResonse->fetchAuthToken();
        // print_r($request->);
        return response()->json(['response' =>  $res]);
    }
}
