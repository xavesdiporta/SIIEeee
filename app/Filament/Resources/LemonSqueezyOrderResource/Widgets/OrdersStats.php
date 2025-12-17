<?php

namespace App\Filament\Resources\LemonSqueezyOrderResource\Widgets;

use App\Filament\Resources\LemonSqueezyOrderResource\Pages\ListOrders;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersStats extends BaseWidget
{
    protected static ?string $heading = 'Chart';

    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListOrders::class;
    }

    protected function getStats(): array
    {
        $total = $this->getPageTableQuery()->sum('total') / 100;
        $totalToday = $this->getPageTableQuery()->where('ordered_at', '>', today()->startOfDay())->sum('total') / 100;
        $lastSevenDays = $this->getPageTableQuery()->where('ordered_at', '>', now()->subDays(7)->startOfDay())->sum('total') / 100;

        return [
            Stat::make('Orders Count', $this->getPageTableQuery()->count()),
            Stat::make('Sales', '$'.number_format($total)),
            Stat::make('Sales Today', '$'.number_format($totalToday)),
            Stat::make('Last 7 Days', '$'.number_format($lastSevenDays)),
        ];
    }
}
