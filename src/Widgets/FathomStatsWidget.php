<?php

namespace Devlogx\FilamentFathom\Widgets;

use Carbon\Carbon;
use Devlogx\FilamentFathom\Concerns\Filter;
use Devlogx\FilamentFathom\Facades\FilamentFathom;
use Devlogx\FilamentFathom\FilamentFathomPlugin;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Str;

class FathomStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -9;

    public function getPollingInterval(): ?string
    {
        return FilamentFathomPlugin::get()->getPollingInterval();
    }

    protected function getStats(): array
    {

        $dateParts = explode('-', Str::remove(' ', $this->filters['date_range']));
        $startDate = ! is_null($dateParts[0] ?? null) ?
            Carbon::parse($dateParts[0])->startOfDay() :
            now()->subDays(30)->startOfDay();

        $endDate = ! is_null($dateParts[1] ?? null) ?
            Carbon::parse($dateParts[1])->endOfDay() :
            now()->endOfDay();

        $diffInDays = round($startDate->diffInDays($endDate), 0);
        $myFilter = (new Filter())
            ->setFrom($startDate)
            ->setTo($endDate);

        [$total_visits, $visit_chart] = FilamentFathom::visitors($myFilter);
        [$total_views, $views_chart] = FilamentFathom::views($myFilter);

        return [
            Stat::make(trans('filament-fathom-dashboard-widget::translations.widget.live_visitors.label'), FilamentFathom::activeVisitors($myFilter))
                ->description(trans('filament-fathom-dashboard-widget::translations.widget.live_visitors.description'))
                ->icon(FilamentFathomPlugin::get()->getLiveVisitorIcon())
                ->color(FilamentFathomPlugin::get()->getLiveVisitorColor()),
            Stat::make(trans('filament-fathom-dashboard-widget::translations.widget.visitors.label'), $total_visits)
                ->description(trans('filament-fathom-dashboard-widget::translations.widget.visitors.description', ['x' => $diffInDays]))
                ->icon(FilamentFathomPlugin::get()->getVisitorsIcon())
                ->color(FilamentFathomPlugin::get()->getVisitorsColor())
                ->chart($visit_chart),
            Stat::make(trans('filament-fathom-dashboard-widget::translations.widget.views.label'), $total_views)
                ->description(trans('filament-fathom-dashboard-widget::translations.widget.views.description', ['x' => $diffInDays]))
                ->icon(FilamentFathomPlugin::get()->getViewsIcon())
                ->color(FilamentFathomPlugin::get()->getViewsColor())
                ->chart($views_chart),
            Stat::make(trans('filament-fathom-dashboard-widget::translations.widget.session.label'), (string) FilamentFathom::sessionDuration($myFilter))
                ->description(trans('filament-fathom-dashboard-widget::translations.widget.session.description', ['x' => $diffInDays]))
                ->icon(FilamentFathomPlugin::get()->getSessionTimeIcon())
                ->color(FilamentFathomPlugin::get()->getSessionTimeColor()),
        ];
    }
}
