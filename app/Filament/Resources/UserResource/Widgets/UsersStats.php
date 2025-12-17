<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListUsers::class;
    }

    protected function getStats(): array
    {
        $registeredToday = $this->getPageTableQuery()->where('created_at', '>', today()->startOfDay())->count();
        $lastSevenDays = $this->getPageTableQuery()->where('created_at', '>', now()->subDays(7)->startOfDay())->count();
        $lastMonth = $this->getPageTableQuery()->where('created_at', '>', now()->subMonth()->startOfDay())->count();

        return [
            Stat::make('Total Users', $this->getPageTableQuery()->count()),
            Stat::make('Registered Today', $registeredToday),
            Stat::make('Last 7 Days', $lastSevenDays),
            Stat::make('Last 30 Days', $lastMonth),
        ];
    }
}
