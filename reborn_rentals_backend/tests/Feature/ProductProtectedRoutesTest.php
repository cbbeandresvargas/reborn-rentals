<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProductProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        // Ajustado: solo seteamos is_admin para cumplir AuthHelper::isAdmin($user)
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
            'name'        => 'Taladro Inalámbrico X',
            'description' => '18V con dos baterías',
            'price'       => 129.90,
            'image_url'   => 'https://cdn.example.com/p/taladro.jpg',
            'active'      => true,
            'category_id' => Category::factory()->create()->id,
        ], $overrides);
    }

    // ========== STORE ==========

    #[Test]
    public function store_responde_401_si_no_autenticado()
    {
        $resp = $this->postJson('/api/product', $this->validPayload());
        $resp->assertStatus(401);
    }

    #[Test]
    public function store_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();

        $this->actingAs($user, 'api')
            ->postJson('/api/product', $this->validPayload())
            ->assertStatus(403);
    }

    #[Test]
    public function store_crea_201_y_devuelve_producto_con_categoria_si_es_admin()
    {
        $admin = $this->createAdmin();
        $payload = $this->validPayload();

        $resp = $this->actingAs($admin, 'api')->postJson('/api/product', $payload);

        // En SQLite los DECIMAL vienen como strings (p.ej. "129.90")
        $formattedPrice = number_format((float)$payload['price'], 2, '.', '');

        $resp->assertStatus(201)
             ->assertJsonFragment([
                 'name'        => $payload['name'],
                 'price'       => $formattedPrice,      // <- aserción como string
                 'active'      => true,
                 'category_id' => $payload['category_id'],
             ]);

        $json = $resp->json();
        $this->assertArrayHasKey('category', $json);
        if (!is_null($json['category'])) {
            $this->assertEquals($payload['category_id'], $json['category']['id']);
            $this->assertArrayHasKey('name', $json['category']);
        }

        $this->assertDatabaseHas('products', [
            'name'        => $payload['name'],
            'category_id' => $payload['category_id'],
            'active'      => 1,
        ]);
    }

    #[Test]
    public function store_422_si_validacion_falla()
    {
        $admin = $this->createAdmin();

        $payload = $this->validPayload([
            'name'  => '',
            'price' => null,
        ]);

        $this->actingAs($admin, 'api')
            ->postJson('/api/product', $payload)
            ->assertStatus(422);
    }

    // ========== UPDATE ==========

    #[Test]
    public function update_responde_401_si_no_autenticado()
    {
        $product = Product::factory()->create();

        $this->putJson("/api/product/{$product->id}", ['name' => 'Nuevo'])
             ->assertStatus(401);
    }

    #[Test]
    public function update_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $product = Product::factory()->create(['name' => 'Viejo']);

        $this->actingAs($user, 'api')
            ->putJson("/api/product/{$product->id}", ['name' => 'Nuevo'])
            ->assertStatus(403);
    }

    #[Test]
    public function update_responde_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
            ->putJson('/api/product/999999', ['name' => 'Nuevo'])
            ->assertStatus(404);
    }

    #[Test]
    public function update_200_actualiza_y_devuelve_producto_refrescado()
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create([
            'name'        => 'Old name',
            'price'       => 50.00,
            'category_id' => Category::factory()->create()->id,
        ]);

        $resp = $this->actingAs($admin, 'api')->putJson("/api/product/{$product->id}", [
            'name'  => 'New name',
            'price' => 199.99,
            'active'=> false,
        ]);

        $resp->assertStatus(200)
             ->assertJsonFragment(['name' => 'New name', 'active' => false]);

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'New name',
            'price' => 199.99,
            'active'=> 0,
        ]);
    }

    // ========== DESTROY ==========

    #[Test]
    public function destroy_responde_401_si_no_autenticado()
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/product/{$product->id}")
             ->assertStatus(401);
    }

    #[Test]
    public function destroy_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $product = Product::factory()->create();

        $this->actingAs($user, 'api')
            ->deleteJson("/api/product/{$product->id}")
            ->assertStatus(403);
    }

    #[Test]
    public function destroy_responde_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
            ->deleteJson('/api/product/999999')
            ->assertStatus(404);
    }

    #[Test]
    public function destroy_204_elimina_si_es_admin()
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'api')
            ->deleteJson("/api/product/{$product->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}