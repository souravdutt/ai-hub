# AI Hub - One App for all type of AIs!
This is a `Laravel 11` && `Vue 3` web app having integrated multiple type of Cloudflare Workers AI APIs. 

## Demo!
https://github.com/user-attachments/assets/384a7d32-cb78-4fef-a781-242f31173090

## Why Cloudflare Workers AI?
Main reason is, it offers unlimited API requests (at least while creating this app, but not sure about future) for any model that is in beta, and free 10K Neurons/month for stable models. So you can create and test as many apps without any worry about the request limits or tokens.

## Pre-requirements
- WAMP/LAMP/XAMP (You will love <a href="https://laragon.org/download/">Laragon</a>)
- PHP 8.3^ (Download <a href="https://www.php.net/downloads.php">here</a>)
- Node 18^ (Download <a href="https://nodejs.org/en/download/package-manager">here</a>)
- <a href="https://getcomposer.org/download/">Composer</a>
- Cloudflare Account ID (<a href="https://developers.cloudflare.com/fundamentals/setup/find-account-and-zone-ids/">learn more</a>)
- Cloudflare <a href="https://dash.cloudflare.com/profile/api-tokens">API Token</a> (Create one with Workers AI Template)
- AI Model ID (<a href="https://developers.cloudflare.com/workers-ai/models/">learn more</a>)

You can read complete Workers AI API documentation <a href="https://developers.cloudflare.com/workers-ai/get-started/rest-api/">here</a>

## Installation:
Clone the repo in your system
```
git clone https://github.com/souravdutt/ai-hub.git
```
Go to installation directory
```
cd ai-hub
```
Install dependencies
```
composer instasll
```
```
npm install
```
Create `.env` file
```
cp .env.example .env
```
Generate App Key
```
php artisan key:generate
```
Open project in <a href="https://code.visualstudio.com/download">VS Code</a>
```
code .
```
Change below environment variables accordingly in your `.env` file
```
WORKERS_API_TOKEN=
WORKERS_ACCOUNT_ID=
```
Migrate database
```
php artisan migrate
```
Start node server
```
npm run dev
```
Serve project
```
php artisan serve
```
Open `http://127.0.0.1:8000/` or `http://ai-hub.test` whichever suitable as per your local setup.
