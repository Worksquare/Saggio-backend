# DOCUMENTATION FOR REGISTRATION , LOGIN AND REST PASSWORD

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

Sure! Here's a step-by-step guide on how to use Postman to test the Laravel Sanctum API for user authentication:

Step 1: Start Laravel Development Server
Ensure that your Laravel application is running locally on a development server. You can start the server by running the following command in your Laravel project root:


php artisan serve


Step 2: Register a New User
In Postman, make a POST request to the following URL to register a new user:


POST {{THE URL COME IN}}/api/register
Set the request body to JSON format and provide the required user registration data, for example:

json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "password",
  "password_confirmation": "password"
}
Click the "Send" button to register the new user. If successful, you should receive a response similar to:

json
{
  "token": "YOUR_API_TOKEN"
}
Step 3: Login with the Registered User
In Postman, make a POST request to the following URL to log in with the registered user:


POST {{THE URL COME IN}}/api/login
Set the request body to JSON format and provide the user's login credentials, for example:

json
{
  "email": "john.doe@example.com",
  "password": "password"
}
Click the "Send" button to log in. If successful, you should receive a response similar to:

json
{
  "token": "YOUR_API_TOKEN"
}
Step 4: Access Protected Routes
Now that you have obtained the API token, you can use it to access protected routes in your Laravel application. For example, let's assume you have a protected route to retrieve the user's profile:


GET {{THE URL COME IN}}/api/user
In Postman, make a GET request to the above URL and add the API token to the request header:


Authorization: Bearer YOUR_API_TOKEN
Click the "Send" button to retrieve the user's profile. If successful, you should receive a response with the user's information.

Step 5: Logout
To log out the user and invalidate the API token, make a POST request to the following URL:


POST {{THE URL COME IN}}/api/logout
Add the API token to the request header as before:


Authorization: Bearer YOUR_API_TOKEN
Click the "Send" button to log out. If successful, you should receive a response similar to:

json
{
  "message": "Logged out successfully"
}
That's it! You have now successfully tested the Laravel Sanctum API for user authentication using Postman. Remember to handle API tokens securely and implement proper error handling in your React application for a complete user authentication experience.

