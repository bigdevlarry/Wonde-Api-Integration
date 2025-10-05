<?php

namespace Tests\Feature;

use App\Contracts\SchoolInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        $this->token = JWTAuth::fromUser($user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itReturnsTeacherStudentsWhenAuthenticated(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockClasses = [
           (object) [
               'id' => 'A1728594835',
               'mis_id' => '16763',
               'name' => '10A/As1',
               'code' => null,
               'description' => '10A/As1',
               'subject' => 'A1136470591',
               'alternative' => null,
               'priority' => null,
               'academic_year' => null,
               'year_group' => null,
               'restored_at' => null,
               'created_at' => (object) [
                   'date' => '2025-09-05 17:31:03.000000',
                   'timezone_type' => 3,
                   'timezone' => 'UTC'
               ],
               'updated_at' => (object) [
                   'date' => '2025-09-06 23:03:59.000000',
                   'timezone_type' => 3,
                   'timezone' => 'UTC'
               ],
               'meta' => null
           ],
           (object) [
               'id' => 'A2097997610',
               'mis_id' => '16730',
               'name' => '10A/Ci',
               'code' => null,
               'description' => '10A/Ci',
               'subject' => 'A1738609093',
               'alternative' => null,
               'priority' => null,
               'academic_year' => null,
               'year_group' => null,
               'restored_at' => null,
               'created_at' => (object) [
                   'date' => '2025-09-05 17:31:03.000000',
                   'timezone_type' => 3,
                   'timezone' => 'UTC'
               ],
               'updated_at' => (object) [
                   'date' => '2025-09-06 23:04:07.000000',
                   'timezone_type' => 3,
                   'timezone' => 'UTC'
               ],
               'meta' => null
           ],
        ];
        
        $mockStudents1 = [
            (object) [
                'id' => 'A1329183376',
                'upi' => '5cc4ab015f8f7870b581e30e0ef474fa',
                'mis_id' => '26',
                'title' => 'Mrs',
                'initials' => 'SS',
                'surname' => 'Smith',
                'forename' => 'Sally',
                'middle_names' => null,
                'legal_surname' => 'Smith',
                'legal_forename' => 'Sally',
                'gender' => 'female',
                'date_of_birth' => (object) [
                    'date' => '1963-02-11 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/London'
                ]
            ],
            (object) [
                'id' => 'A1161584171',
                'upi' => 'e5164e2f29965c14f1c68e241a116551',
                'mis_id' => '6903',
                'title' => 'Mrs',
                'initials' => 'KA',
                'surname' => 'Anderson',
                'forename' => 'Kate',
                'middle_names' => 'Victoria',
                'legal_surname' => 'Anderson',
                'legal_forename' => 'Kate',
                'gender' => 'female',
                'date_of_birth' => (object) [
                    'date' => '1966-03-11 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/London'
                ]
            ],
        ];
        
        $mockStudents2 = [
            (object) [
                'id' => 'A9999999999',
                'upi' => '99999999999999999999999999999999',
                'mis_id' => '9999',
                'title' => 'Mr',
                'initials' => 'BJ',
                'surname' => 'Johnson',
                'forename' => 'Bob',
                'middle_names' => null,
                'legal_surname' => 'Johnson',
                'legal_forename' => 'Bob',
                'gender' => 'male',
                'date_of_birth' => (object) [
                    'date' => '1990-05-15 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/London'
                ]
            ],
        ];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->andReturns($mockClasses);
        
        $mockSchool->expects('getClassStudents')
           ->with('A1728594835')
           ->andReturns($mockStudents1);

       $mockSchool->expects('getClassStudents')
           ->with('A2097997610')
           ->andReturns($mockStudents2);

        $mockSchool->expects('getClassLessons')
           ->with('A1728594835')
           ->andReturns([]);

        $mockSchool->expects('getClassLessons')
           ->with('A2097997610')
           ->andReturns([]);

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'className',
                        'students',
                        'studentCount',
                    ],
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'has_more_pages',
                    'next_page_url',
                    'prev_page_url',
                    'first_page_url',
                    'last_page_url',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 2,
                    'last_page' => 1,
                    'has_more_pages' => false,
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'className',
                        'students' => [
                            '*' => [
                                'id',
                                'upi',
                                'mis_id',
                                'title',
                                'initials',
                                'surname',
                                'forename',
                                'middle_names',
                                'legal_surname',
                                'legal_forename',
                                'gender',
                                'date_of_birth',
                            ],
                        ],
                        'studentCount',
                    ],
                ],
            ]);
    }

    #[Test]
    public function itReturnsUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->allows('getTeacherClasses')->andReturns([]);
        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->getJson('/api/v1/teacher/A500460806/students');

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function itReturnsTeacherStudentsWhenUnverified(): void
    {
        // Arrange
        $unverifiedUser = User::factory()->unverified()->create();
        $unverifiedToken = JWTAuth::fromUser($unverifiedUser);

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with('A500460806')
            ->andReturns([]);
        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $unverifiedToken")
            ->getJson('/api/v1/teacher/A500460806/students');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                    'has_more_pages' => false,
                ],
            ]);
    }

    #[Test]
    public function itReturnsEmptyDataWhenTeacherHasNoClasses(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockClasses = [];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->andReturns($mockClasses);

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                    'has_more_pages' => false,
                ],
            ]);
    }

    #[Test]
    public function itReturnsEmptyStudentsWhenClassHasNoStudents(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockClasses = [
                (object) [
                   'id' => 'A1728594835',
                   'mis_id' => '16763',
                   'name' => '10A/As1',
                   'code' => null,
                   'description' => '10A/As1',
                   'subject' => 'A1136470591',
                   'alternative' => null,
                   'priority' => null,
                   'academic_year' => null,
                   'year_group' => null,
                   'restored_at' => null,
                   'created_at' => (object) [
                       'date' => '2025-09-05 17:31:03.000000',
                       'timezone_type' => 3,
                       'timezone' => 'UTC'
                   ],
                   'updated_at' => (object) [
                       'date' => '2025-09-06 23:03:59.000000',
                       'timezone_type' => 3,
                       'timezone' => 'UTC'
                   ],
                   'meta' => null
               ],
           ];
        $mockStudents = [];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->andReturns($mockClasses);
        
       $mockSchool->expects('getClassStudents')
           ->with('A1728594835')
           ->andReturns($mockStudents);

        $mockSchool->expects('getClassLessons')
           ->with('A1728594835')
           ->andReturns([]);

       $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
               'status' => 'success',
               'data' => [
                   [
                       'className' => '10A/As1',
                       'students' => [],
                       'studentCount' => 0,
                   ],
               ],
           ]);
    }

    #[Test]
    public function itHandlesServiceExceptionGracefully(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->andThrow(new \Exception('Service unavailable'));

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Assert
        $response->assertStatus(500);
    }

    #[Test]
    public function itReturnsUnauthorizedWithInvalidToken(): void
    {
        // Arrange
        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->allows('getTeacherClasses')->andReturns([]);
        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/v1/teacher/A500460806/students');

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function itReturnsUnauthorizedWithMalformedToken(): void
    {
        // Arrange
        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->allows('getTeacherClasses')->andReturns([]);
        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer malformed.token.here')
            ->getJson('/api/v1/teacher/A500460806/students');

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function itReturnsUnauthorizedWithMissingAuthorizationHeader(): void
    {
        // Arrange
        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->allows('getTeacherClasses')->andReturns([]);
        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->getJson('/api/v1/teacher/A500460806/students');

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function itReturnsFilteredStudentsByDay(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $day = 'monday';
        $mockClasses = [
            (object) [
                'id' => 'A1728594835',
                'name' => '10A/As1',
            ]
        ];

        $mockStudents = [
            (object) [
                'id' => 'A1329183376',
                'upi' => '5cc4ab015f8f7870b581e30e0ef474fa',
                'mis_id' => '26',
                'title' => 'Mrs',
                'initials' => 'SS',
                'surname' => 'Smith',
                'forename' => 'Sally',
                'middle_names' => null,
                'legal_surname' => 'Smith',
                'legal_forename' => 'Sally',
                'gender' => 'female',
                'date_of_birth' => (object) [
                    'date' => '1963-02-11 00:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/London'
                ],
            ]
        ];

        $mockLessons = [
            (object) [
                'start_at' => (object) [
                    'date' => '09:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'UTC'
                ],
                'end_at' => (object) [
                    'date' => '10:00:00.000000',
                    'timezone_type' => 3,
                    'timezone' => 'UTC'
                ],
                'period' => (object) [
                    'data' => (object) [
                        'name' => 'Mon:1',
                        'day' => 'monday'
                    ]
                ]
            ]
        ];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->andReturns($mockClasses);
        
        $mockSchool->expects('getClassStudents')
           ->with('A1728594835')
           ->andReturns($mockStudents);

        $mockSchool->expects('getClassLessons')
           ->with('A1728594835')
           ->andReturns($mockLessons);

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students?day={$day}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'className',
                        'classId',
                        'studentCount',
                        'students',
                        'dayOfWeek',
                        'periods' => [
                            '*' => [
                                'period',
                                'startTime',
                                'endTime',
                            ],
                        ],
                    ],
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'has_more_pages',
                    'next_page_url',
                    'prev_page_url',
                    'first_page_url',
                    'last_page_url',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'className' => '10A/As1',
                        'classId' => 'A1728594835',
                        'studentCount' => 1,
                        'dayOfWeek' => 'monday',
                        'periods' => [
                            [
                                'period' => 'Mon:1',
                                'startTime' => '9:00 AM',
                                'endTime' => '10:00 AM',
                            ]
                        ],
                    ]
                ]
            ]);
    }

    #[Test]
    public function itCachesTeacherScheduleData(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockClasses = [
            (object) [
                'id' => 'A1728594835',
                'name' => '10A/As1',
            ]
        ];

        $mockStudents = [
            (object) [
                'id' => 'A1329183376',
                'upi' => '5cc4ab015f8f7870b581e30e0ef474fa',
                'mis_id' => '26',
                'title' => 'Mrs',
                'initials' => 'SS',
                'surname' => 'Smith',
                'forename' => 'Sally',
            ]
        ];

        $mockLessons = [
            (object) [
                'start_at' => (object) ['date' => '09:00:00.000000'],
                'end_at' => (object) ['date' => '10:00:00.000000'],
                'period' => (object) [
                    'data' => (object) [
                        'name' => 'Mon:1',
                        'day' => 'monday'
                    ]
                ]
            ]
        ];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->once() // Should only be called once due to caching
            ->andReturns($mockClasses);
        $mockSchool->expects('getClassStudents')
            ->with('A1728594835')
            ->once() // Should only be called once due to caching
            ->andReturns($mockStudents);
        $mockSchool->expects('getClassLessons')
            ->with('A1728594835')
            ->once() // Should only be called once due to caching
            ->andReturns($mockLessons);

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act - First call (should build cache)
        $response1 = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students?day=monday");

        // Act - Second call (should use cache)
        $response2 = $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students?day=monday");

        // Assert - Both responses should be identical
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        $this->assertEquals($response1->json(), $response2->json());
    }

    #[Test]
    public function itExpiresCacheAfterTimePasses(): void
    {
        // Arrange
        $teacherId = 'A500460806';
        $mockClasses = [(object) ['id' => 'A1728594835', 'name' => '10A/As1']];
        $mockStudents = [(object) ['id' => 'A1329183376', 'name' => 'Test Student']];
        $mockLessons = [];

        $mockSchool = Mockery::mock(SchoolInterface::class);
        $mockSchool->expects('getTeacherClasses')
            ->with($teacherId)
            ->twice() // Should be called twice - once for cache, once after expiration
            ->andReturns($mockClasses);
        $mockSchool->expects('getClassStudents')
            ->with('A1728594835')
            ->twice() // Should be called twice - once for cache, once after expiration
            ->andReturns($mockStudents);
        $mockSchool->expects('getClassLessons')
            ->with('A1728594835')
            ->twice() // Should be called twice - once for cache, once after expiration
            ->andReturns($mockLessons);

        $this->app->instance(SchoolInterface::class, $mockSchool);

        // Act - First call (builds cache)
        $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Fast forward time by 31 minutes (past cache expiration)
        $this->travel(31)->minutes();

        // Act - Second call (should rebuild cache)
        $this->withHeader('Authorization', "Bearer $this->token")
            ->getJson("/api/v1/teacher/{$teacherId}/students");

        // Assert - Mock expectations verify that methods were called twice
        $this->assertTrue(true); // If we get here, the test passed
    }
}
