# composer-system-administrator

#### Required Environment variables

- APP_ENV: dev
- APP_SECRET: keyboard_cat
- SYSTEM_EMAIL: valid@email.com // used to create the system user
- SYSTEM_PASSWORD: keyboard_cat // used to create the system user
- DOMAIN: composer.local
- DATABASE_PREFIX: your_system_name_ // used while creating tenant database, the code of the tenant is concatenated at the end

#### .env
The .env file should be a template/reference of the configurations that your system needs. You MUST create a .env.local
to override the configurations on your local machine or .env.$APP_ENV.local for a specific environment.

### Useful commands
run php unit test coverage:
```
vendor/bin/phpunit --coverage-html=coverage
```



## 1.Migrations
After setting up the DATABASE_URL, use the doctrine generate database to create your database or create it
directly on the engine of you choice, 
then run the migration command.

***If you are using docker containers don't forget to enter the container first***

```
    bin/console doctrine:database:create
    bin/console doctrine:migrations:migrate
```

## 2.Queue

Some tasks are done asynchronously (ex: tenant database creation), and for that to happen we need to have the consumer
running so that it can assign the queue messages to their respective handlers.

***If you are using docker containers don't forget to enter the container first***
```
    bin/console messenger:consume
```

If asked, select the async consumer.

## 3.API JWT

To access the API we need a valid JWT. When we login in the API a valid JWT is generated and return.

To generate this token the system needs a public and private key, and to generate that we need to run:

***If you are using docker containers don't forget to enter the container first***
```
    bin/console lexik:jwt:generate-keypair
```

This key is saved on ___./config/jwt___ directory. After the command execution, check if the files 
are generated correctly

### Disclaimer
Do we need to implement DDD, CQRS and Hexagonal architecture on a small project like this? Noup, I wanted to use them
so that I could implement some theoretical knowledge and have some practice on these matters.