<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itReturnsJwtTokenWhenUserLogsIn(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'user' => ['id', 'name', 'email'],
                'authorisation' => [
                    'token', 'type',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'authorisation' => [
                    'type' => 'bearer',
                ],
            ]);
    }

    #[Test]
    public function itCreatesUserWhenRegistrationIsSuccessful(): void
    {
        // Act
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id', 'name', 'email',
                ],
                'authorisation' => [
                    'token', 'type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    #[Test]
    public function itLogsOutUserSuccessfully(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/logout');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);

        $this->assertTrue(Auth::guest());
    }

    #[Test]
    public function itRefreshesTokenWhenUserIsAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/refresh');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'user' => [
                    'id', 'name', 'email', 'email_verified_at'
                ],
                'authorisation' => [
                    'token', 'type',
                ],
            ]);

        $this->assertSame('success', $response['status']);
        $this->assertSame('bearer', $response['authorisation']['type']);
    }

    #[Test]
    public function itReturnsErrorWhenCredentialsAreInvalid(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Incorrect email or password.',
            ]);
    }

    #[Test]
    public function itReturnsValidationErrorWhenEmailIsInvalid(): void
    {
        // Act
        $response = $this->postJson('/api/v1/login', [
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function itReturnsValidationErrorsWhenNoDataIsSent(): void
    {
        // Act
        $response = $this->postJson('/api/v1/login', []);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function itReturnsUnauthorizedWhenLoggingOutWithoutAuthentication(): void
    {
        // Act
        $response = $this->postJson('/api/v1/logout');

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    #[Test]
    public function itReturnsUnauthorizedWhenRefreshingTokenWithoutAuthentication(): void
    {
        // Act
        $response = $this->postJson('/api/v1/refresh');

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
