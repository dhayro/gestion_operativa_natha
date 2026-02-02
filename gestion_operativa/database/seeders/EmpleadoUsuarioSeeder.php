<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmpleadoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapeo de cargos a roles
        $cargoRoleMap = [
            'Gerente' => 'admin',
            'Supervisor' => 'supervisor',
            'Técnico' => 'tecnico',
            'Operario' => 'operario',
            'Ayudante' => 'operario',
            'Coordinador' => 'supervisor',
            'Inspector' => 'tecnico',
            'Encargado' => 'supervisor',
        ];

        // Obtener todos los empleados
        $empleados = Empleado::with('cargo')->get();
        
        $usuariosCreados = 0;
        $usuariosActualizados = 0;

        foreach ($empleados as $empleado) {
            // Generar patrón de usuario: apellido.nombre@empresa.local
            $nombre = strtolower(trim($empleado->nombre));
            $apellido = strtolower(trim($empleado->apellido));
            
            // Remover espacios y caracteres especiales
            $nombre = preg_replace('/\s+/', '', $nombre);
            $apellido = preg_replace('/\s+/', '', $apellido);
            
            // Crear email: apellido.nombre@empresa.local
            $email = $apellido . '.' . $nombre . '@empresa.local';

            // Evitar duplicados agregando número si es necesario
            $emailBase = $email;
            $contador = 1;
            while (User::where('email', $email)->where('id', '!=', $empleado->id)->exists()) {
                $email = str_replace('@empresa.local', $contador . '@empresa.local', $emailBase);
                $contador++;
            }

            // Obtener o crear usuario
            $usuario = User::updateOrCreate(
                ['empleado_id' => $empleado->id],
                [
                    'name' => $empleado->nombre . ' ' . $empleado->apellido,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'perfil' => 'operario', // Perfil por defecto
                ]
            );

            // Asignar rol según cargo
            $nombreCargo = $empleado->cargo?->nombre ?? 'Operario';
            $rolName = $cargoRoleMap[$nombreCargo] ?? 'operario';
            
            // Buscar rol por columna 'nombre'
            $role = DB::table('roles')->where('nombre', $rolName)->first();
            if ($role) {
                // Detach roles anteriores y asignar el nuevo
                $usuario->roles()->sync([$role->id]);
            }

            // Contar creados vs actualizados
            if ($usuario->wasRecentlyCreated) {
                $usuariosCreados++;
            } else {
                $usuariosActualizados++;
            }
        }

        // Mostrar resumen
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║  EMPLEADOS RELACIONADOS CON USUARIOS EXITOSAMENTE  ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("✓ Usuarios creados: {$usuariosCreados}");
        $this->command->info("✓ Usuarios actualizados: {$usuariosActualizados}");
        $this->command->info('');
        $this->command->info('PATRÓN DE USUARIO: apellido.nombre@empresa.local');
        $this->command->info('');
        $this->command->info('MAPEO DE CARGOS A ROLES:');
        foreach ($cargoRoleMap as $cargo => $rol) {
            $this->command->line("  • {$cargo} → {$rol}");
        }
    }
}
