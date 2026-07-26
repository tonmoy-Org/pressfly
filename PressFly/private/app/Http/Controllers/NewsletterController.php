<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class NewsletterController extends Controller
{
    /**
     * @see https://mailchimp.com/developer/marketing/api/list-members/add-member-to-list/
     */
    public function subscribe()
    {
        $api_key = \get_option('mailchimp_api_key');
        $list_id = \get_option('mailchimp_list_id');

        if (!$api_key || !$list_id) {
            return [
                'status' => 0,
                'message' => __('Make sure to set the Mailchimp API key and list ID from the admin settings.'),
            ];
        }

        $dc = \explode("-", $api_key)[1] ?? null;
        if (!$dc) {
            return [
                'status' => 0,
                'message' => __('The Mailchimp API key is invalid.'),
            ];
        }

        try {
            $response = Http::asJson()
                ->withBasicAuth('anystring', $api_key)
                ->post(
                    "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members",
                    [
                        'email_address' => \request()->post('email'),
                        'status' => 'pending',
                    ]
                );

            $response->throw();

            return [
                'status' => 1,
                'message' => __('Please check your email to confirm your subscription'),
            ];
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            $response_body = $exception->response->json();

            if ($exception->response->clientError()) {
                $error = $response_body['errors'][0]['message'] ?? \explode('. ', $response_body['detail'])[0];

                return [
                    'status' => 0,
                    'message' => $error,
                ];
            }

            if ($exception->response->serverError()) {
                \info(print_r($response_body, true));
                return [
                    'status' => 0,
                    'message' => __('Server Error'),
                ];
            }
        } catch (\Exception $exception) {
            \info(print_r($exception->getMessage(), true));
            return [
                'status' => 0,
                'message' => __('Server Error'),
            ];
        }
    }
}
