<?php

namespace App\Audit;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class ImpersonatorResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return session('impersonator');
    }
}
