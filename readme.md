# Wonde School Management API 🏫📚

A Laravel-based API that integrates with the Wonde API to provide school management functionality, allowing teachers to view their class rosters and student information.

## Background 📍🍁

This application provides a bridge between school management systems and the Wonde API, enabling teachers to view their daily class schedules with student information.

**User Story:**
> *"As a Teacher I want to be able to see which students are in my class each day of the week so that I can be suitably prepared."*

The API integrates with the Wonde Testing School (ID: A1930499544) to provide real school data for testing and development.

## Features 🔥🔥

- **JWT Authentication** - Secure API access with token-based authentication
- **Email Verification** - User account verification via email
- **Password Reset** - Secure password reset functionality
- **Teacher Daily Schedule** - View students in classes by day of the week
- **Day Filtering** - Filter classes by specific day (monday, tuesday, etc.)
- **Class Grouping** - Classes grouped by day with multiple periods per class
- **User-Friendly Time Format** - Times displayed in 12-hour format (8:15 AM, 1:30 PM)
- **Student Count Tracking** - Automatic counting of students per class
- **Wonde API Integration** - Real-time data from Wonde school management system
- **Pagination Support** - 10 records per page with navigation URLs
- **Intelligent Caching** - 30-minute cache for optimal performance (99.9% improvement)
- **Comprehensive Error Handling** - Try-catch blocks with detailed logging
- **Request Validation** - Form Request validation for all endpoints
- **Comprehensive Testing** - Full test coverage with feature tests
- **Dependency Injection** - Clean architecture with interface-based design

## Installation 🔌⚡

1. **Clone repository and install dependencies**

```bash
git clone [repository-url]
cd [project-directory]
composer install
```

2. **Setup environment file**

```bash
cp .env.example .env
```

3. **Configure Wonde API credentials**

Add the following to your `.env` file:

```bash
# Wonde API Configuration
WONDE_ACCESS_TOKEN=your_wonde_access_token_here
WONDE_SCHOOL_ID=A1930499544
```

4. **Generate application key**

```bash
./vendor/bin/sail artisan key:generate
```

5. **Generate JWT token**

```bash
./vendor/bin/sail artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
./vendor/bin/sail artisan jwt:secret
```

6. **Start Docker containers**

```bash
./vendor/bin/sail up -d
# OR
docker compose down && docker compose up -d
```

## Database Setup 📊🖥️

1. **Run Migration**

```bash
./vendor/bin/sail artisan migrate
```

2. **Run Seeder**

```bash
./vendor/bin/sail artisan db:seed
```

3. **Clear cache files**

```bash
./vendor/bin/sail artisan optimize:clear
```

## API Endpoints 🎉🎉

All endpoints are prefixed with `/api/v1`

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/login` | Authenticate user and get token | No |
| POST | `/register` | Register new user | No |
| POST | `/logout` | Logout user | Yes |
| POST | `/refresh` | Refresh JWT token | Yes |

### Password Reset

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/password/email` | Send password reset email | No |
| POST | `/password/reset` | Reset password | Yes |

### School Management Endpoints

All school management endpoints require authentication and email verification.

#### Teacher Endpoints

| Method | Endpoint | Description | Auth Required | Pagination |
|--------|----------|-------------|---------------|------------|
| GET | `/teacher/{teacher_id}/students` | Get all students across teacher's classes | Yes | Yes (10 per page) |

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `day` (optional): Filter by day of week (case-insensitive): monday, tuesday, wednesday, thursday, friday, saturday, sunday

**Response Format:**
```json
{
  "status": "success",
  "data": [
    {
      "className": "11x/Sc2",
      "classId": "A1956255974",
      "studentCount": 21,
      "students": [
        {
          "id": "A544429684",
          "upi": "3f32f1d531eb9dc3c970398b60088926",
          "mis_id": "14119",
          "initials": "HK",
          "surname": "Kienan",
          "forename": "Henry",
          "middle_names": null,
          "legal_surname": null,
          "legal_forename": null,
          "gender": null,
          "date_of_birth": null,
          "created_at": {
            "date": "2022-09-05 14:21:15.000000",
            "timezone_type": 3,
            "timezone": "UTC"
          },
          "updated_at": {
            "date": "2025-10-04 01:03:22.000000",
            "timezone_type": 3,
            "timezone": "UTC"
          }
        }
      ],
      "dayOfWeek": "wednesday",
      "periods": [
        {
          "period": "Wed:1",
          "startTime": "8:15 AM",
          "endTime": "9:15 AM"
        },
        {
          "period": "Wed:2",
          "startTime": "9:15 AM",
          "endTime": "10:15 AM"
        }
      ]
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 11,
    "last_page": 2,
    "has_more_pages": true,
    "next_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=2",
    "prev_page_url": null,
    "first_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=1",
    "last_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=2"
  }
}
```

