<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Contact;

class ContactPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function crea_contacto_publico_y_retorna_201()
    {
        $payload = [
            'first_name'   => 'Fernando',
            'last_name'    => 'Almaraz',
            'phone_number' => '+59170000000',
        ];

        $resp = $this->postJson('/api/contact', $payload);

        $resp->assertStatus(201);

        // La respuesta puede ser el recurso o un mensaje. Si devuelves el recurso:
        $resp->assertJsonFragment([
            'first_name'   => 'Fernando',
            'last_name'    => 'Almaraz',
            'phone_number' => '+59170000000',
        ]);

        $this->assertDatabaseHas('contacts', [
            'first_name'   => 'Fernando',
            'last_name'    => 'Almaraz',
            'phone_number' => '+59170000000',
        ]);
    }

    #[Test]
    public function retorna_422_si_faltan_campos_obligatorios()
    {
        // vacío
        $this->postJson('/api/contact', [])->assertStatus(422);

        // faltan campos (solo uno presente)
        $this->postJson('/api/contact', ['first_name' => 'Fer'])->assertStatus(422);
    }

    #[Test]
    public function ignora_campos_extras_no_permitidos()
    {
        $resp = $this->postJson('/api/contact', [
            'first_name'   => 'Ana',
            'last_name'    => 'García',
            'phone_number' => '+59171111111',
            'email'        => 'extra@ignored.com',   // extra
            'message'      => 'no usado',            // extra
            'role'         => 'admin-no-permitido',  // extra
        ]);

        $resp->assertStatus(201);

        $this->assertDatabaseHas('contacts', [
            'first_name'   => 'Ana',
            'last_name'    => 'García',
            'phone_number' => '+59171111111',
        ]);

        $contact = Contact::firstWhere('first_name', 'Ana');
        $this->assertNotNull($contact);
        // No deben existir en el array del modelo
        $this->assertArrayNotHasKey('email', $contact->toArray());
        $this->assertArrayNotHasKey('message', $contact->toArray());
        $this->assertArrayNotHasKey('role', $contact->toArray());
    }
}