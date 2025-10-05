<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgetPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itSendsResetLinkEmailSuccessfully(): void
    {
        // Arrange
        User::factory()->create(['email' => 'user@example.com']);

        // Act
        $response = $this->postJson('/api/v1/password/email', [
            'email' => 'user@example.com',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password reset link sent',
            ]);
    }
}
