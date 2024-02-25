How to setup project:

- Add database credentials and discog key and secret in `.env.local`
- Check requirements: `symfony check:requirements`
- Install dependencies: `composer install`
- Create DB: `php bin/console doctrine:database:create`
- Create tables (?): `php bin/console doctrine:migrations:migrate`
