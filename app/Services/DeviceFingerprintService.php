<?php

namespace App\Services;

class DeviceFingerprintService
{
    public function generate($request)
    {
        $components = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->header('Accept'),
        ];

        $fingerprint = implode('|', array_filter($components));
        return hash('sha256', $fingerprint);
    }

    public function isMatch($fingerprint, $request)
    {
        return $this->generate($request) === $fingerprint;
    }
}
