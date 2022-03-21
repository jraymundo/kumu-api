# KUMU API

## Installation

Clone git repo
```bash
clone master branch -- https://github.com/jraymundo/kumu-api.git
```

Creat .env
```bash
Copy .env.example contents and put it inside .env file -- make sure the db is configured
```
Install using composer
```bash
composer install
```

Create database schema
```bash
php artisan migrate
```


### User Registration Endpoint
1. Endpoint - https://your-local-endpoint/v1/auth/register
2. Method - POST
3. Request Payload JSON
```bash
{
  "data":{
    "type":"auth",
    "attributes":
        {
          "username":"admin2@gmail.com",
          "password":"jempogi123"
        }
  }
}
```
Response Payload
```bash
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NDc4NzcxMTcsImV4cCI6MTY0Nzg5NzkxNywidXVpZCI6Ik93RGRxTWIzQjdoSG1xZHhjTWRwIiwic3ViIjoibmlQN0h0d1lkT3FCcnJnaXZlVE0ifQ.C38o20LkR104u7ssTmoGTjHtvm1zjZx7kwbyP0bz3fw"
}
```

### User Login Endpoint
1. Endpoint - https://your-local-endpoint/v1/auth/login
2. Method - POST
3. Request Payload JSON
```bash
{
  "data":{
    "type":"auth",
    "attributes":
        {
          "username":"admin2@gmail.com",
          "password":"jempogi123"
        }
  }
}
```
Response Payload
```bash
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NDc4NzcxMTcsImV4cCI6MTY0Nzg5NzkxNywidXVpZCI6Ik93RGRxTWIzQjdoSG1xZHhjTWRwIiwic3ViIjoibmlQN0h0d1lkT3FCcnJnaXZlVE0ifQ.C38o20LkR104u7ssTmoGTjHtvm1zjZx7kwbyP0bz3fw"
}
```
### Get Github Users Endpoint
1. Endpoint - https://your-local-endpoint/v1/github/users
2. Method - POST
3. Header - Authorization: Bearer <TOKEN from login/register>
4. Request Payload JSON
```bash
{
  "data":{
    "type":"github_users",
    "attributes":{
        "users": [
            "jraymundo",
            "test123"
        ]
    }
  }
}
```
Response Payload
```bash
{
    "data": [
        {
            "type": "github_users",
            "id": 27031,
            "attributes": {
                "name": "Avi Dutta",
                "login": "test123",
                "company": "Amazon",
                "number_of_followers": 567,
                "number_of_repositories": 4,
                "average_number_of_followers": 14175
            },
            "links": {
                "self": "/github/users/27031"
            }
        },
        {
            "type": "github_users",
            "id": 4113927,
            "attributes": {
                "name": "JL",
                "login": "jraymundo",
                "company": "CodeBrew Inc",
                "number_of_followers": 0,
                "number_of_repositories": 0,
                "average_number_of_followers": 0
            },
            "links": {
                "self": "/github/users/4113927"
            }
        }
    ],
    "links": {
        "self": "https://sidelines.kumu-api.devv/v1/github/users"
    }
}
```
