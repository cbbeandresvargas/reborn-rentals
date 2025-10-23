<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
class ProductPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function lista_productos_paginados_en_endpoint_publico()
    {
        Product::factory()->count(20)->create();

        $resp = $this->getJson('/api/products');

        $resp->assertStatus(200)
             ->assertJsonStructure([
                 'current_page',
                 'data' => [
                     '*' => [
                         'id','name','description','price','image_url','active','category_id',
                         // 'category' puede ser null o un objeto; si usas relación opcional,
                         // no fuerces la estructura de category aquí.
                     ]
                 ],
                 'first_page_url','from','last_page','last_page_url',
                 'links','next_page_url','path','per_page','prev_page_url','to','total'
             ]);

        // Por defecto paginate(15): debe devolver 15 en la primera página
        $this->assertCount(15, $resp->json('data'));
    }

    #[Test]
    public function filtra_por_texto_q_en_nombre_o_descripcion()
    {
        Product::factory()->create(['name' => 'Taladro Inalámbrico', 'description' => '18V']);
        Product::factory()->create(['name' => 'Sierra Circular', 'description' => 'Potente y precisa']);

        // Debe encontrar solo el que coincide con 'Taladro'
        $resp = $this->getJson('/api/products?q=Taladro');
        $resp->assertStatus(200);
        $names = collect($resp->json('data'))->pluck('name')->all();

        $this->assertContains('Taladro Inalámbrico', $names);
        $this->assertNotContains('Sierra Circular', $names);
    }

    #[Test]
    public function filtra_por_category_id()
    {
        $catA = Category::factory()->create(['name' => 'Herramientas']);
        $catB = Category::factory()->create(['name' => 'Jardín']);

        Product::factory()->count(2)->create(['category_id' => $catA->id]);
        Product::factory()->count(3)->create(['category_id' => $catB->id]);

        $resp = $this->getJson('/api/products?category_id=' . $catA->id);
        $resp->assertStatus(200);

        // Todos los devueltos deben tener category_id = catA
        collect($resp->json('data'))->each(function ($p) use ($catA) {
            $this->assertEquals($catA->id, $p['category_id']);
        });
    }

    #[Test]
    public function filtra_por_active_true_y_false()
    {
        // Requiere que en el controlador uses $request->has('active') (no filled)
        Product::factory()->create(['name' => 'A1', 'active' => true]);
        Product::factory()->create(['name' => 'A2', 'active' => false]);

        // active=1 → solo activos
        $respTrue = $this->getJson('/api/products?active=1');
        $respTrue->assertStatus(200);
        $namesTrue = collect($respTrue->json('data'))->pluck('name')->all();
        $this->assertContains('A1', $namesTrue);
        $this->assertNotContains('A2', $namesTrue);

        // active=0 → solo inactivos
        $respFalse = $this->getJson('/api/products?active=0');
        $respFalse->assertStatus(200);
        $namesFalse = collect($respFalse->json('data'))->pluck('name')->all();
        $this->assertContains('A2', $namesFalse);
        $this->assertNotContains('A1', $namesFalse);
    }

    #[Test]
    public function muestra_detalle_de_producto_publico()
    {
        $cat = Category::factory()->create(['name' => 'Herramientas']);
        $product = Product::factory()->create([
            'name' => 'Taladro Pro',
            'category_id' => $cat->id,
            'active' => true,
        ]);

        $resp = $this->getJson('/api/product/' . $product->id);

        $resp->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $product->id,
                 'name' => 'Taladro Pro',
                 'category_id' => $cat->id,
                 'active' => true,
             ]);

        // Si quieres asegurar que carga la relación:
        // OJO: solo si siempre existe categoría; si es nullable, quita esta aserción.
        $json = $resp->json();
        $this->assertArrayHasKey('category', $json);
        if (!is_null($json['category'])) {
            $this->assertEquals($cat->id, $json['category']['id']);
            $this->assertEquals('Herramientas', $json['category']['name']);
        }
    }

    #[Test]
    public function retorna_404_si_el_producto_no_existe()
    {
        $resp = $this->getJson('/api/product/999999');
        $resp->assertStatus(404)
             ->assertJsonFragment(['message' => 'Producto no encontrado']);
    }
}