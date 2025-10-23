<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class CouponPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function lista_cupones_paginados()
    {
        Cupon::factory()->count(20)->create();

        $resp = $this->getJson('/api/coupons');
        $resp->assertStatus(200)
             ->assertJsonStructure([
                 'current_page',
                 'data' => [
                     '*' => [
                         'id','code','discount_type','discount_value','max_uses',
                         'min_order_total','starts_at','expires_at','is_active',
                         'created_at','updated_at'
                     ]
                 ],
                 'first_page_url','from','last_page','last_page_url',
                 'links','next_page_url','path','per_page','prev_page_url','to','total'
             ]);

        // paginate(15) por defecto
        $this->assertCount(15, $resp->json('data'));
    }

    #[Test]
    public function filtra_por_active_true_y_false()
    {
        // Requiere usar $request->has('active') en el controlador
        Cupon::factory()->create(['code' => 'ON',  'is_active' => true]);
        Cupon::factory()->create(['code' => 'OFF', 'is_active' => false]);

        $respTrue = $this->getJson('/api/coupons?active=1');
        $respTrue->assertStatus(200);
        $codesTrue = collect($respTrue->json('data'))->pluck('code')->all();
        $this->assertContains('ON', $codesTrue);
        $this->assertNotContains('OFF', $codesTrue);

        $respFalse = $this->getJson('/api/coupons?active=0');
        $respFalse->assertStatus(200);
        $codesFalse = collect($respFalse->json('data'))->pluck('code')->all();
        $this->assertContains('OFF', $codesFalse);
        $this->assertNotContains('ON', $codesFalse);
    }

    #[Test]
    public function filtra_por_code_exact()
    {
        Cupon::factory()->create(['code' => 'BIENVENIDO10']);
        Cupon::factory()->create(['code' => 'OTRO']);

        $resp = $this->getJson('/api/coupons?code=BIENVENIDO10');
        $resp->assertStatus(200);
        $codes = collect($resp->json('data'))->pluck('code')->all();
        $this->assertContains('BIENVENIDO10', $codes);
        $this->assertNotContains('OTRO', $codes);
    }

    #[Test]
    public function filtra_por_valid_now_solo_dentro_de_rango_y_activos()
    {
        $now = Carbon::now();

        // Válido ahora: activo y rango cubre "now"
        Cupon::factory()->create([
            'code'       => 'VAL',
            'is_active'  => true,
            'starts_at'  => $now->copy()->subDay(),
            'expires_at' => $now->copy()->addDay(),
        ]);

        // Aún no inicia
        Cupon::factory()->create([
            'code'       => 'FUT',
            'is_active'  => true,
            'starts_at'  => $now->copy()->addDay(),
            'expires_at' => $now->copy()->addDays(2),
        ]);

        // Expirado
        Cupon::factory()->create([
            'code'       => 'EXP',
            'is_active'  => true,
            'starts_at'  => $now->copy()->subDays(3),
            'expires_at' => $now->copy()->subDay(),
        ]);

        // Inactivo aunque fechas válidas
        Cupon::factory()->create([
            'code'       => 'INACT',
            'is_active'  => false,
            'starts_at'  => $now->copy()->subDay(),
            'expires_at' => $now->copy()->addDay(),
        ]);

        $resp = $this->getJson('/api/coupons?valid_now=1');
        $resp->assertStatus(200);
        $codes = collect($resp->json('data'))->pluck('code')->all();

        $this->assertContains('VAL', $codes);
        $this->assertNotContains('FUT', $codes);
        $this->assertNotContains('EXP', $codes);
        $this->assertNotContains('INACT', $codes);
    }

    #[Test]
    public function muestra_cupon_por_id()
    {
        $c = Cupon::factory()->create(['code' => 'TESTCODE', 'is_active' => true]);

        $resp = $this->getJson('/api/coupon/'.$c->id);
        $resp->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $c->id,
                 'code' => 'TESTCODE',
                 'is_active' => true,
             ]);
    }

    #[Test]
    public function show_devuelve_404_si_no_existe()
    {
        $resp = $this->getJson('/api/coupon/999999');
        $resp->assertStatus(404)
             ->assertJsonFragment(['message' => 'Cupón no encontrado']);
    }
}