<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TriviaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Cultura General',
                'descripcion' => 'Preguntas de conocimiento general para todo público.',
                'color' => '#7c3aed',
                'niveles' => [
                    'Fácil' => [
                        ['¿Cuál es el océano más grande del mundo?', ['Pacífico', 'Atlántico', 'Índico', 'Ártico'], 0],
                        ['¿Cuántos días tiene un año bisiesto?', ['366', '365', '360', '364'], 0],
                    ],
                    'Difícil' => [
                        ['¿Qué metal es líquido a temperatura ambiente?', ['Mercurio', 'Plomo', 'Hierro', 'Estaño'], 0],
                        ['¿Qué instrumento mide la presión atmosférica?', ['Barómetro', 'Termómetro', 'Higrómetro', 'Anemómetro'], 0],
                    ],
                ],
            ],
            [
                'nombre' => 'Historia',
                'descripcion' => 'Preguntas de historia universal y de Bolivia.',
                'color' => '#d97706',
                'niveles' => [
                    'Fácil' => [
                        ['¿En qué año llegó Cristóbal Colón a América?', ['1492', '1500', '1453', '1600'], 0],
                        ['¿Quién fue el primer presidente de Bolivia?', ['Antonio José de Sucre', 'Simón Bolívar', 'José Ballivián', 'Andrés de Santa Cruz'], 0],
                    ],
                    'Difícil' => [
                        ['¿En qué año finalizó la Segunda Guerra Mundial?', ['1945', '1939', '1944', '1950'], 0],
                        ['¿Qué imperio precolombino tuvo su centro en el Cusco?', ['Imperio Inca', 'Imperio Azteca', 'Imperio Maya', 'Imperio Tiwanaku'], 0],
                    ],
                ],
            ],
            [
                'nombre' => 'Ciencia y Tecnología',
                'descripcion' => 'Preguntas de ciencia, tecnología y naturaleza.',
                'color' => '#059669',
                'niveles' => [
                    'Fácil' => [
                        ['¿Cuál es el planeta más cercano al Sol?', ['Mercurio', 'Venus', 'Tierra', 'Marte'], 0],
                        ['¿Qué gas respiramos principalmente para vivir?', ['Oxígeno', 'Nitrógeno', 'Dióxido de carbono', 'Hidrógeno'], 0],
                    ],
                    'Difícil' => [
                        ['¿Quién propuso la teoría de la relatividad?', ['Albert Einstein', 'Isaac Newton', 'Galileo Galilei', 'Nikola Tesla'], 0],
                        ['¿Cuál es la unidad básica de la herencia genética?', ['Gen', 'Átomo', 'Célula', 'Cromosoma'], 0],
                    ],
                ],
            ],
        ];

        $now = now();

        foreach ($categorias as $ordenCategoria => $categoria) {
            $categoriaId = DB::table('trivia_categorias')->where('nombre', $categoria['nombre'])->value('id');

            if (! $categoriaId) {
                $categoriaId = DB::table('trivia_categorias')->insertGetId([
                    'nombre' => $categoria['nombre'],
                    'slug' => Str::slug($categoria['nombre']),
                    'descripcion' => $categoria['descripcion'],
                    'color' => $categoria['color'],
                    'orden' => $ordenCategoria,
                    'activo' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $ordenNivel = 0;
            foreach ($categoria['niveles'] as $nombreNivel => $preguntas) {
                $puntajeBase = $nombreNivel === 'Fácil' ? 100 : 200;

                $nivelId = DB::table('trivia_niveles')
                    ->where('categoria_id', $categoriaId)
                    ->where('nombre', $nombreNivel)
                    ->value('id');

                if (! $nivelId) {
                    $nivelId = DB::table('trivia_niveles')->insertGetId([
                        'categoria_id' => $categoriaId,
                        'nombre' => $nombreNivel,
                        'orden' => $ordenNivel,
                        'puntaje_base' => $puntajeBase,
                        'activo' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($preguntas as [$enunciado, $opciones, $indiceCorrecta]) {
                    $yaExiste = DB::table('trivia_preguntas')->where('enunciado', $enunciado)->exists();
                    if ($yaExiste) {
                        continue;
                    }

                    $preguntaId = DB::table('trivia_preguntas')->insertGetId([
                        'categoria_id' => $categoriaId,
                        'nivel_id' => $nivelId,
                        'enunciado' => $enunciado,
                        'tiempo_limite_segundos' => 20,
                        'activo' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    foreach ($opciones as $ordenOpcion => $texto) {
                        DB::table('trivia_opciones')->insert([
                            'pregunta_id' => $preguntaId,
                            'texto' => $texto,
                            'es_correcta' => $ordenOpcion === $indiceCorrecta,
                            'orden' => $ordenOpcion,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }

                $ordenNivel++;
            }
        }

        $premios = [
            ['nombre' => 'Lápiz + libreta CENEFCO', 'tipo' => 'souvenir', 'costo_puntos' => 300, 'stock' => 50],
            ['nombre' => 'Termo CENEFCO', 'tipo' => 'souvenir', 'costo_puntos' => 800, 'stock' => 20],
            ['nombre' => 'Polerón CENEFCO', 'tipo' => 'souvenir', 'costo_puntos' => 1500, 'stock' => 10],
            ['nombre' => '10% de descuento en tu próxima inscripción', 'tipo' => 'descuento', 'costo_puntos' => 1000, 'stock' => null],
            ['nombre' => '25% de descuento en tu próxima inscripción', 'tipo' => 'descuento', 'costo_puntos' => 2200, 'stock' => null],
        ];

        foreach ($premios as $orden => $premio) {
            $existe = DB::table('trivia_premios')->where('nombre', $premio['nombre'])->exists();
            if ($existe) {
                continue;
            }

            DB::table('trivia_premios')->insert([
                'nombre' => $premio['nombre'],
                'descripcion' => null,
                'tipo' => $premio['tipo'],
                'costo_puntos' => $premio['costo_puntos'],
                'stock' => $premio['stock'],
                'activo' => true,
                'orden' => $orden,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
