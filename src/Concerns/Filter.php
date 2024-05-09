<?php

namespace Devlogx\FilamentFathom\Concerns;

use Carbon\Carbon;

class Filter
{
    protected Carbon $from;
    protected Carbon $to;
    protected string $tz;

    public function __construct()
    {
        $this->tz = config('app.timezone');
    }

    /**
     * Building up the query params for the api call
     */
    public function toQuery(): string
    {
        return http_build_query([
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'tz' => $this->tz,
        ]);
    }

    /**
     * Generation a md5 hash out of the filter object for caching.
     */
    public function hash(): string
    {
        return md5($this->toQuery());
    }

    /**
     * The from date filter
     *
     * @return $this
     */
    public function setFrom(Carbon $from): Filter
    {
        $this->from = $from;

        return $this;
    }

    public function getFrom():Carbon{
        return $this->from;
    }

    /**
     * The to date filter
     *
     * @return $this
     */
    public function setTo(Carbon $to): Filter
    {
        $this->to = $to;

        return $this;
    }

    public function getTo():Carbon{
        return $this->to;
    }

    public function getTz():string{
        return $this->tz;
    }

}
