<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Shopify\Auth\OAuth;
use Shopify\Context;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Callback function after installing app and retrieve access token
 */
Route::get('/', function () {
    $filePath = 'access_token.txt'; // storage/app/public/access_token.txt
    $accessToken = Storage::disk('local')->exists($filePath) ? Storage::disk('local')->get($filePath) : '';
    return view('welcome', ['accessToken' => $accessToken]);
});

/**
 * Callback function after installing app and retrieve access token
 */
Route::get('/redirect', function () {
    Context::initialize(
        $_ENV['SHOPIFY_API_KEY'],
        $_ENV['SHOPIFY_API_SECRET'],
        $_ENV['SHOPIFY_APP_SCOPES'],
        $_ENV['SHOPIFY_APP_HOST_NAME'],
        new \Shopify\Auth\FileSessionStorage('/tmp/php_sessions'),
        '2024-01',
        true,
        false,
    );

    $params = request()->query();
    $sessionIdSig = request()->cookie('SHOPIFY_SESSION_ID_SIG');
    $sessionId = request()->cookie('SHOPIFY_SESSION_ID');

    $cookies = [
        OAuth::SESSION_ID_SIG_COOKIE_NAME => $sessionIdSig ?? '',
        OAuth::SESSION_ID_COOKIE_NAME => $sessionId ?? '',
    ];

    $session = OAuth::callback($cookies, $params);
    Storage::disk('local')->put('access_token.txt', $session->getAccessToken() ?? '');
    return redirect('/');
});

/**
 * Install custom app
 */
Route::get('/install', function () {
    Context::initialize(
        $_ENV['SHOPIFY_API_KEY'],
        $_ENV['SHOPIFY_API_SECRET'],
        $_ENV['SHOPIFY_APP_SCOPES'],
        $_ENV['SHOPIFY_APP_HOST_NAME'],
        new \Shopify\Auth\FileSessionStorage('/tmp/php_sessions'),
        '2024-01',
        true,
        false,
    );

    $returnUrl = OAuth::begin(
        $_ENV['SHOPIFY_SHOP_NAME'],
        '/redirect',
        true,
        function ($cookie) {
            if ($cookie->getName() === 'shopify_session_id_sig') {
                Cookie::queue('SHOPIFY_SESSION_ID_SIG', $cookie->getValue(), 60);
            } else {
                Cookie::queue('SHOPIFY_SESSION_ID', $cookie->getValue(), 60);
            }
            return $cookie;
        }
    );

    return redirect($returnUrl);
});
