<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * CSRFバリデーションから除外するURI
     * @var array
     */
    protected $except = [
        // 外部からのPOST受け取り先をCSRF処理から除外
        'inport_post/*',
    ];
}
