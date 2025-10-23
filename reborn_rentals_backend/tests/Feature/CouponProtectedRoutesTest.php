<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class CouponProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        // Asegúrate que AuthHelper::isAdmin($user) se base en este campo
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'code'            => 'BLACKFRIDAY',
            'discount_type'   => 'percent',   // 'percent' | 'fixed'
            'discount_value'  => 15,
            'max_uses'        => 100,
            'min_order_total' => 50,
            // El validador usa 'date', así que ISO8601 funciona bien
            'starts_at'       => Carbon::now()->subDay()->toISOString(),
            'expires_at'      => Carbon::now()->addDays(7)->toISOString(),
            'is_active'       => true,
        ], $overrides);
    }

    // ---------- STORE ----------

    #[Test]
    public function store_responde_401_si_no_autenticado()
    {
        $this->postJson('/api/coupon', $this->validPayload())
             ->assertStatus(401);
    }

    #[Test]
    public function store_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();

        $this->actingAs($user, 'api')
             ->postJson('/api/coupon', $this->validPayload())
             ->assertStatus(403);
    }

    #[Test]
    public function store_crea_201_si_es_admin()
    {
        $admin = $this->createAdmin();
        $payload = $this->validPayload();

        $resp = $this->actingAs($admin, 'api')
                     ->postJson('/api/coupon', $payload);

        $resp->assertStatus(201)
             ->assertJsonFragment([
                 'code' => 'BLACKFRIDAY',
                 'discount_type' => 'percent',
                 'is_active' => true,
             ]);

        $this->assertDatabaseHas('cupons', [
            'code'      => 'BLACKFRIDAY',
            'is_active' => 1,
        ]);
    }

    #[Test]
    public function store_422_si_validacion_falla()
    {
        $admin = $this->createAdmin();

        // code vacío y discount_type inválido => 422
        $bad = $this->validPayload([
            'code' => '',
            'discount_type' => 'invalid',
        ]);

        $this->actingAs($admin, 'api')
             ->postJson('/api/coupon', $bad)
             ->assertStatus(422);
    }

    // ---------- UPDATE ----------

    #[Test]
    public function update_responde_401_si_no_autenticado()
    {
        $c = Cupon::factory()->create();

        $this->putJson("/api/coupon/{$c->id}", ['code' => 'NEW'])
             ->assertStatus(401);
    }

    #[Test]
    public function update_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $c = Cupon::factory()->create(['code' => 'OLD']);

        $this->actingAs($user, 'api')
             ->putJson("/api/coupon/{$c->id}", ['code' => 'NEW'])
             ->assertStatus(403);
    }

    #[Test]
    public function update_responde_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->putJson('/api/coupon/999999', ['code' => 'NEW'])
             ->assertStatus(404);
    }

    #[Test]
    public function update_200_actualiza_y_devuelve_cupon_fresh()
    {
        $admin = $this->createAdmin();
        $c = Cupon::factory()->create([
            'code'           => 'OLD',
            'discount_type'  => 'percent',
            'discount_value' => 10,
            'is_active'      => false,
        ]);

        $resp = $this->actingAs($admin, 'api')
                     ->putJson("/api/coupon/{$c->id}", [
                         'code'           => 'NEW',
                         'discount_type'  => 'fixed',
                         'discount_value' => 25,
                         'is_active'      => true,
                     ]);

        $resp->assertStatus(200)
             ->assertJsonFragment([
                 'code' => 'NEW',
                 'discount_type' => 'fixed',
                 'is_active' => true,
             ]);

        $this->assertDatabaseHas('cupons', [
            'id'        => $c->id,
            'code'      => 'NEW',
            'is_active' => 1,
        ]);
    }

    // ---------- DESTROY ----------

    #[Test]
    public function destroy_responde_401_si_no_autenticado()
    {
        $c = Cupon::factory()->create();

        $this->deleteJson("/api/coupon/{$c->id}")
             ->assertStatus(401);
    }

    #[Test]
    public function destroy_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $c = Cupon::factory()->create();

        $this->actingAs($user, 'api')
             ->deleteJson("/api/coupon/{$c->id}")
             ->assertStatus(403);
    }

    #[Test]
    public function destroy_responde_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->deleteJson('/api/coupon/999999')
             ->assertStatus(404);
    }

    #[Test]
    public function destroy_204_elimina_si_es_admin()
    {
        $admin = $this->createAdmin();
        $c = Cupon::factory()->create();

        $this->actingAs($admin, 'api')
             ->deleteJson("/api/coupon/{$c->id}")
             ->assertStatus(204);

        $this->assertDatabaseMissing('cupons', ['id' => $c->id]);
    }
}