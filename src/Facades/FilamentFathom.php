<?php

namespace Devlogx\FilamentFathom\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devlogx\FilamentFathom\FilamentFathom
 */
class FilamentFathom extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Devlogx\FilamentFathom\FilamentFathom::class;
    }
}
