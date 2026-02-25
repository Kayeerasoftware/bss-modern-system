# BSS System Testing Guide

## Test Structure

```
tests/
├── Browser/          # Browser/Dusk tests
├── Feature/          # Feature tests
│   ├── Admin/
│   ├── Api/
│   ├── Auth/
│   ├── Cashier/
│   ├── CEO/
│   ├── Member/
│   ├── Shareholder/
│   └── TD/
├── Unit/             # Unit tests
│   ├── Models/
│   ├── Services/
│   └── Traits/
└── TestCase.php      # Base test case
```

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Specific Test File
```bash
php artisan test tests/Feature/Auth/LoginTest.php
```

### With Coverage
```bash
php artisan test --coverage
php artisan test --coverage-html coverage
```

### Parallel Testing
```bash
php artisan test --parallel
```

## Writing Tests

### Feature Test Example
```php
<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
```

### Unit Test Example
```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Financial\LoanCalculator;

class LoanCalculatorTest extends TestCase
{
    public function test_calculates_monthly_payment_correctly()
    {
        $calculator = new LoanCalculator();
        
        $payment = $calculator->calculateMonthlyPayment(
            principal: 1000000,
            interestRate: 5.5,
            months: 12
        );

        $this->assertEqualsWithDelta(85610, $payment, 10);
    }
}
```

## Test Database

Tests use SQLite in-memory database by default (configured in `phpunit.xml`):

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Factories

### Using Factories
```php
use App\Models\Member;

// Create single member
$member = Member::factory()->create();

// Create multiple members
$members = Member::factory()->count(10)->create();

// Create with specific attributes
$member = Member::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

## Assertions

### Common Assertions
```php
// HTTP Status
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertNotFound();

// JSON
$response->assertJson(['key' => 'value']);
$response->assertJsonStructure(['data' => ['id', 'name']]);

// Database
$this->assertDatabaseHas('members', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('members', ['email' => 'deleted@example.com']);

// Authentication
$this->assertAuthenticated();
$this->assertGuest();
```

## Mocking

### Mocking Services
```php
use App\Services\Notification\SmsService;
use Mockery;

public function test_sends_sms_notification()
{
    $mock = Mockery::mock(SmsService::class);
    $mock->shouldReceive('send')
        ->once()
        ->with('0700000000', 'Test message')
        ->andReturn(true);

    $this->app->instance(SmsService::class, $mock);

    // Test code that uses SmsService
}
```

## Test Coverage Goals

- Overall: > 80%
- Controllers: > 70%
- Services: > 90%
- Models: > 85%

## Continuous Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

## Best Practices

1. **Use RefreshDatabase** trait for database tests
2. **Keep tests isolated** - each test should be independent
3. **Test one thing** per test method
4. **Use descriptive names** - `test_admin_can_create_member`
5. **Arrange-Act-Assert** pattern
6. **Mock external services** (SMS, Payment gateways)
7. **Test edge cases** and error conditions
8. **Keep tests fast** - use in-memory database

## Common Test Scenarios

### Authentication Tests
- Login with valid credentials
- Login with invalid credentials
- Logout
- Password reset
- Registration

### Authorization Tests
- Admin can access admin routes
- Cashier cannot access admin routes
- Member can only access own data

### CRUD Tests
- Create resource
- Read resource
- Update resource
- Delete resource
- Validation errors

### Business Logic Tests
- Loan calculations
- Interest calculations
- Transaction processing
- Report generation

## Debugging Tests

### Run with verbose output
```bash
php artisan test --verbose
```

### Stop on failure
```bash
php artisan test --stop-on-failure
```

### Filter tests
```bash
php artisan test --filter=LoginTest
```

### Debug with dd()
```php
public function test_example()
{
    $response = $this->get('/api/members');
    dd($response->json()); // Dump and die
}
```
