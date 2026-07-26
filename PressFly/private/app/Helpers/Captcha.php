<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Captcha
{
    public static function verify($post_data)
    {
        $captcha_type = get_option('captcha_type');

        if ($captcha_type === 'recaptcha_v2_checkbox') {
            return self::recaptchaV2CheckboxVerify($post_data);
        }

        if ($captcha_type === 'recaptcha_v2_invisible') {
            return self::recaptchaV2InvisibleVerify($post_data);
        }

        if ($captcha_type === 'hcaptcha_checkbox') {
            return self::hcaptchaCheckboxVerify($post_data);
        }

        if ($captcha_type === 'solvemedia') {
            return self::solvemediaVerify($post_data);
        }

        return false;
    }

    public static function recaptchaV2CheckboxVerify($post_data = [])
    {
        $secretKey = get_option('recaptcha_v2_checkbox_secret_key');

        if (!isset($post_data['g-recaptcha-response'])) {
            self::errorVerify($post_data);
            return false;
        }

        $data = array(
            'secret' => $secretKey,
            'response' => $post_data['g-recaptcha-response'],
        );

        try {
            $response = Http::asForm()->post('https://www.recaptcha.net/recaptcha/api/siteverify', $data);
            $response->throw();
            $responseData = $response->json();
        } catch (\Exception $exception) {
            self::errorVerify($post_data);
            return false;
        }

        if ($responseData['success']) {
            self::successVerify($post_data);
            return true;
        }

        self::errorVerify($post_data);
        return false;
    }

    public static function recaptchaV2InvisibleVerify($post_data = [])
    {
        $secretKey = get_option('recaptcha_v2_invisible_secret_key');

        if (!isset($post_data['g-recaptcha-response'])) {
            self::errorVerify($post_data);
            return false;
        }

        $data = array(
            'secret' => $secretKey,
            'response' => $post_data['g-recaptcha-response'],
        );

        try {
            $response = Http::asForm()->post('https://www.recaptcha.net/recaptcha/api/siteverify', $data);
            $response->throw();
            $responseData = $response->json();
        } catch (\Exception $exception) {
            self::errorVerify($post_data);
            return false;
        }

        if ($responseData['success']) {
            self::successVerify($post_data);
            return true;
        }

        self::errorVerify($post_data);
        return false;
    }

    public static function hcaptchaCheckboxVerify($post_data = [])
    {
        $secretKey = \get_option('hcaptcha_checkbox_secret_key');

        if (!isset($post_data['h-captcha-response'])) {
            self::errorVerify($post_data);
            return false;
        }

        $data = [
            'secret' => $secretKey,
            'response' => $post_data['h-captcha-response'],
        ];

        try {
            $response = Http::asForm()->post('https://hcaptcha.com/siteverify', $data);
            $response->throw();
            $responseData = $response->json();
        } catch (\Exception $exception) {
            self::errorVerify($post_data);
            return false;
        }

        if ($responseData['success']) {
            self::successVerify($post_data);
            return true;
        }

        self::errorVerify($post_data);
        return false;
    }

    public static function solvemediaVerify($post_data = [])
    {
        $solvemedia_verification_key = get_option('solvemedia_verification_key');
        $solvemedia_authentication_key = get_option('solvemedia_authentication_key');

        if (!isset($post_data['adcopy_challenge']) || !isset($post_data['adcopy_response'])) {
            self::errorVerify($post_data);
            return false;
        }

        $data = array(
            'privatekey' => $solvemedia_verification_key,
            'challenge' => $post_data['adcopy_challenge'],
            'response' => $post_data['adcopy_response'],
            'remoteip' => get_ip(),
        );

        $response = Http::asForm()->post('http://verify.solvemedia.com/papi/verify', $data);
        $answers = \explode("\n", $response->body());

        $hash = sha1($answers[0] . $post_data['adcopy_challenge'] . $solvemedia_authentication_key);

        if ($hash !== $answers[2]) {
            self::errorVerify($post_data);
            return false;
        }

        if (trim($answers[0]) == 'true') {
            self::successVerify($post_data);
            return true;
        }

        self::errorVerify($post_data);
        return false;
    }

    public static function successVerify($post_data)
    {
    }

    public static function errorVerify($post_data)
    {
    }

    public static function recaptchaV3Verify($post_data = [])
    {
        $secretKey = get_option('recaptcha_v3_secret_key');

        if (is_null($post_data['g-recaptcha-response'])) {
            return [
                'status' => false,
                'message' => 'Missing g-recaptcha-response',
            ];
        }

        $data = array(
            'secret' => $secretKey,
            'response' => $post_data['g-recaptcha-response'],
        );

        try {
            $response = Http::asForm()->post('https://www.recaptcha.net/recaptcha/api/siteverify', $data);
            $response->throw();
            $responseData = $response->json();
        } catch (\Exception $exception) {
            return false;
        }

        if ($responseData['success'] === false) {
            return [
                'status' => false,
                'message' => $responseData['error-codes'],
            ];
        }

        if (!(isset($responseData['action']) && $responseData['action'] == 'articleShow')) {
            return [
                'status' => false,
                'message' => 'Invalid reCaptchaV3 action',
            ];
        }

        if (isset($responseData['score'])) {
            return [
                'status' => true,
                'score' => $responseData['score'],
            ];
        }

        return [
            'status' => false,
            'message' => '',
        ];
    }
}
