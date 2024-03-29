# Yii2 Rest

Базовый компонент для работы REST, применяемый в корпоративном шаблоне

В компонент вынесен котроллер для RESTfull API, cо стандартизированными фильтрами и поведениями.

Кроме базовой конфигурации имеется дополнительная надстройка для форматирования и обработки CORS запросов. А так же вынесены базовые ответы API сервера в случае успешного выполнения или ошибки запроса.

## Установка

Для подключения компонентов в свой код необходимо добавить в _composer.json_ следующий код:
```
"require": {
    "oleg-chulakov-studio/yii2-rest": "~1.0.0"
}
```

Или выполнив команду:
```
composer require oleg-chulakov-studio/yii2-rest
```

## Контроль доступа

По умолчанию в Yii2 используется два поведения, которые контролируют доступ к
каждому контролеру. Настройка `VerbFilter` и `AccessControl` фильтров доступа занимает
достаточно весомый массив правил. Поэтому данная настройка была упрощена путем
создания базового массива доступа:

```
public function accessRules()
{
    return [
        'index'   => $this->createAccess('get', true),
        'view'    => $this->createAccess('get', true, '@'),
        'create'  => $this->createAccess('post', true, '@'),
        'update'  => $this->createAccess('put, patch', true, '@'),
        'delete'  => $this->createAccess('delete', true, '@'),
        'options' => $this->createAccess(),
    ];
}
```

Элементы метода доступа и правил доступа может быть записан в двух вариациях:

- `'post, get'` - строка с элементами разделенных запятой
- `['admin', '@']` - массив с элементами

Если требуется расширенная настройка поведения, отличающаяся от стандартного,
можно переопределить метод генерации конфигурации фильтра:

- `AccessControl` - метод `protected function accessBehavior($rules)` получающий список
правил доступа и возвращающий конфигурацию поведения.
- `VerbFilter` - меод `protected function verbsBehavior($actions)` получающий список
экшенов с методами доступа к ним и возвращающий конфигурацию поведения.

## RESTfull API

Каждое API как правило сопровождается дополнительными проверками и поведениями,
обеспечивающими доступ контроллеру для автоизованных пользователей или путем
кросдоменного запроса (CORS). Базовый REST контроллер предоставляет настройку
необходимых поведений для подобных ситуаций.

**Авторизация:**

Для авторизации используется по умолчанию настроено `HttpBearerAuth` поведение.
Который добавляет за собой дополнительные две настройки в контроллер:

- `authenticatorExcept` - Массив исключаемых из авторизации действий
- `authenticatorOnly` - Массив требующих авторизацию действий

Если обе настройки пусты, то все действия контроллера будут закрыты авторизацией.

**ContentNegotiator:**

Для RESTfull API как правило используется формат общения в виде json строки
и данное поведение позволяет автоматически проставить для обмена запросами
преобразование в json формат и обратно. Так же подается настройке:

- `negotiatorExcept` - Массив исключаемых из форматирования действий
- `negotiatorOnly` - Массив требующих форматирование действий

Если обе настройки пусты, то все действия контроллера будут подвержены форматированию.

**CORS:**

Фильтр кросдоменного доступа настроен для каждого API контроллера и предоставляет
максимальный уровень доступа ко всем действиям. На него так же можно повлиять:

- `corsMethods` - Массив разрешенных методов доступа (POST, GET)

Если настройка остается пустой, в нее будет передан массив из `VerbFilter` со всеми
указанными методами к действиям.

**Переопределение и расширенная настройка поведений:**

Каждый фильтр имеет свой метод создания конфигурации поведения. Если метод
возвращает пустой массив или null значение, поведение не будет подключено
к контроллеру.

- Авторизация: `protected function authenticatorBehavior()`
- ContentNegotiator: `protected function contentNegotiatorBehavior()`
- CORS: `protected function corsBehavior()`

**Стандартизированный ответ:**

Для стандартизации ответа с сервера используется два базовых метода:

- Успешное завершение обработки: `return $this->successResult($code = HTTP::SUCCESS_OK, $message = null)`
- Ошибка в процессе обработки: `return $this->errorResult(Model $model, $error)`

Во всех остальных ситуациях может быть возвращен любой массив, что ожидает получить
клиент API запроса.

В результате успешного завершения будет возвращен массив:
```
{
    "success" : true,
    "message" : $message
}
```

В результате сообщения об ошибке будет выброще исключение `BadRequestHttpException`
с указанием первой ошибки валидации формы/модели или с сообщением об ошибке.
Дополнительно для исключения передается `errorStatus` для дополнительно обработки
клиентом ошибки валидации и ошибки сервера. По умолчанию переменная выставлена 
в значение `1`.

Поведения:
----------
- [Поведение "CORS"](docs/behavior-CORS.md)
