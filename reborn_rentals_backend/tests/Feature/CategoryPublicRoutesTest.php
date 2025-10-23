<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CategoryPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function lista_categorias_devuelve_404_si_no_hay_registros()
    {
        $resp = $this->getJson('/api/categories');
        $resp->assertStatus(404)
             ->assertJsonFragment(['message' => 'No existen categorías registradas aún.']);
    }

    #[Test]
    public function lista_categorias_devuelve_array_si_hay_datos()
    {
        Category::factory()->count(3)->create();

        $resp = $this->getJson('/api/categories');
        $resp->assertStatus(200);
        $this->assertIsArray($resp->json());
        $this->assertCount(3, $resp->json());
        $resp->assertJsonStructure([
            '*' => ['id','name','description','created_at','updated_at']
        ]);
    }

    #[Test]
    public function muestra_categoria_por_id()
    {
        $cat = Category::factory()->create(['name' => 'Electrónica']);

        $resp = $this->getJson('/api/category/'.$cat->id);
        $resp->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $cat->id,
                 'name' => 'Electrónica',
             ]);
    }

    #[Test]
    public function show_devuelve_404_si_no_existe()
    {
        $resp = $this->getJson('/api/category/999999');
        $resp->assertStatus(404)
             ->assertJsonFragment(['message' => 'Categoría no encontrada.']);
    }
}