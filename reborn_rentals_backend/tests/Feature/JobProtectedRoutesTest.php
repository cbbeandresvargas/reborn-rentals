<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class JobProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // INDEX
    #[Test]
    public function index_401_si_no_autenticado()
    {
        $this->getJson('/api/jobs')->assertStatus(401);
    }

    #[Test]
    public function index_200_si_autenticado()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin, 'api')->getJson('/api/jobs')->assertStatus(200);
    }

    // STORE
    #[Test]
    public function store_401_si_no_autenticado()
    {
        $this->postJson('/api/job', [])->assertStatus(401);
    }

    #[Test]
    public function store_2xx_si_autenticado()
    {
        $admin = $this->createAdmin();
        $resp = $this->actingAs($admin, 'api')->postJson('/api/job', []);

        // Aceptar 2xx o 422 cuando la validación exige campos
        $this->assertContains(
            $resp->getStatusCode(),
            [200, 201, 202, 204, 422],
            'Esperaba 2xx o 422'
        );
    }


    // SHOW
    #[Test]
    public function show_401_si_no_autenticado()
    {
        $this->getJson('/api/job/1')->assertStatus(401);
    }

    #[Test]
    public function show_2xx_o_404_si_autenticado()
    {
        $admin = $this->createAdmin();
        $resp = $this->actingAs($admin, 'api')->getJson('/api/job/1');
        $this->assertContains($resp->getStatusCode(), [200, 404], 'Esperaba 200 o 404');
    }

    // UPDATE
    #[Test]
    public function update_401_si_no_autenticado()
    {
        $this->putJson('/api/job/1', [])->assertStatus(401);
    }

    #[Test]
    public function update_2xx_o_404_si_autenticado()
    {
        $admin = $this->createAdmin();
        $resp = $this->actingAs($admin, 'api')->putJson('/api/job/1', []);
        $this->assertContains($resp->getStatusCode(), [200, 204, 404], 'Esperaba 200/204/404');
    }

    // DESTROY
    #[Test]
    public function destroy_401_si_no_autenticado()
    {
        $this->deleteJson('/api/job/1')->assertStatus(401);
    }

    #[Test]
    public function destroy_2xx_o_404_si_autenticado()
    {
        $admin = $this->createAdmin();
        $resp = $this->actingAs($admin, 'api')->deleteJson('/api/job/1');
        $this->assertContains($resp->getStatusCode(), [200, 204, 404], 'Esperaba 200/204/404');
    }
}