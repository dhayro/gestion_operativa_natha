<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\Empleado;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empleado::truncate();

        $areas = Area::pluck('id', 'nombre');
        $cargos = Cargo::pluck('id', 'nombre');

        $empleados = [
            ['area' => 'SUPERVISION', 'dni' => '19900220', 'nombre_completo' => 'CHIHUAN JIMENEZ DOMINGO ARISTARCO', 'cargo' => 'SUPERVISOR GENERAL'],
            ['area' => 'SUPERVISION', 'dni' => '41874847', 'nombre_completo' => 'CANALES HUARAZ RUDY ABISMAEL', 'cargo' => 'ING DE SEGURIDAD'],
            ['area' => 'SUPERVISION', 'dni' => '47039677', 'nombre_completo' => 'CHIHUAN ARTEAGA JUAN CARLOS', 'cargo' => 'ANALISTA DE SUMINISTRO NUEVO'],
            ['area' => 'SUPERVISION', 'dni' => '41206430', 'nombre_completo' => 'HUARACA MEZA ADMER', 'cargo' => 'ANALISTA DE MTTO CONEXIONES Y RECLAMOS'],
            ['area' => 'SUPERVISION', 'dni' => '71088505', 'nombre_completo' => 'HUINCHO SEDANO YORDAN JESUS', 'cargo' => 'ANALISTA DE DENUNCIAS Y EMERGENCIA'],
            ['area' => 'SUPERVISION', 'dni' => '46142579', 'nombre_completo' => 'CASTILLO GARCÍA KATIA JOSSY', 'cargo' => 'ASISTENTE ANALISTAS DE SUMINISTROS NUEVOS'],

            ['area' => 'EMERGENCIAS', 'dni' => '61733661', 'nombre_completo' => 'MESTAS CAVIÑA JIMMY JICOLT', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '44712602', 'nombre_completo' => 'HUAMAN VENTOCILLA BRITO SEGUNDO', 'cargo' => 'CHOFER EMERGENCIA'],
            ['area' => 'EMERGENCIAS', 'dni' => '41685200', 'nombre_completo' => 'CANALES SULCA MARCO ANTONIO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '42190817', 'nombre_completo' => 'TUESTA RUCOBA WILLY EDWARD', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '76398108', 'nombre_completo' => 'GUTIERREZ ROJAS ALEX', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '62289988', 'nombre_completo' => 'HUAYABA VASQUEZ JEYSON ALEJANDRO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '72120614', 'nombre_completo' => 'MURRIETA DAVILA ISAAC', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'EMERGENCIAS', 'dni' => '79052062', 'nombre_completo' => 'CALAMPA FERREYRA MAYKOL JUNIOR', 'cargo' => 'TECNICO ELECTRICISTA'],

            ['area' => 'FONOLUZ', 'dni' => '71004814', 'nombre_completo' => 'TORRES DEL AGUILA MARCO ANTONIO', 'cargo' => 'Aten al Cliente las 24 horas - Fonoluz'],
            ['area' => 'FONOLUZ', 'dni' => '75788700', 'nombre_completo' => 'CAHUAZA MENDOZA JOY ARQUIMEDES', 'cargo' => 'Aten al Cliente las 24 horas - Fonoluz'],
            ['area' => 'FONOLUZ', 'dni' => '71741270', 'nombre_completo' => 'VICTOR MANUEL DAVILA TUESTA', 'cargo' => 'Aten al Cliente las 24 horas - Fonoluz'],

            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '41820198', 'nombre_completo' => 'PADILLA ALONSO RAUL', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75249777', 'nombre_completo' => 'CORDOVA YSUIZA JARRY JIMMY', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '47790158', 'nombre_completo' => 'NAVARRO ROJAS ALEX DAVID', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '77819700', 'nombre_completo' => 'CARDENAS FLORES ROY ANTHONY', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '47536222', 'nombre_completo' => 'VASQUEZ MACUYAMA DAVID ANIBAL', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75586030', 'nombre_completo' => 'AMASIFUEN USHIÑAHUA JUAN JAIME', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '42510537', 'nombre_completo' => 'CUBAS SILVANO ROMER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '60714943', 'nombre_completo' => 'CAHUAZA MUENA DEYVI ANDERSON', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '81184552', 'nombre_completo' => 'VILCHEZ NUÑEZ SEGUNDO AMERICO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '72276984', 'nombre_completo' => 'SANCHES REATEGUI MANUEL', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '76285512', 'nombre_completo' => 'PUA ANCON FRANK ERIK', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '46727896', 'nombre_completo' => 'PARI PIO JHON HAMER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '43436733', 'nombre_completo' => 'GASLA BARDALES LUIS', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '72131995', 'nombre_completo' => 'MACEDO OÑATE JEFFER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75195260', 'nombre_completo' => 'AREVALO CHOTA JORDAN GUSSEPI', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '76227997', 'nombre_completo' => 'CURI CARDENAS ALBERTO WILLIAN', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '41196167', 'nombre_completo' => 'REATEGUI PINEDO DANY FRANK', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '62447519', 'nombre_completo' => 'SEGARRA ACHO GILMER GEYMI', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '73069118', 'nombre_completo' => 'FLORES ARBILDO JOSE LUIS', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '70141592', 'nombre_completo' => 'CHANCHARI VEGA MARBIN', 'cargo' => 'Técnico-Auxiliares de Reparto'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75815935', 'nombre_completo' => 'PANDURO SALAS FRANCO GIORDANO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '48041181', 'nombre_completo' => 'ARIRAMA CANAYO ERIK', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '45134718', 'nombre_completo' => 'NATORCE RIOS HEBER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '23704296', 'nombre_completo' => 'TORRES MUNGUIA EDGAR RONAL', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '77217154', 'nombre_completo' => 'MARIN VARGAS LEONCIO', 'cargo' => 'CHOFER ELECTRISISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '42426495', 'nombre_completo' => 'MARIÑO ASPAJO JULIO CESAR', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '72367120', 'nombre_completo' => 'SANDOVAL SORIA JHON PITER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '41769357', 'nombre_completo' => 'BARBARAN BUSTOS MARIO LUIS', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '71391371', 'nombre_completo' => 'CORDOVA GOMEZ EFRAIN', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '41059373', 'nombre_completo' => 'REATEGUI PINEDO JAIME SEGUNDO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '73497572', 'nombre_completo' => 'GUERRA LOMAS ERICKSON', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '46393473', 'nombre_completo' => 'ROJAS CARDENAS JHAN', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '77029829', 'nombre_completo' => 'JAVIER MENDEZ BARDALES', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '44386867', 'nombre_completo' => 'CHOTA NUÑEZ CARLOS AUGUSTO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75234727', 'nombre_completo' => 'DAVID SAAVEDRA RODRIGUEZ', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '76022280', 'nombre_completo' => 'DAVILA RIOS JHON JAIRO FERNANDO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '47730028', 'nombre_completo' => 'MEZA PAREDES ALOYSIO MAURICIO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '19875507', 'nombre_completo' => 'CALLUPE GONZALES JHONY', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '74885400', 'nombre_completo' => 'CORDERO HOYOS JAIR', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '40296249', 'nombre_completo' => 'LINARES URIBE JOHN', 'cargo' => 'CHOFER FURGONETA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '71348270', 'nombre_completo' => 'ALEX JUNIOR ROSARIO CHAVEZ', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '74725608', 'nombre_completo' => 'CARRANZA BARTOLO BENNY', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '74464005', 'nombre_completo' => 'GUIMARAES AHUANARI EDSON DAVID', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '81054493', 'nombre_completo' => 'VARGAS AGUILAR JUNIOR', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '71717708', 'nombre_completo' => 'MELENDEZ RUCOBA EDUARDO ANDRES', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '76686896', 'nombre_completo' => 'GUERRA SUAREZ NICK ALEX', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '62290149', 'nombre_completo' => 'IMUNDA VILLACREZ HITHLER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES', 'dni' => '75616102', 'nombre_completo' => 'FERREYRA VARGAS MARCO ANTONIO', 'cargo' => 'TECNICO ELECTRICISTA'],

            ['area' => 'UU.NN. AGUAYTIA', 'dni' => '48616942', 'nombre_completo' => 'JANNY LINET ORTIZ CALDERON', 'cargo' => 'Asistente Aguaytia'],
            ['area' => 'UU.NN. AGUAYTIA', 'dni' => '73515814', 'nombre_completo' => 'SABINO LAURENCIO YEISON SOLANO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'UU.NN. AGUAYTIA', 'dni' => '41551275', 'nombre_completo' => 'MARCOS FRANSISCO SANTIAGO ALEJANDRO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'UU.NN. AGUAYTIA', 'dni' => '45629367', 'nombre_completo' => 'ORDOÑEZ ARBILDO PEDRO', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'UU.NN. AGUAYTIA', 'dni' => '43598605', 'nombre_completo' => 'MENDOZA SEDANO WEBER LEONEL', 'cargo' => 'TECNICO ELECTRICISTA'],

            ['area' => 'UU.NN. ATALAYA', 'dni' => '40907776', 'nombre_completo' => 'JHONI CACERES SOLIER', 'cargo' => 'TECNICO ELECTRICISTA'],
            ['area' => 'UU.NN. ATALAYA', 'dni' => '46599591', 'nombre_completo' => 'CHOTA TUESTA CRISTIAN RODOLFO', 'cargo' => 'TECNICO ELECTRICISTA'],
        ];

        foreach ($empleados as $empleado) {
            $areaId = $areas[$empleado['area']] ?? null;
            $cargoId = $cargos[$empleado['cargo']] ?? null;

            if (!$areaId) {
                throw new \RuntimeException("Área '{$empleado['area']}' no encontrada para el empleado {$empleado['nombre_completo']}");
            }

            if (!$cargoId) {
                throw new \RuntimeException("Cargo '{$empleado['cargo']}' no encontrado para el empleado {$empleado['nombre_completo']}");
            }

            [$apellido, $nombres] = $this->separarNombreApellido($empleado['nombre_completo']);

            Empleado::create([
                'nombre' => $nombres,
                'apellido' => $apellido,
                'dni' => $empleado['dni'],
                'telefono' => null,
                'email' => $this->generarEmail($empleado['dni']),
                'direccion' => null,
                'estado' => true,
                'cargo_id' => $cargoId,
                'area_id' => $areaId,
                'ubigeo_id' => null,
                'licencia' => null,
            ]);
        }
    }

    private function separarNombreApellido(string $nombreCompleto): array
    {
        $nombreCompleto = preg_replace('/\s+/', ' ', trim($nombreCompleto));
        $partes = explode(' ', $nombreCompleto);

        if (count($partes) === 1) {
            return [$nombreCompleto, $nombreCompleto];
        }

        $palabrasConectoras = ['DE', 'DEL', 'LA', 'LAS', 'LOS', 'SAN', 'SANTA'];
        $apellidoPartes = [];

        while (count($partes) > 1 && count($apellidoPartes) < 2) {
            $token = array_shift($partes);
            $apellidoPartes[] = $token;

            if (in_array(strtoupper($token), $palabrasConectoras, true) && !empty($partes)) {
                $apellidoPartes[] = array_shift($partes);
            }
        }

        $apellido = implode(' ', $apellidoPartes);
        $nombres = trim(implode(' ', $partes));

        if ($nombres === '') {
            $nombres = $apellido;
        }

        return [$apellido, $nombres];
    }

    private function generarEmail(string $dni): string
    {
        return 'empleado' . $dni . '@example.com';
    }
}
