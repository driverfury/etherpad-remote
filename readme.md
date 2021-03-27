# Collab edit
Collab edit is a small PHP web application based on [Slim Framework](https://www.slimframework.com)
useful for saving the content of an editor - or collaborative one - to a remote file.
## Installation
To install this application you need to clone this github repository:
> git clone https://github.com/driverfury/collab-edit.git
Next you need to install all composer packages required:
> cd collab-edit
> composer install
And finally, you need to set database configurations inside the file app/database.php
> <?php
>
> $db_host = '<DB_HOST>';
> $db_port = <DB_PORT>;
> $db_user = '<DB_USER>';
> $db_password = '<DB_PASSWORD>';
> $db_name = '<DB_NAME>';
> ...
## API
