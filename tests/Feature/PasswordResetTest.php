<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itResetsPasswordWhenTokenIsValid(): void
    {
       // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password/reset', [
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => $token,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset successfully',
            ]);
    }

    #[Test]
    public function itReturnsErrorWhenTokenIsInvalid(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password/reset', [
            'email' => 'test@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => 'invalidtoken',
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Failed to reset password',
            ]);
    }

    #[Test]
    public function itReturnsValidationErrorWhenPasswordIsMissing(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password/reset', [
            'email' => 'test@example.com',
            'token' => 'validtoken',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => ['The password field is required.'],
                ]
            ]);
    }
}

