<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Support\Str;
use Database\Seeders\UserSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
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
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => 'Bungursari',
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Citerewes',
                    'city' => 'Tasikmalaya',
                    'province' => 'Jawa Barat',
                    'country' => 'Bungursari',
                    'postal_code' => 46151,
                ]
            ]);
    }
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => "",
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['Country tidak boleh kosong']
                ]
            ]);
    }

    public function testCreateMaxFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => Str::random(201),
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => "Bungursari",
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'street' => ['Street maksimal 200 character']
                ]
            ]);
    }
    public function testContactNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post('/api/contacts/' . $contact->id + 1 . '/addresses', [
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => "Bungursari",
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test',
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Not Found'
                ]
            ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Citerewes',
                    'city' => 'Tasikmalaya',
                    'province' => 'Jawa Barat',
                    'country' => 'Bungursari',
                    'postal_code' => 46151,
                ]
            ]);
    }

    public function testGetAddressContactNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id + 1 . '/addresses/' . $address->id, [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Contact Not Found'
                ]
            ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();
        $this->get('/api/contacts/' . $contact->id . '/addresses/' . $address->id + 1, [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Address Not Found'
                ]
            ]);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();
        $this->put('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => 'Cieunteung',
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Citerewes',
                    'city' => 'Tasikmalaya',
                    'province' => 'Jawa Barat',
                    'country' => 'Cieunteung',
                    'postal_code' => 46151,
                ]
            ]);
    }

    public function testUpdateAddressVallidationError()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();
        $this->put('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => '',
            'postal_code' => 46151,
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['Country tidak boleh kosong']
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();
        $this->delete('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteContactNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $contact->id + 1 . '/addresses/' . $address->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Contact Not Found'
                ]
            ]);
    }

    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();
        $this->delete('/api/contacts/' . $contact->id . '/addresses/' . $address->id + 1, [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Address Not Found'
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        // $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id . '/addresses', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        // 'id' => $address->id,
                        'street' => 'Citerewes',
                        'city' => 'Tasikmalaya',
                        'province' => 'Jawa Barat',
                        'country' => 'Bungursari',
                        'postal_code' => '46151',
                    ]
                ]
            ]);
    }
    public function testListNotFound()
    {

        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $this->seed([AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        // $address = Address::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id + 1 . '/addresses', [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Contact Not Found'
                ]
            ]);
    }
}
