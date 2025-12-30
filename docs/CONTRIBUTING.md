# Contributing to Secure Online Voting System

Thank you for considering contributing to this project! Here are some guidelines to help you get started.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/jesus-justin/Secure-Online-Voting-System-Laravel-/issues)
2. If not, create a new issue using the Bug Report template
3. Include as much detail as possible:
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - Screenshots (if applicable)
   - Environment details

### Suggesting Features

1. Check if the feature has already been requested
2. Create a new issue using the Feature Request template
3. Clearly describe the feature and its benefits
4. Include mockups or examples if possible

### Pull Requests

1. **Fork the repository**
2. **Create a feature branch** from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes:**
   - Follow Laravel coding standards
   - Write meaningful commit messages
   - Add tests for new features
   - Update documentation as needed

4. **Test your changes:**
   ```bash
   php artisan test
   ```

5. **Commit your changes:**
   ```bash
   git add .
   git commit -m "Add: Brief description of your changes"
   ```

6. **Push to your fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Open a Pull Request:**
   - Use the PR template
   - Link to any related issues
   - Describe what you changed and why
   - Add screenshots for UI changes

## Coding Standards

### PHP/Laravel

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use meaningful variable and function names
- Add DocBlocks to all methods
- Keep methods focused and small
- Use type hints where possible

Example:
```php
/**
 * Cast a vote in an election
 *
 * @param Election $election
 * @param Candidate $candidate
 * @param User $user
 * @return Vote
 */
public function castVote(Election $election, Candidate $candidate, User $user): Vote
{
    // Implementation
}
```

### Blade Templates

- Use proper indentation (4 spaces)
- Keep logic minimal in views
- Use components for reusable elements
- Follow Bootstrap conventions

### Database

- Always create migrations for schema changes
- Use descriptive migration names
- Include down() method for rollbacks
- Add indexes for frequently queried columns

### Testing

- Write tests for all new features
- Aim for high code coverage
- Use descriptive test names
- Group related tests

Example:
```php
public function test_user_can_vote_in_active_election()
{
    // Arrange
    $user = User::factory()->create(['is_verified' => true]);
    $election = Election::factory()->active()->create();
    
    // Act
    $response = $this->actingAs($user)->post("/vote/{$election->id}");
    
    // Assert
    $response->assertSuccessful();
    $this->assertTrue($user->hasVotedInElection($election->id));
}
```

## Git Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Start with a capital letter
- Keep first line under 50 characters
- Add detailed description after blank line if needed

Good commit messages:
```
Add vote tampering detection system

- Implement hash verification
- Add logging for tampered votes
- Create admin alert system
```

Bad commit messages:
```
fixed stuff
update
changes
```

## Security

- **Never commit sensitive data** (passwords, keys, tokens)
- Report security vulnerabilities privately via email
- Don't create public issues for security bugs
- Follow OWASP best practices

## Code Review Process

1. All PRs require at least one review
2. Address all review comments
3. Keep PRs focused and reasonably sized
4. Be open to feedback and suggestions

## Development Workflow

1. **Setup development environment:**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

2. **Create feature branch:**
   ```bash
   git checkout -b feature/my-feature
   ```

3. **Develop and test:**
   ```bash
   # Make changes
   php artisan test
   ```

4. **Commit and push:**
   ```bash
   git add .
   git commit -m "Add: Feature description"
   git push origin feature/my-feature
   ```

5. **Open Pull Request**

## Questions?

- Open a discussion on GitHub
- Check existing documentation
- Review closed issues for similar questions

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing! 🎉
