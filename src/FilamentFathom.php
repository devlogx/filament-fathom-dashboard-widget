<?php

namespace Devlogx\FilamentFathom;

use Carbon\CarbonInterval;
use Devlogx\FilamentFathom\Concerns\Filter;
use Illuminate\Support\Facades\Cache;
use MarcReichel\LaravelFathom\Fathom;

class FilamentFathom
{
    /**
     * @throws \Exception Missing Fathom API-Token and or Site id
     */
    public function __construct()
    {
        if (! config('filament-fathom-dashboard-widget.api_token') || ! config('filament-fathom-dashboard-widget.site_id')) {
            throw new \Exception('Fathom API-Token and Site ID are required.');
        }
    }

    /**
     * Get the Fathom Dashboard link for the right domain.
     */
    public function getDashboardLink(): string
    {
        return 'https://app.usefathom.com/?site=' . config('filament-fathom-dashboard-widget.site_id');
    }

    private function getCachedValue(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, config('filament-fathom-dashboard-widget.cache_time'), $callback);
    }

    /**
     * Get the active visitors, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function activeVisitors(Filter $filter, bool $rawData = false): array | string | null
    {
        if ($rawData) {
            return Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->currentVisitors(true);
        }

        return $this->getCachedValue('current-visitors-' . $filter->hash(), function () {
            return Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->currentVisitors(false)['total'];
        });
    }

    /**
     * Get the session duration, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function sessionDuration(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['avg_duration'])
                ->groupByMonth()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();
        }

        return $this->getCachedValue('month-avg-time-' . $filter->hash(), function () use ($filter) {
            $durationDays = Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['avg_duration'])
                ->groupByMonth()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();

            if (isset($durationDays['message'])) {
                return '00:00';
            }

            $avgTimeArray = array_map(function ($item) {
                return (int) $item['avg_duration'];
            }, $durationDays);
            if (count($avgTimeArray) == 2) {
                $avgDuration = array_sum($avgTimeArray) / 2;
            } else {
                $avgDuration = array_sum($avgTimeArray);
            }

            return CarbonInterval::seconds($avgDuration)->cascade()->format('%I:%S');
        });
    }

    /**
     * Get the visitors, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function visitors(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['visits'])
                ->groupByDay()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();
        }

        return $this->getCachedValue('month-visitors-' . $filter->hash(), function () use ($filter) {
            $stats = Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['visits'])
                ->groupByDay()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();

            if (isset($stats['message'])) {
                return [0, [0]];
            }

            $visitsArray = array_map(function ($item) {
                return (int) $item['visits'];
            }, $stats);
            $totalVisits = array_sum($visitsArray);

            return [
                $totalVisits,
                $visitsArray,
            ];
        });
    }

    /**
     * Get the page views, if set $rawData = true, the return value is the raw API data
     *
     * @throws \Exception
     */
    public function views(Filter $filter, bool $rawData = false): array | string
    {
        if ($rawData) {
            return Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['pageviews'])
                ->groupByDay()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();
        }

        return $this->getCachedValue('month-views-' . $filter->hash(), function () use ($filter) {
            $stats = Fathom::site(config('filament-fathom-dashboard-widget.site_id'))
                ->aggregate(['pageviews'])
                ->groupByDay()
                ->orderBy('timestamp', 'asc')
                ->fromDate($filter->getFrom()->format('Y-m-d H:i:s'))
                ->toDate($filter->getTo()->format('Y-m-d H:i:s'))
                ->timezone($filter->getTz())
                ->get();

            if (isset($stats['message'])) {
                return [0, [0]];
            }

            $pageViewsArray = array_map(function ($item) {
                return (int) $item['pageviews'];
            }, $stats);
            $totalPageViews = array_sum($pageViewsArray);

            return [
                $totalPageViews,
                $pageViewsArray,
            ];
        });
    }
}
