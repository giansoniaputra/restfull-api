<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => "Gian",
            'last_name' => "Sonia",
            'email' => "giansoniaputra@gmail.com",
            'phone' => "08321634181",
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => "Gian",
                    'last_name' => "Sonia",
                    'email' => "giansoniaputra@gmail.com",
                    'phone' => "08321634181",
                ]
            ]);
    }
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => "",
            'last_name' => "Sonia",
            'email' => "giansoniaputra",
            'phone' => "08321634181",
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'Nama depan wajib diisi.'
                    ],
                    'email' => [
                        'Format email tidak valid.'
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorize()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => "test",
                    'last_name' => "test",
                    'email' => "test@gmail.com",
                    'phone' => "12345",
                ]
            ]);
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test',
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => "Not Found"
                ]
            ]);
    }
    public function testOtherUserContact()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test2',
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => "Not Found"
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => "test2",
            'last_name' => "test2",
            'email' => "test2@gmail.com",
            'phone' => "123456",
        ], [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => "test2",
                    'last_name' => "test2",
                    'email' => "test2@gmail.com",
                    'phone' => "123456",
                ]
            ]);
    }
    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => "",
            'last_name' => "test2",
            'email' => "test2@gmail.com",
            'phone' => "123456",
        ], [
            'Authorization' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'Nama depan wajib diisi.'
                    ]
                ]
            ]);
    }
}
