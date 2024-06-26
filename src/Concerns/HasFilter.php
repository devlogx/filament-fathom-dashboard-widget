<?php

namespace Devlogx\FilamentFathom\Concerns;

use Devlogx\FilamentFathom\Facades\FilamentFathom;
use Devlogx\FilamentFathom\FilamentFathomPlugin;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Support\Enums\ActionSize;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

trait HasFilter
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(trans('filament-fathom-dashboard-widget::translations.filter.title'))
                    ->description(trans('filament-fathom-dashboard-widget::translations.filter.description'))
                    ->icon(fn (): string => FilamentFathomPlugin::get()->getFilterSectionIcon())
                    ->iconColor(fn (): string => FilamentFathomPlugin::get()->getFilterSectionIconColor())
                    ->headerActions([
                        Action::make('open')
                            ->label(trans('filament-fathom-dashboard-widget::translations.filter.open_pirsch'))
                            ->hidden(fn () => ! FilamentFathomPlugin::get()->shouldShowFathomLink())
                            ->icon('heroicon-s-link')
                            ->color('primary')
                            ->size(ActionSize::Small)
                            ->url(FilamentFathom::getDashboardLink())
                            ->openUrlInNewTab(),
                    ])
                    ->schema([
                        DateRangePicker::make('date_range')
                            ->label(trans('filament-fathom-dashboard-widget::translations.filter.select_range'))
                            ->columnSpan('full')
                            ->displayFormat('DD.MM.YYYY')
                            ->format('d.m.Y')
                            ->startDate(now()->subDays(30), true)
                            ->endDate(now(), true)
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}
