<?php

namespace Azamatx\EskizsmsLaravel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Azamatx\EskizsmsLaravel\Skeleton\SkeletonClass
 */
class EskizsmsLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eskizsms-laravel';
    }
}
