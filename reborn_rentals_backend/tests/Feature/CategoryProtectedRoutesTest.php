<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CategoryProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
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

    #[Test]
    public function store_responde_401_si_no_autenticado()
    {
        $this->postJson('/api/categories', ['name' => 'Electro'])
             ->assertStatus(401);
    }

    #[Test]
    public function store_responde_403_si_no_es_admin()
    {
        $user = $this->createUser();

        $this->actingAs($user, 'api')
             ->postJson('/api/categories', ['name' => 'Electro'])
             ->assertStatus(403);
    }

    #[Test]
    public function store_crea_categoria_201_si_es_admin()
    {
        $admin = $this->createAdmin();

        $payload = ['name' => 'Electrónica', 'description' => 'Gadgets'];

        $this->actingAs($admin, 'api')
             ->postJson('/api/categories', $payload)
             ->assertStatus(201)
             ->assertJsonFragment(['name' => 'Electrónica']);

        $this->assertDatabaseHas('categories', ['name' => 'Electrónica']);
    }

    #[Test]
    public function store_retorna_422_si_falla_validacion()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->postJson('/api/categories', ['name' => ''])
             ->assertStatus(422);
    }

    #[Test]
    public function update_retorna_401_si_no_autenticado()
    {
        $cat = Category::factory()->create(['name' => 'Vieja']);

        $this->putJson("/api/category/{$cat->id}", ['name' => 'Nueva'])
             ->assertStatus(401);
    }

    #[Test]
    public function update_retorna_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $cat = Category::factory()->create(['name' => 'Vieja']);

        $this->actingAs($user, 'api')
             ->putJson("/api/category/{$cat->id}", ['name' => 'Nueva'])
             ->assertStatus(403);
    }

    #[Test]
    public function update_retorna_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->putJson('/api/category/999999', ['name' => 'Nueva'])
             ->assertStatus(404);
    }

    #[Test]
    public function update_actualiza_200_si_es_admin()
    {
        $admin = $this->createAdmin();
        $cat = Category::factory()->create(['name' => 'Vieja']);

        $this->actingAs($admin, 'api')
             ->putJson("/api/category/{$cat->id}", [
                 'name' => 'Nueva',
                 'description' => 'Desc actualizada',
             ])
             ->assertStatus(200)
             ->assertJsonFragment(['name' => 'Nueva']);

        $this->assertDatabaseHas('categories', ['id' => $cat->id, 'name' => 'Nueva']);
    }

    #[Test]
    public function destroy_retorna_401_si_no_autenticado()
    {
        $cat = Category::factory()->create();

        $this->deleteJson("/api/category/{$cat->id}")
             ->assertStatus(401);
    }

    #[Test]
    public function destroy_retorna_403_si_no_es_admin()
    {
        $user = $this->createUser();
        $cat = Category::factory()->create();

        $this->actingAs($user, 'api')
             ->deleteJson("/api/category/{$cat->id}")
             ->assertStatus(403);
    }

    #[Test]
    public function destroy_retorna_404_si_no_existe()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->deleteJson('/api/category/999999')
             ->assertStatus(404);
    }

    #[Test]
    public function destroy_elimina_y_responde_200_con_mensaje_si_es_admin()
    {
        $admin = $this->createAdmin();
        $cat = Category::factory()->create();

        $this->actingAs($admin, 'api')
             ->deleteJson("/api/category/{$cat->id}")
             ->assertStatus(200)
             ->assertJsonFragment(['message' => 'Categoría eliminada correctamente.']);

        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }
}