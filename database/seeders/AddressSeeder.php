<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $user = User::where('username', 'test')->first();
        $contact = Contact::query()->limit(1)->first();
        Address::create([
            'street' => 'Citerewes',
            'city' => 'Tasikmalaya',
            'province' => 'Jawa Barat',
            'country' => 'Bungursari',
            'postal_code' => 46151,
            'contact_id' => $contact->id
        ]);
    }
}
