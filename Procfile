web: heroku-php-apache2 public/
web: ADDR=:$PORT ./mercure --jwt-key='!ChangeMe! --debug --allow-anonymous --cors-allowed-origins='*' --publish-allowed-origins='*'

release: php bin/console doctrine:migrations:migrate --no-interaction