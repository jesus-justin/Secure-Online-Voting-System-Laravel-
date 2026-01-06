# Testing Guide

## Secure Online Voting System - Testing Documentation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL database
- PHPUnit (installed via Composer)

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/VotingTest.php
```

### Test Structure

```
tests/
├── Feature/          # Integration tests
│   ├── VotingTest.php
│   ├── AuthTest.php
│   └── AdminTest.php
└── Unit/             # Unit tests
    ├── VoteModelTest.php
    └── ElectionModelTest.php
```

### Manual Testing Checklist

#### 1. User Registration & Authentication
- [ ] User can register with valid email
- [ ] Email verification is sent
- [ ] User cannot login without verification
- [ ] Password reset works correctly
- [ ] Login with valid credentials succeeds
- [ ] Login with invalid credentials fails

#### 2. Voter Profile
- [ ] User can view their profile
- [ ] User can update profile information
- [ ] Voter ID is displayed correctly
- [ ] Profile photo upload works
- [ ] Email notification preferences save

#### 3. Voting Process
- [ ] Only verified users can access voting
- [ ] Active elections are displayed
- [ ] Election details show correctly
- [ ] Candidate information is visible
- [ ] User can select a candidate
- [ ] reCAPTCHA validation works
- [ ] Vote is successfully submitted
- [ ] User cannot vote twice in same election
- [ ] Success confirmation is displayed
- [ ] Vote is encrypted in database

#### 4. Vote Integrity
- [ ] Vote hash is generated correctly
- [ ] Vote integrity verification passes
- [ ] Tampering detection works
- [ ] Device fingerprint is recorded
- [ ] IP address is logged
- [ ] Vote timestamp is accurate

#### 5. Election Management (Admin)
- [ ] Admin can create new election
- [ ] Start/end dates are validated
- [ ] Admin can add candidates
- [ ] Candidate images upload correctly
- [ ] Election can be edited
- [ ] Elections display with correct status
- [ ] Inactive elections cannot be voted in

#### 6. Results & Statistics
- [ ] Results only visible after election ends
- [ ] Admin can view results anytime
- [ ] Vote counts are accurate
- [ ] Percentages calculate correctly
- [ ] Charts display properly
- [ ] Results cannot be manipulated

#### 7. Security Features
- [ ] CSRF protection active on all forms
- [ ] SQL injection attempts blocked
- [ ] XSS attempts sanitized
- [ ] Rate limiting prevents spam
- [ ] Session management secure
- [ ] Password hashing uses bcrypt
- [ ] Vote encryption uses AES-256

#### 8. API Endpoints
- [ ] GET /api/v1/elections returns active elections
- [ ] GET /api/v1/elections/{id} returns election details
- [ ] GET /api/v1/elections/{id}/results requires authentication
- [ ] GET /api/v1/statistics returns correct data
- [ ] API rate limiting works
- [ ] Error responses are formatted correctly

#### 9. Logging & Audit
- [ ] Vote logs are created
- [ ] Failed vote attempts logged
- [ ] Admin actions logged
- [ ] Login attempts logged
- [ ] Error logs capture exceptions

#### 10. UI/UX
- [ ] Responsive design works on mobile
- [ ] All buttons function correctly
- [ ] Forms validate input
- [ ] Error messages display clearly
- [ ] Success messages show
- [ ] Loading states appear
- [ ] Navigation works correctly

### Performance Testing

```bash
# Database query optimization
php artisan telescope:install  # For query monitoring

# Cache testing
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Security Testing

```bash
# Check for security vulnerabilities
composer audit

# Verify environment variables
php artisan config:show

# Test encryption
php artisan tinker
> encrypt('test')
> decrypt('encrypted_value')
```

### Database Testing

```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Verify database integrity
php artisan votes:verify

# Check for orphaned records
php artisan tinker
> \App\Models\Vote::whereDoesntHave('election')->count()
> \App\Models\Candidate::whereDoesntHave('election')->count()
```

### Load Testing

Use Apache Bench or similar tools:

```bash
# Test voting endpoint (adjust parameters)
ab -n 100 -c 10 http://localhost:8000/voting

# Test API endpoints
ab -n 100 -c 10 http://localhost:8000/api/v1/elections
```

### Expected Test Results

- All unit tests should pass
- No SQL injection vulnerabilities
- No XSS vulnerabilities
- Rate limiting prevents abuse
- Vote integrity 100% verified
- No orphaned database records
- Response times < 500ms for most endpoints

### Reporting Issues

When reporting bugs, include:
1. Steps to reproduce
2. Expected behavior
3. Actual behavior
4. Screenshots if applicable
5. Error logs from `storage/logs/laravel.log`
6. Browser console errors (if frontend issue)

### Continuous Integration

Add to your CI/CD pipeline:

```yaml
# .github/workflows/tests.yml example
- name: Run tests
  run: |
    php artisan test
    php artisan votes:verify
```
