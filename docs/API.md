# REST API Reference

## Base URL
Default: `HTTP://localhost:8080/api/v1`

## Authentication
Bearer Token (JWT) required for all non-login endpoints.
```http
Authorization: Bearer <your_jwt_token>
```

## Endpoints

### POST /auth/login
Authenticates a user and returns a JWT token.
- **Parameters**: `username`, `password`
- **Response**: `{ "token": "...", "expires_at": 1712345678 }`

### GET /student
Returns a paginated list of students within the authenticated branch.
- **Response**: `[ { "id": 123, "first_name": "Anvar", ... }, ... ]`

### GET /student/{id}
Returns full details for a specific student.
- **Response**: Detailed student object including `direction` and `eduForm`.

### POST /student/{id}/status
Changes a student's status.
- **Parameters**: `status` (int), `note` (string)
- **Response**: Success/Failure status.

### GET /exam
Returns available exams.
- **Response**: List of `Exam` objects with associated `ExamDate` slots.

## Error Formats
- 401: Unauthorized (Invalid token)
- 403: Forbidden (Cross-branch access attempt blocked)
- 422: Validation Error (Check `errors` field)
- 500: Server Error (Alert sent to admin)
