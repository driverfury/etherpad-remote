# Etherpad remote
Etherpad remote is a small PHP web application based on [Slim Framework](https://www.slimframework.com) useful for saving the content of [etherpad](https://etherpad.org) editor into a remote file and for opening a file inside etherpad.
## Installation
To install this application you need to clone this github repository:
```bash
git clone https://github.com/driverfury/collab-edit.git
```
Next you need to install all composer packages required:
```bash
cd collab-edit
composer install
```
Then you need to create a database and set database configurations inside the file `app/database.php`:
```php
<?php

$db_host = '<DB_HOST>';
$db_port = <DB_PORT>;
$db_user = '<DB_USER>';
$db_password = '<DB_PASSWORD>';
$db_name = '<DB_NAME>';
...
```
And finally you need to create tables inside the database by making a GET request - or by simply visiting with a web browser - the following URL:
```
http://<YOUR_HOST>/install
```
## API
There are 3 endpoints for Etherpad remote API.

To edit a file in etherpad you need to make a POST request to `api/start` endpoint and open the URL received.
```
POST api/start

RESPONSE:
{
    "status": "success",
    "pad_id": "<etherpad_pad_id>",
    "url": "<etherpad_pad_url>"
}
```

To save the content of a etherpad pad into a remote file you need to make a POST request to `api/save/{pad_id}` endpoint.
```
POST api/save/{pad_id}

RESPONSE:
{
    "status": "success",
    "pad_id": "<etherpad_pad_id>",
    "message": "File saved"
}
```

To lock a file [WORK IN PROGRESS]
