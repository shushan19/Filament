<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAppOverView extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', Team::find(Filament::getTenant())->first()->members()->count()),
            Stat::make('Department', Department::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('All Users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Employees', Employee::query()->count())
                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
