<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ContactProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        // Ajusta según tu AuthHelper::isAdmin($user)
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    // ========== INDEX ==========

    #[Test]
    public function index_401_si_no_autenticado()
    {
        $this->getJson('/api/contacts')->assertStatus(401);
    }

    #[Test]
    public function index_200_si_admin_autenticado()
    {
        $admin = $this->createAdmin();
        Contact::factory()->count(3)->create();

        $this->actingAs($admin, 'api')
             ->getJson('/api/contacts')
             ->assertStatus(200);
    }

    // ========== SHOW ==========

    #[Test]
    public function show_401_si_no_autenticado()
    {
        $c = Contact::factory()->create();
        $this->getJson('/api/contact/'.$c->id)->assertStatus(401);
    }

    #[Test]
    public function show_200_si_admin_autenticado()
    {
        $admin = $this->createAdmin();
        $c = Contact::factory()->create([
            'first_name'   => 'Ana',
            'last_name'    => 'García',
            'phone_number' => '+59171111111',
        ]);

        $this->actingAs($admin, 'api')
             ->getJson('/api/contact/'.$c->id)
             ->assertStatus(200);
    }

    #[Test]
    public function show_404_si_no_existe_pero_admin_autenticado()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->getJson('/api/contact/999999')
             ->assertStatus(404);
    }

    // ========== UPDATE ==========

    #[Test]
    public function update_401_si_no_autenticado()
    {
        $c = Contact::factory()->create();
        $this->putJson('/api/contact/'.$c->id, ['first_name' => 'New'])
             ->assertStatus(401);
    }

    #[Test]
    public function update_200_si_admin_y_datos_validos()
    {
        $admin = $this->createAdmin();
        $c = Contact::factory()->create([
            'first_name'   => 'Old',
            'last_name'    => 'Name',
            'phone_number' => '+59170000000',
        ]);

        $payload = [
            'first_name'   => 'New',
            'last_name'    => 'Name',
            'phone_number' => '+59172222222',
        ];

        $this->actingAs($admin, 'api')
             ->putJson('/api/contact/'.$c->id, $payload)
             ->assertStatus(200);

        $this->assertDatabaseHas('contacts', [
            'id'           => $c->id,
            'first_name'   => 'New',
            'last_name'    => 'Name',
            'phone_number' => '+59172222222',
        ]);
    }

    #[Test]
    public function update_422_si_validacion_falla_con_admin()
    {
        $admin = $this->createAdmin();
        $c = Contact::factory()->create();

        // first_name vacío para provocar 422
        $this->actingAs($admin, 'api')
             ->putJson('/api/contact/'.$c->id, [
                 'first_name'   => '',
                 'last_name'    => 'X',
                 'phone_number' => '+5917',
             ])
             ->assertStatus(422);
    }

    #[Test]
    public function update_404_si_no_existe_con_admin()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->putJson('/api/contact/999999', [
                 'first_name'   => 'New',
                 'last_name'    => 'X',
                 'phone_number' => '+5917000',
             ])
             ->assertStatus(404);
    }

    // ========== DESTROY ==========

    #[Test]
    public function destroy_401_si_no_autenticado()
    {
        $c = Contact::factory()->create();
        $this->deleteJson('/api/contact/'.$c->id)->assertStatus(401);
    }

    #[Test]
    public function destroy_200_o_204_si_admin_autenticado()
    {
        $admin = $this->createAdmin();
        $c = Contact::factory()->create();

        $resp = $this->actingAs($admin, 'api')
                     ->deleteJson('/api/contact/'.$c->id);

        // Tu controller podría devolver 200 con mensaje o 204 sin cuerpo
        $this->assertContains($resp->getStatusCode(), [200, 204], 'Esperaba 200 o 204');

        $this->assertDatabaseMissing('contacts', ['id' => $c->id]);
    }

    #[Test]
    public function destroy_404_si_no_existe_con_admin()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin, 'api')
             ->deleteJson('/api/contact/999999')
             ->assertStatus(404);
    }
}