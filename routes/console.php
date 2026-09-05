<?php

use App\Console\Commands\EnviarRecordatoriosCuotas;
use App\Console\Commands\GenerarAlertasNotificaciones;
use App\Console\Commands\LimpiarNotificacionesAntiguas;
use App\Console\Commands\NotificarCompromisosCobro;
use App\Console\Commands\NotificarCuotasVendedor;
use App\Infrastructure\Productos\Models\Producto;
use App\Infrastructure\Usuarios\Models\User;
use App\Notifications\StockBajoNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $productosStockBajo = Producto::where('activo', true)
        ->whereColumn('stock_actual', '<=', 'stock_minimo')
        ->get();

    if ($productosStockBajo->isEmpty()) {
        return;
    }

    
    
    
    
    
    
    
    
    
    
    User::with('roles.permisos')
        ->get()
        ->filter(fn ($u) => $u->tienePermiso('*'))
        ->each(fn ($admin) => $admin->notifyNow(new StockBajoNotification($productosStockBajo)));

})->dailyAt('08:00')->name('check-stock-bajo')->withoutOverlapping();


Schedule::command(EnviarRecordatoriosCuotas::class, ['--dias=3'])
    ->dailyAt('07:30')
    ->name('cuotas-recordatorios')
    ->withoutOverlapping();


Schedule::command(GenerarAlertasNotificaciones::class)
    ->dailyAt('07:00')
    ->name('notificaciones-alertas')
    ->withoutOverlapping();


Schedule::command(LimpiarNotificacionesAntiguas::class)
    ->weeklyOn(0, '00:00')
    ->name('notificaciones-limpieza')
    ->withoutOverlapping();



Schedule::command(NotificarCompromisosCobro::class)
    ->everyFiveMinutes()
    ->name('compromisos-cobro-recordatorios')
    ->withoutOverlapping();


Schedule::command(NotificarCuotasVendedor::class, ['--dias=3'])
    ->dailyAt('07:30')
    ->name('cuotas-notificar-vendedor')
    ->withoutOverlapping();
