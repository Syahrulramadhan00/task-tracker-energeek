<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Me-reset database setiap kali test dijalankan

    public function test_user_can_login_with_correct_credentials()
    {
        // 1. Persiapan Data (Arrange)
        $user = User::factory()->create([
            'email' => 'admin@energeek.id',
            'password' => Hash::make('password123'),
        ]);

        // 2. Eksekusi (Act)
        $response = $this->postJson('/api/login', [
            'email' => 'admin@energeek.id',
            'password' => 'password123',
        ]);

        // 3. Validasi (Assert)
        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'user']);
    }

    public function test_login_requires_valid_email_format()
    {
        // Test untuk validasi format email (Sesuai syarat brief No. 4)
        $response = $this->postJson('/api/login', [
            'email' => 'bukan-format-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422) // Error validasi
                 ->assertJsonValidationErrors(['email']);
    }
}