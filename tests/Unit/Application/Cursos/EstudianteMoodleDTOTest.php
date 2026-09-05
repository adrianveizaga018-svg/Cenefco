<?php

namespace Tests\Unit\Application\Cursos;

use App\Application\Cursos\DTOs\EstudianteMoodleDTO;
use Tests\TestCase;

class EstudianteMoodleDTOTest extends TestCase
{
    private function fila(string $ci = '7854321'): object
    {
        return (object) [
            'nombre_usuario' => '',
            'nombre'         => 'Juan Carlos',
            'appaterno'      => 'Pérez',
            'apmaterno'      => 'Mamani',
            'email'          => 'juan@example.com',
            'ci'             => $ci,
        ];
    }

    public function test_la_password_nunca_contiene_el_ci(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $dto = EstudianteMoodleDTO::fromRow($this->fila('7854321'));

            $this->assertStringNotContainsString('7854321', $dto->password);
            $this->assertStringNotContainsString('Cenefco.', $dto->password);
        }
    }

    public function test_la_password_siempre_cumple_la_politica_de_moodle(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $password = EstudianteMoodleDTO::fromRow($this->fila())->password;

            $this->assertGreaterThanOrEqual(8, strlen($password));
            $this->assertMatchesRegularExpression('/[A-Z]/', $password);
            $this->assertMatchesRegularExpression('/[a-z]/', $password);
            $this->assertMatchesRegularExpression('/[0-9]/', $password);
            $this->assertMatchesRegularExpression('/[^A-Za-z0-9]/', $password);
        }
    }

    public function test_dos_generaciones_seguidas_dan_passwords_distintas(): void
    {
        $p1 = EstudianteMoodleDTO::fromRow($this->fila())->password;
        $p2 = EstudianteMoodleDTO::fromRow($this->fila())->password;

        $this->assertNotSame($p1, $p2);
    }

    public function test_username_y_nombre_no_se_ven_afectados(): void
    {
        $dto = EstudianteMoodleDTO::fromRow($this->fila());

        $this->assertSame('juan', $dto->username);
        $this->assertSame('Juan Carlos', $dto->firstname);
        $this->assertSame('Pérez Mamani', $dto->lastname);
        $this->assertSame('juan@example.com', $dto->email);
    }
}