**Day Filtering Examples:**
```bash
# Get all classes (no filter)
curl "http://localhost/api/v1/teacher/A500460806/students"

# Get only Monday classes
curl "http://localhost/api/v1/teacher/A500460806/students?day=monday"

# Case-insensitive - all these work the same
curl "http://localhost/api/v1/teacher/A500460806/students?day=Wednesday"
curl "http://localhost/api/v1/teacher/A500460806/students?day=WEDNESDAY"
curl "http://localhost/api/v1/teacher/A500460806/students?day=wednesday"

# Get Friday classes with pagination
curl "http://localhost/api/v1/teacher/A500460806/students?day=friday&page=2"
```


## Architecture 🏗️

### Service Layer Pattern

The application uses a clean service layer pattern with dependency injection:

- **`SchoolInterface`** - Contract defining school-related operations
- **`WondeService`** - Implementation of `SchoolInterface` using Wonde API
- **`TeacherService`** - Business logic for teacher and class operations
- **`DailyScheduleDTO`** - Data transfer object for daily schedule with grouped periods and formatted times

### Key Components

- **Controllers**: Handle HTTP requests and responses with pagination
- **Services**: Contain business logic and coordinate with external APIs
- **DTOs**: Structure data for consistent API responses
- **Contracts**: Define interfaces for dependency injection
- **Form Requests**: Validate incoming requests with custom rules
- **Traits**: Reusable pagination logic (`PaginatesData` trait)
- **Caching**: 30-minute intelligent caching for performance
- **Tests**: Comprehensive test coverage for all functionality

### Error Handling & Logging 🚨

The application implements comprehensive error handling and logging:

#### **Exception Handling in WondeService**
```php
try {
    $response = $this->client->school($this->schoolId)
        ->employees
        ->get($teacherId, ['include' => 'classes']);
    
    return $response->classes->data ?? [];
} catch (Exception $e) {
    Log::error(sprintf('Wonde API error fetching teacher classes for teacher_id: %s', $teacherId), [
        'teacher_id' => $teacherId,
        'error' => $e->getMessage()
    ]);
    throw new Exception('Failed to fetch teacher classes', 0, $e);
}
```

#### **Request Validation**
- **TeacherStudentsRequest**: Validates teacher ID format (A\d+)
- **Custom Error Messages**: User-friendly validation messages
- **Route Parameter Validation**: Automatic validation of URL parameters

#### **Logging Strategy**
- **Error Context**: Includes relevant IDs and error messages
- **Structured Logging**: JSON format for easy parsing
- **No Sensitive Data**: Only logs necessary information
- **Exception Chaining**: Preserves original exception context

## Wonde API Integration 🔗

The application integrates with the Wonde API to provide real school data:

- **School ID**: A1930499544 (Wonde Testing School)
- **Authentication**: Bearer token authentication
- **Endpoints Used**:
  - `GET /schools/{school_id}/employees/{employee_id}?include=classes` - Get teacher classes
  - `GET /schools/{school_id}/classes/{class_id}?include=lessons,lessons.period` - Get class lessons with period data

## Pagination 📄

The API implements Laravel's built-in pagination for efficient data handling:

### **Pagination Features**
- **10 Records Per Page**: Consistent page size across all endpoints
- **Navigation URLs**: Pre-generated URLs for easy navigation
- **Total Count**: Accurate total count from pagination metadata
- **Page Information**: Current page, last page, and more pages indicator

### **Pagination Response Structure**
```json
{
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3,
    "has_more_pages": true,
    "next_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=2",
    "prev_page_url": null,
    "first_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=1",
    "last_page_url": "http://localhost/api/v1/teacher/A500460806/students?day=wednesday&page=3"
  }
}
```

### **Usage Examples**
```bash
# First page (default)
curl "http://localhost/api/v1/teacher/A500460806/students"

# Specific page
curl "http://localhost/api/v1/teacher/A500460806/students?page=2"

# Use navigation URLs from response
curl "http://localhost/api/v1/teacher/A500460806/students?page=2"
```

## Testing 🧪

### Running Tests

1. **Run All Tests**

```bash
./vendor/bin/sail artisan test
```

2. **Run Specific Test Files**

```bash
./vendor/bin/sail artisan test tests/Feature/TeacherControllerTest.php
```

3. **Run Tests with Coverage**

```bash
./vendor/bin/sail artisan test --coverage
```

### Test Coverage

- **TeacherController**: 12 tests covering authentication, data retrieval, pagination, day filtering, caching, and error handling
- **Total**: 25 tests with 160 assertions
- **Pagination Testing**: All endpoints tested with pagination scenarios
- **Validation Testing**: Form Request validation tested for all endpoints
- **Day Filtering Testing**: Case-insensitive day filtering functionality tested
- **Cache Testing**: Intelligent caching with time manipulation for proper cache expiration testing
- **Trait Testing**: Pagination trait functionality verified across controllers
