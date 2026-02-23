<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medidor;
use App\Models\Suministro;

class SincronizarEstadoMedidores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medidores:sincronizar-estado {--force : Ejecutar sin confirmación}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Sincroniza el estado de medidores basado en asignaciones de suministros (1=Disponible, 2=Asignado)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿Deseas sincronizar el estado de todos los medidores?')) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $this->info('Iniciando sincronización de estados de medidores...');
        $this->newLine();

        // Paso 1: Marcar todos como disponibles (estado = 1)
        $this->info('Paso 1: Marcando todos los medidores como disponibles...');
        Medidor::query()->update(['estado' => 1]);
        $this->line('✓ Todos los medidores marcados como disponibles (estado = 1)');
        $this->newLine();

        // Paso 2: Marcar como asignados (estado = 2) aquellos que tienen suministro
        $this->info('Paso 2: Marcando medidores asignados a suministros...');
        
        $medidoresAsignados = Suministro::whereNotNull('medidor_id')
            ->distinct()
            ->pluck('medidor_id')
            ->toArray();
        
        if (!empty($medidoresAsignados)) {
            Medidor::whereIn('id', $medidoresAsignados)
                ->update(['estado' => 2]);
            $this->line('✓ ' . count($medidoresAsignados) . ' medidores marcados como asignados (estado = 2)');
        } else {
            $this->line('✓ No hay medidores asignados');
        }
        
        $this->newLine();

        // Paso 3: Mostrar resumen
        $this->info('Resumen de estados:');
        $this->newLine();
        
        $disponibles = Medidor::where('estado', 1)->count();
        $asignados = Medidor::where('estado', 2)->count();
        $total = Medidor::count();
        
        $this->table(
            ['Estado', 'Cantidad', 'Porcentaje'],
            [
                ['Disponible', $disponibles, round(($disponibles / $total) * 100, 2) . '%'],
                ['Asignado', $asignados, round(($asignados / $total) * 100, 2) . '%'],
                ['TOTAL', $total, '100%'],
            ]
        );
        
        $this->newLine();
        $this->info('✓ Sincronización completada exitosamente');

        return Command::SUCCESS;
    }
}
