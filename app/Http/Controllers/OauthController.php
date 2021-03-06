<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * 授权操作
 * Class OauthController
 *
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function callback(Request $request, $oneDrive)
    {
        if (
            empty($request->get('state')) || !Session::has('state')
            || ($request->get('state') !== Session::get('state'))
        ) {
            Tool::showMessage('Invalid state', false);
            Session::forget('state');

            return view(config('olaindex.theme') . 'message');
        }

        Session::forget('state'); // 兼容下次登陆
        $code = $request->get('code');
        $oneDrive = app('onedrive');
        $form_params = [
            'client_id'     => $oneDrive->client_id,
            'client_secret' => $oneDrive->client_secret,
            'redirect_uri'  => $oneDrive->redirect_uri,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ];

        if ($oneDrive->account_type === 'cn') {
            $form_params = Arr::add(
                $form_params,
                'resource',
                Constants::REST_ENDPOINT_21V
            );
        }

        $curl = new Curl();
        $curl->post($oneDrive->access_token_url, $form_params);

        if ($curl->error) {
            Log::error(
                'OneDrive Login Err',
                [
                    'code' => $curl->errorCode,
                    'msg'  => $curl->errorMessage,
                ]
            );
            $msg = 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            Tool::showMessage($msg, false);

            return view(config('olaindex.theme') . 'message');
        } else {
            $token = collect($curl->response)->toArray();
            $access_token = $token['access_token'];
            $refresh_token = $token['refresh_token'];
            $expires = (int) $token['expires_in'] != 0 ? time() + $token['expires_in'] : 0;
            $data = [
                'access_token'         => $access_token,
                'refresh_token'        => $refresh_token,
                'access_token_expires' => $expires,
            ];

            $oneDrive->update($data);
            // Tool::updateConfig($data);

            return redirectSuccess('admin.onedrive.index');
        }
    }

    /**
     * @param $url
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function oauth($url = '')
    {
        // 跳转授权登录
        // $state = str_random(32);
        $state = urlencode($url ? 'http://' . $url : config('app.url')); // 添加中转
        session(['state' => $state]);
        $oneDrive = app('onedrive');
        $values = [
            'client_id'     => $oneDrive->client_id,
            'redirect_uri'  => $oneDrive->redirect_uri,
            'scope'         => $oneDrive->scopes,
            'response_type' => 'code',
            'state'         => $state,
        ];
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authorizationUrl = $oneDrive->authorize_url . "?{$query}";

        return redirect()->away($authorizationUrl);
    }

    /**
     * @param bool $redirect
     *
     * @return false|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|string
     * @throws \ErrorException
     */
    public function refreshToken($redirect = true)
    {
        $hasExpired = app('onedrive')->access_token_expires - time() <= 0;
        if (!$hasExpired) {
            return response()->json(['code' => 400, 'msg' => 'Bad Request']);
        }

        $form_params = [
            'client_id'     => app('onedrive')->client_id,
            'client_secret' => app('onedrive')->client_secret,
            'redirect_uri'  => app('onedrive')->redirect_uri,
            'refresh_token' => app('onedrive')->refresh_token,
            'grant_type'    => 'refresh_token',
        ];

        if (app('onedrive')->account_type === 'cn') {
            $form_params = Arr::add(
                $form_params,
                'resource',
                Constants::REST_ENDPOINT_21V
            );
        }

        $curl = new Curl();
        $curl->post(app('onedrive')->access_token_url, $form_params);

        if ($curl->error) {
            Log::error(
                'OneDrive Refresh Token Err',
                [
                    'code' => $curl->errorCode,
                    'msg'  => $curl->errorMessage,
                ]
            );

            return json_encode([
                'code' => $curl->errorCode,
                'msg'  => $curl->errorMessage,
                'response' => $curl->response
            ]);
        } else {
            $token = collect($curl->response)->toArray();
            $expires = (int) $token['expires_in'] != 0 ? time() + $token['expires_in'] : 0;
            $data = [
                'access_token'         => $token['access_token'],
                'refresh_token'        => $token['refresh_token'],
                'access_token_expires' => $expires,
            ];

            app('onedrive')->update($data);

            if ($redirect) {
                $redirect = Session::get('refresh_redirect') ?? '/';

                return redirect()->away($redirect);
            } else {
                return json_encode(['code' => 200, 'msg' => 'ok']);
            }
        }
    }
}
