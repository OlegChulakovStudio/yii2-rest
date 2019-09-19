# Поведение для CORS запросов

Поведение `CorsBehavior` расширяет базовый класс поведения `yii\filters\Cors`.
Для предупреждения неправильной конфигурации поведения были добавлены некоторые ограничения и дополнительные проверки.

## Использование

Настройка контейнера зависимостей через конфигурацию приложения с обращением к env переменным:

```php
...
'container' => [
    'definitions' => [
        'chulakov\components\rest\behaviors\CorsBehavior' => [
            'allowedOrigin' => [
                'http://site.com',
            ],
            'allowedHeaders' => [...],
            'exposedHeaders' => [...],
            'isAllowedCredentials' => true,
            'optionsCachingMaxAge' => 3600,
        ],
    ],
],
...
```

- `allowedOrigin` -- спиок доменных имен, которым будет разрешено обращаться к ресурсам сайта, соответствует заголовку ответа `Access-Control-Allow-Origin`. По умолчанию будет задано `Access-Control-Allow-Origin : *`
- `allowedHeaders` -- список разрешенных заголовков запроса, соответствует заголовку ответа Access-Control-Allow-Headers. По умолчанию будут разрешены заголовки, определенные в поведении.
- `exposedHeaders` -- список заголовков к которым будет предоставлен доступ на чтение браузером. Будет отказано в доступе js на чтение заголовока, который не был перечислен в списке `Access-Control-Expose-Headers`.
- `isAllowedCredentials` -- булево значение, указывает будет ли с запросом дополнительно передаваться куки и авторизующие заголовки.
В конфигурации поведения разрешено включать этот заголовок только если задан список имен для `Access-Control-Allow-Origin`
- `optionsCachingMaxAge` -- указывает время жизни предзапроса о досупности того или иного метода, после которого будет отправлен новый. Cоответствует заголовку Access-Control-Max-Age. 
При конфигурировании параметр можно опустить, тогда будет использовано значение по умолчанию (3600)

## Пример настройки в отдельном контроллере

```php
public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => CorsBehavior::class,
            'isAllowedCredentials' => false,
        ];
        return $behaviors;
    }
```
