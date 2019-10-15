<?php

return [
    /*
     * Auth container binding
     */

    'enabled' => env('GOOGLE2FA_ENABLED', true),

    /*
     * Lifetime in minutes.
     * In case you need your users to be asked for a new one time passwords from time to time.
     */

    'lifetime' => 0, // 0 = eternal

    /*
     * Renew lifetime at every new request.
     */

    'keep_alive' => true,

    /*
     * Auth container binding
     */

    'auth' => 'auth',

    /*
     * 2FA verified session var
     */

    'session_var' => 'google2fa',

    /*
     * One Time Password request input name
     */
    'otp_input' => env('GOOGLE2FA_OTP_INPUT', 'one_time_password'),

    /*
     * One Time Password Window
     */
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret
     */
    'otp_secret_column' => env('GOOGLE2FA_OTP_SECRET_COLUMN', 'google2fa_secret'),

    /*
     * One Time Password View
     */
    'view' => 'google2fa.index',

    /*
     * One Time Password error message
     */
    'error_messages' => [
        'wrong_otp' => "The 'One Time Password' typed was wrong.",
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => true,

    /*
     * remember this device  
     */
    'remember_cookie_field' => env('GOOGLE2FA_REMEMBE_COOKIE_FIELD', 'remember_2fa')
];
