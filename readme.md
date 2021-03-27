# Etherpad remote
Etherpad remote is a small PHP web application based on [Slim Framework](https://www.slimframework.com) useful for saving the content of [etherpad](https://etherpad.org) editor into a remote file and for opening a file inside etherpad.
## Installation
To install this application you need to clone this github repository:
```bash
git clone https://github.com/driverfury/etherpad-remote.git
```
Next you need to install all composer packages required:
```bash
cd etherpad-remote
composer install
```
Then you need to create a database and set configurations (database and etherpad config) inside the file `app/config.php`:
```php
    'database' => [
        'host' => '<DB_HOST>',
        'port' => <DB_PORT>,
        'user' => '<DB_USER>',
        'password' => '<DB_PASSWORD>',
        'name' => '<DB_NAME>',
    ],

    'etherpad' => [
        'host' => 'http://<ETHERPAD_HOST>:<ETHERPAD_PORT>',
        'token' => '<ETHERPAD_TOKEN>',
    ],
```
And finally you need to create tables inside the database by making a GET request - or by simply visiting with a web browser - the following URL:
```
http://<YOUR_HOST>/install
```
## API
There are 3 endpoints for Etherpad remote API.

To edit a file in etherpad you need to make a POST request to `api/start` endpoint and open the URL received.
```
POST /api/start
Content-Type: application/json

REQUEST BODY:
{
    "file": "<file_pathname>"
}

RESPONSE:
{
    "status": "success",
    "pad_id": "<etherpad_pad_id>",
    "url": "<etherpad_pad_url>"
}
```

To save the content of a etherpad pad into a remote file you need to make a POST request to `api/save/{pad_id}` endpoint.
```
POST /api/save/{pad_id}
Content-Type: application/json

RESPONSE:
{
    "status": "success",
    "pad_id": "<etherpad_pad_id>",
    "message": "File saved"
}
```

To lock a file [WORK IN PROGRESS]
