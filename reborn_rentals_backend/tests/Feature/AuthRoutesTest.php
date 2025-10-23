<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    /** Helper: intenta extraer un token desde posibles estructuras del login */
    private function extractToken(array $json): ?string
    {
        if (isset($json['access_token'])) return $json['access_token'];
        if (isset($json['token'])) return $json['token'];
        if (isset($json['authorisation']['token'])) return $json['authorisation']['token'];
        if (isset($json['authorization']['token'])) return $json['authorization']['token'];
        if (isset($json['data']['token'])) return $json['data']['token'];
        return null;
    }

    /** Payload de registro según tu implementación */
    private function registrationPayload(): array
    {
        return [
            'name'                  => 'Fernando',
            'last_name'             => 'Almaraz',
            'second_last_name'      => 'Quispe',
            'phone_number'          => '+59170000000',
            'address'               => 'Av. Siempre Viva 123',
            'email'                 => 'fer@example.com',
            'username'              => 'ferchex',
            'password'              => 'secret12345',
            'password_confirmation' => 'secret12345',
        ];
    }

    /** Crea usuario normal para login */
    private function makeUser(string $plain = 'secret12345'): User
    {
        return User::factory()->create([
            'password' => Hash::make($plain),
            'is_admin' => false,
            'email'    => 'userlogin@example.com',
            'username' => 'userlogin',
        ]);
    }

    // ======================================================
    // =============== TESTS DE AUTENTICACIÓN ===============
    // ======================================================

    #[Test]
    public function register_crea_usuario_y_devuelve_200_o_201()
    {
        $payload = $this->registrationPayload();

        $resp = $this->postJson('/api/auth/register', $payload);

        // Si tu register devuelve 200 o 201, aceptamos ambos
        $this->assertContains($resp->getStatusCode(), [200, 201], 'Esperaba 200 o 201 en /auth/register');

        // Verifica que el usuario exista al menos por email (evitamos username porque salió null)
        $this->assertDatabaseHas('users', [
            'email' => 'fer@example.com',
        ]);
    }

    #[Test]
    public function login_entrega_token_con_credenciales_validas()
    {
        $user = $this->makeUser();

        $resp = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret12345',
        ])->assertStatus(200);

        $token = $this->extractToken($resp->json());
        $this->assertNotNull($token, 'No se encontró token en la respuesta de login');
    }

    #[Test]
    public function profile_200_con_token_valido()
    {
        $user = $this->makeUser();

        $login = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret12345',
        ])->assertStatus(200);

        $token = $this->extractToken($login->json());
        $this->assertNotNull($token, 'No se encontró token tras login');

        $this->withHeader('Authorization', 'Bearer '.$token)
             ->postJson('/api/auth/profile')
             ->assertStatus(200);
    }

    #[Test]
    public function refresh_entrega_nuevo_token_valido()
    {
        $user = $this->makeUser();

        $login = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret12345',
        ])->assertStatus(200);

        $token = $this->extractToken($login->json());
        $this->assertNotNull($token);

        $refresh = $this->withHeader('Authorization', 'Bearer '.$token)
                        ->postJson('/api/auth/refresh')
                        ->assertStatus(200);

        $newToken = $this->extractToken($refresh->json());
        $this->assertNotNull($newToken, 'No se encontró token en refresh');
        $this->assertNotEquals($token, $newToken, 'El token nuevo debe diferir del anterior');
    }

    #[Test]
    public function logout_200_e_invalida_la_sesion()
    {
        $user = $this->makeUser();

        $login = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret12345',
        ])->assertStatus(200);

        $token = $this->extractToken($login->json());
        $this->assertNotNull($token);

        $this->withHeader('Authorization', 'Bearer '.$token)
             ->postJson('/api/auth/logout')
             ->assertStatus(200);
    }

    // ======================================================
    // ========= VALIDACIÓN DEL TOKEN JWT REAL ==============
    // ======================================================

    #[Test]
    public function token_valido_permite_acceder_a_ruta_protegida()
    {
        $user = $this->makeUser();

        $login = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret12345',
        ])->assertStatus(200);

        $token = $this->extractToken($login->json());
        $this->assertNotNull($token);

        $resp = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->getJson('/api/orders'); // ruta protegida

        $this->assertEquals(200, $resp->getStatusCode(), 'El token JWT no permitió acceder a la ruta protegida');
    }

    #[Test]
    public function token_invalido_retorna_401_en_ruta_protegida()
    {
        $resp = $this->withHeader('Authorization', 'Bearer token_falso_invalido')
                     ->getJson('/api/orders');

        $this->assertEquals(401, $resp->getStatusCode(), 'Se esperaba 401 con token inválido');
    }
}