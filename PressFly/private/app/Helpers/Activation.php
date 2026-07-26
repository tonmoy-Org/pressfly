<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Activation
{
    public static function checkLicense(): bool
    {
        return true;
    }

    public static function validateLicense(): bool
    {
        return true;
    }

    public static function licenseCurlRequest($data = [])
    {
        $response = Http::acceptJson()
            ->post('https://verify.mightyscripts.com/api/license', [
                'purchase_code' => $data['purchase_code'],
                'envato_id' => 23491785,
                'domain' => \request()->server('HTTP_HOST'),
                'url' => \url('/'),
            ]);

        return $response->json();
    }
}
