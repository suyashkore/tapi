# JWT Authentication Flow Documentation

## Endpoint: /tapi/v1/userauth/withloginid

## Flow Diagram:

```text
Client
  |
  | HTTP POST /tapi/v1/userauth/withloginid
  |
  V
Router
  |
  | routes/api.php -> AuthRoutes.php
  |
  V
AuthController
  |
  | authByLoginId(AuthByLoginIdRequest $request)
  |
  V
AuthService
  |
  | attemptAuthByLoginId($validated)
  |
  V
UserRepository
  |
  | findByLoginIdAndTenant($login_id, $tenant_id)
  |
  V
Database
  |
  | User Record
  |
  V
AuthService
  |
  | Verify Password
  | Generate JWT Token using \Tymon\JWTAuth\Facades\JWTAuth
  |
  V
Response
  |
  | JSON Response (Token)
  |
  V
Client

```

## Detailed Explanation

### 1. Client Request:

+ The client sends an HTTP POST request to the endpoint `/tapi/v1/userauth/withloginid` with JSON data containing `tenant_id`, `login_id`, and `password`.

### 2. Router:

+ The Laravel router maps the request to the appropriate route defined in `routes/api.php`, which includes `AuthRoutes.php`.
+ The route points to the `authByLoginId` method in `AuthController`.

### 3. AuthController:

+ The `authByLoginId` method in `AuthController` is called.
+ It receives an instance of `AuthByLoginIdRequest` which validates the incoming request data.
+ If validation passes, it logs the request and forwards the validated data to `AuthService`.

### 4. AuthService:

+ The `attemptAuthByLoginId` method in `AuthService` is invoked with the validated data.
+ It logs the authentication attempt and calls `UserRepository` to find the user by `login_id` and `tenant_id`.

### 5. UserRepository:

The `findByLoginIdAndTenant` method in `UserRepository` queries the database for a user record matching the `login_id` and `tenant_id`.

### 6. Database:

The database returns the user record if found.

### 7. AuthService:

+ Back in `AuthService`, it verifies the provided password against the hashed password stored in the database using `\Illuminate\Support\Facades\Hash`.
+ If the password is valid, it generates a JWT token for the user using `\Tymon\JWTAuth\Facades\JWTAuth::fromUser($user)`.

### 8. Response:

+ The generated JWT token is included in the JSON response returned by `AuthService`.
+ `AuthController` sends the JSON response back to the client.

### 9. Client:

The client receives the JSON response containing the JWT token.

## Bearer Token Authorization Flow Documentation

### Using a Bearer Token in HTTP Headers for API Calls
When making an API call that requires authentication, a Bearer Token is sent in the HTTP headers for authorization. Below is the detailed flow of how this token is validated and how user context and privileges are checked.

**HTTP Request**

**Example Request:**

```sh
curl --location 'http://localhost:8000/tapi/v1/users' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <your_jwt_token>' \
--data '{}'
```

**HTTP Headers:**

+ Content-Type: application/json
+ Authorization: Bearer <your_jwt_token>

```text
Client
  |
  | HTTP GET /tapi/v1/users
  | Authorization: Bearer <your_jwt_token>
  |
  V
Router
  |
  | routes/api.php -> UserRoutes.php
  |
  V
Middleware
  |
  | jwt.auth
  | setUserContext
  | checkPrivileges
  |
  V
UserController
  |
  | index(Request $request)
  |
  V
Response
  |
  | JSON Response (Data)
  |
  V
Client
```
### Detailed Explanation

#### 1. Client Request:
+ The client sends an HTTP GET request to the endpoint `/tapi/v1/users` with the `Authorization` header containing the Bearer Token (`Bearer <your_jwt_token>`).

#### 2. Router:

+ The Laravel router maps the request to the appropriate route defined in `routes/api.php`, which includes `UserRoutes.php`.
+ The route points to the `index` method in `UserController`.

#### 3. Middleware:

The request passes through a series of middleware: `jwt.auth`, `setUserContext`, and `checkPrivileges`.

**Middleware Flow**

**a. jwt.auth Middleware:**

+ **Purpose:** Validates the JWT token in the `Authorization` header.
+ **Implementation:** This middleware is provided by the `tymon/jwt-auth` package.

**b. `setUserContext` Middleware:**

+ **Purpose:** Sets the user context in the request after the token is validated.
+ **Implementation:** Custom middleware to retrieve user information from the JWT token and set it in the request attributes.

**c. `checkPrivileges` Middleware:**

+ **Purpose:** Checks if the authenticated user has the required privileges to access the endpoint.
+ **Implementation:** Custom middleware to verify user privileges.

#### 4. UserController:
+ After passing through the middleware, the request reaches the `index` method in `UserController`.

#### 5. Response:

+ The `UserController` processes the request and sends back a JSON response containing the requested data.

#### 6. Client:

+ The client receives the JSON response with the requested data.
