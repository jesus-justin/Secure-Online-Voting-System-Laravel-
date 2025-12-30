<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function verify($token, $action = null)
    {
        if (!config('recaptcha.enabled')) {
            return true;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $token,
        ]);

        $result = $response->json();

        if (!$result['success']) {
            return false;
        }

        if ($action && isset($result['action']) && $result['action'] !== $action) {
            return false;
        }

        // reCAPTCHA v3 returns a score between 0.0 and 1.0
        if (isset($result['score']) && $result['score'] < 0.5) {
            return false;
        }

        return true;
    }
}
