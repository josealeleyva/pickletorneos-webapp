<?php

namespace Tests\Feature\Jugador;

use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerfilControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Jugador $jugador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Carlos',
            'apellido' => 'García',
            'password' => Hash::make('password'),
        ]);

        $this->jugador = Jugador::factory()->create([
            'user_id' => $this->user->id,
            'nombre' => 'Carlos',
            'apellido' => 'García',
            'email' => $this->user->email,
        ]);
    }

    public function test_jugador_puede_ver_su_perfil(): void
    {
        $response = $this->actingAs($this->user)->get(route('jugador.perfil'));

        $response->assertOk();
        $response->assertViewIs('jugador.perfil');
        $response->assertViewHas('jugador');
        $response->assertViewHas('deportes');
    }

    public function test_jugador_no_autenticado_redirige_al_login(): void
    {
        $response = $this->get(route('jugador.perfil'));

        $response->assertRedirect(route('login'));
    }

    public function test_jugador_puede_actualizar_datos_personales(): void
    {
        $response = $this->actingAs($this->user)->put(route('jugador.perfil.update'), [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@example.com',
            'telefono' => '3411234567',
            'dni' => '30123456',
            'fecha_nacimiento' => '15/06/1990',
            'genero' => 'masculino',
            'deporte_principal_id' => null,
        ]);

        $response->assertRedirect(route('jugador.perfil'));
        $response->assertSessionHas('success_perfil');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@example.com',
        ]);

        $this->assertDatabaseHas('jugadores', [
            'id' => $this->jugador->id,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'dni' => '30123456',
            'fecha_nacimiento' => '1990-06-15',
            'genero' => 'masculino',
        ]);
    }

    public function test_email_duplicado_rechazado_al_actualizar(): void
    {
        $otro = User::factory()->create(['email' => 'ocupado@example.com']);

        $response = $this->actingAs($this->user)->put(route('jugador.perfil.update'), [
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'ocupado@example.com',
            'telefono' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email'], null, 'perfil');
    }

    public function test_nombre_requerido_al_actualizar(): void
    {
        $response = $this->actingAs($this->user)->put(route('jugador.perfil.update'), [
            'name' => '',
            'apellido' => 'Pérez',
            'email' => $this->user->email,
        ]);

        $response->assertSessionHasErrors(['name'], null, 'perfil');
    }

    public function test_jugador_puede_cambiar_contrasena(): void
    {
        $response = $this->actingAs($this->user)->put(route('jugador.perfil.password'), [
            'current_password' => 'password',
            'password' => 'nuevapassword123',
            'password_confirmation' => 'nuevapassword123',
        ]);

        $response->assertRedirect(route('jugador.perfil'));
        $response->assertSessionHas('success_password');
        $this->assertTrue(Hash::check('nuevapassword123', $this->user->fresh()->password));
    }

    public function test_contrasena_actual_incorrecta_rechazada(): void
    {
        $response = $this->actingAs($this->user)->put(route('jugador.perfil.password'), [
            'current_password' => 'incorrecta',
            'password' => 'nuevapassword123',
            'password_confirmation' => 'nuevapassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['current_password'], null, 'password');
        $this->assertFalse(Hash::check('nuevapassword123', $this->user->fresh()->password));
    }

    public function test_confirmacion_de_contrasena_debe_coincidir(): void
    {
        $response = $this->actingAs($this->user)->put(route('jugador.perfil.password'), [
            'current_password' => 'password',
            'password' => 'nuevapassword123',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors(['password'], null, 'password');
    }

    public function test_jugador_puede_subir_foto(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('foto.jpg', 200, 200);

        $response = $this->actingAs($this->user)->post(route('jugador.perfil.foto'), [
            'foto' => $file,
        ]);

        $response->assertRedirect(route('jugador.perfil'));
        $response->assertSessionHas('success_foto');

        $this->jugador->refresh();
        $this->assertNotNull($this->jugador->foto);
        Storage::disk('public')->assertExists($this->jugador->foto);
    }

    public function test_archivo_no_imagen_rechazado(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('documento.pdf', 100);

        $response = $this->actingAs($this->user)->post(route('jugador.perfil.foto'), [
            'foto' => $file,
        ]);

        $response->assertSessionHasErrors(['foto'], null, 'foto');
    }

    public function test_foto_anterior_se_elimina_al_subir_nueva(): void
    {
        Storage::fake('public');

        // Primer upload
        $file1 = UploadedFile::fake()->image('primera.jpg');
        $this->actingAs($this->user)->post(route('jugador.perfil.foto'), ['foto' => $file1]);
        $this->jugador->refresh();
        $fotoAnterior = $this->jugador->foto;

        // Segundo upload
        $file2 = UploadedFile::fake()->image('segunda.jpg');
        $this->actingAs($this->user)->post(route('jugador.perfil.foto'), ['foto' => $file2]);
        $this->jugador->refresh();

        Storage::disk('public')->assertMissing($fotoAnterior);
        Storage::disk('public')->assertExists($this->jugador->foto);
    }
}
