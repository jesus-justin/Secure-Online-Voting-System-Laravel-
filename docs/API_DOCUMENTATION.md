# API Documentation

## Secure Online Voting System API

Base URL: `/api/v1`

### Authentication

Most endpoints require authentication using Laravel Sanctum tokens.

```bash
# Get token
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

### Endpoints

#### 1. Get Active Elections

**GET** `/api/v1/elections`

Returns a list of all active elections.

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Student Council Election 2024",
            "description": "Annual student council election",
            "start_date": "2024-01-15 08:00:00",
            "end_date": "2024-01-20 18:00:00",
            "candidates_count": 4,
            "total_votes": 150
        }
    ]
}
```

#### 2. Get Election Details

**GET** `/api/v1/elections/{id}`

Returns detailed information about a specific election including candidates.

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Student Council Election 2024",
        "description": "Annual student council election",
        "start_date": "2024-01-15 08:00:00",
        "end_date": "2024-01-20 18:00:00",
        "status": "active",
        "candidates": [
            {
                "id": 1,
                "name": "John Doe",
                "position": "President",
                "bio": "Experienced leader...",
                "image_url": "/images/candidates/john.jpg"
            }
        ]
    }
}
```

#### 3. Get Election Results

**GET** `/api/v1/elections/{id}/results`

Returns election results. Only available for ended elections or admin users.

**Response:**
```json
{
    "status": "success",
    "data": {
        "election": {
            "id": 1,
            "title": "Student Council Election 2024",
            "total_votes": 250
        },
        "results": [
            {
                "candidate_id": 1,
                "name": "John Doe",
                "vote_count": 125,
                "percentage": 50.0
            },
            {
                "candidate_id": 2,
                "name": "Jane Smith",
                "vote_count": 75,
                "percentage": 30.0
            }
        ]
    }
}
```

#### 4. Get System Statistics

**GET** `/api/v1/statistics`

Returns overall system statistics.

**Response:**
```json
{
    "status": "success",
    "data": {
        "total_elections": 10,
        "active_elections": 2,
        "total_voters": 1500,
        "total_votes_cast": 3200,
        "verified_voters": 1450,
        "pending_verifications": 50
    }
}
```

#### 5. Get User's Votes (Protected)

**GET** `/api/v1/my-votes`

Requires: `auth:sanctum`

Returns the authenticated user's voting history.

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "election_id": 1,
            "election_title": "Student Council Election 2024",
            "voted_at": "2024-01-16 10:30:00",
            "verified": true
        }
    ]
}
```

### Error Responses

```json
{
    "status": "error",
    "message": "Election not found",
    "code": 404
}
```

### Rate Limiting

- Public endpoints: 60 requests per minute
- Authenticated endpoints: 120 requests per minute

### Security

- All API requests must use HTTPS in production
- Votes are encrypted using AES-256-CBC
- Device fingerprinting is used to prevent duplicate votes
- reCAPTCHA v3 validation for critical actions
