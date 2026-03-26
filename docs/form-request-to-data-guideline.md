# FormRequest to Data Guideline

## Цель

Этот guideline фиксирует паттерн для входных данных в API:

`FormRequest -> Spatie Data -> Service`

Идея простая:

- `FormRequest` отвечает за авторизацию, валидацию и подготовку входа.
- `Data` отвечает за типизированное представление уже валидных данных.
- `Service` отвечает за бизнес-операцию и не должен работать с сырыми массивами из HTTP.

## Почему это нужно

Проблема, которую мы решаем:

- массивы из `$request->validated()` не дают строгого контракта;
- ключи типа `game_type_id` протекают в доменный код;
- сервисы начинают зависеть от формы HTTP payload;
- сложнее поддерживать автодополнение, рефакторинг и статический анализ.

Что дает `Spatie Laravel Data`:

- типизированный объект вместо массива;
- явный контракт use-case;
- удобный mapping между `snake_case` API и `camelCase` PHP;
- более чистую границу между transport layer и application layer.

## Базовое правило

Для каждого write-scenario в API:

1. Создаем отдельный `FormRequest`.
2. Создаем отдельный `Data`-класс под этот сценарий.
3. Добавляем в request метод `toData()`.
4. Передаем в service только `Data`, а не массив.

## Разделение ответственности

### FormRequest

`FormRequest` должен отвечать за:

- `authorize()`;
- `rules()`;
- `messages()` при необходимости;
- `attributes()` при необходимости;
- `prepareForValidation()` если нужно нормализовать вход;
- `passedValidation()` если нужна пост-обработка после валидации;
- `toData()` для возврата typed-объекта.

`FormRequest` не должен:

- содержать бизнес-логику создания сущности;
- ходить в сервисы ради доменной операции;
- возвращать в контроллер сырой массив, если для use-case уже заведен `Data`.

### Data

`Data` должен отвечать за:

- типизированные свойства use-case;
- mapping имен входных полей;
- транспорт данных между HTTP boundary и service layer.

`Data` не должен:

- знать про HTTP-ответ;
- знать про Eloquent persistence;
- содержать побочные эффекты.

### Service

`Service` должен:

- принимать typed-объект;
- выполнять прикладную операцию;
- работать с доменной моделью, а не с формой HTTP payload.

`Service` не должен:

- читать `$request`;
- ожидать `array<string, mixed>`;
- знать, как назывались входные поля в JSON.

## Именование

Рекомендуемое именование:

- request: `StoreMatchRequest`
- data: `StoreMatchData`
- service: `CreateMatchService`
- request converter method: `toData()`

Почему `toData()`, а не `data()`:

- в Laravel 13 у базового `Request` уже есть метод `data($key = null, $default = null)`;
- переопределение `data()` с другим return type ломает совместимость сигнатур;
- поэтому безопасное и понятное имя здесь `toData()`.

## Текущий пример в проекте

### 1. Data-класс

Файл: `app/Data/Matches/StoreMatchData.php`

```php
<?php

namespace App\Data\Matches;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class StoreMatchData extends Data
{
    public function __construct(
        #[MapInputName('game_type_id')]
        public int $gameTypeId,
    ) {
    }
}
```

Смысл:

- снаружи API принимает `game_type_id`;
- внутри PHP-кода используем `gameTypeId`;
- сервис дальше работает уже с нормальным typed-свойством.

### 2. FormRequest

Файл: `app/Http/Requests/Api/StoreMatchRequest.php`

```php
public function rules(): array
{
    return [
        'game_type_id' => [
            'required',
            'integer',
            Rule::exists(GameType::class, 'id'),
        ],
    ];
}

public function toData(): StoreMatchData
{
    return StoreMatchData::from($this->safe()->all());
}
```

Смысл:

- request валидирует вход;
- request не возвращает наружу сырой массив;
- request отдает только безопасный typed-объект.

Важно:

- используем `$this->safe()->all()`, а не `$this->all()`;
- это гарантирует, что в `Data` попадут только прошедшие валидацию поля.

### 3. Controller

Файл: `app/Http/Controllers/Api/MatchController.php`

```php
public function store(StoreMatchRequest $request, CreateMatchService $createMatchService): JsonResponse
{
    $match = $createMatchService->create($request->toData());

    return MatchResource::make($match)
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
}
```

Смысл:

- контроллер остается тонким;
- он не знает внутреннюю структуру массива;
- он просто прокидывает DTO в service.

### 4. Service

Файл: `app/Services/Matches/CreateMatchService.php`

```php
public function create(StoreMatchData $data): GameMatch
{
    $match = GameMatch::query()->create([
        'game_type_id' => $data->gameTypeId,
    ]);

    return $match->load('gameType');
}
```

Смысл:

- сервис принимает ровно тот контракт, который ему нужен;
- сервис не зависит от HTTP naming;
- сервис проще тестировать и рефакторить.

## Обязательные правила для новых API request-классов

### Делать

- Создавать отдельный `Data` на каждый сценарий записи.
- Держать `rules()` в request.
- Возвращать DTO через `toData()`.
- Использовать `MapInputName`, если API-поле в `snake_case`, а внутри нужен `camelCase`.
- Передавать DTO в service или action.
- Покрывать хотя бы один happy-path feature-тест.
- При необходимости добавлять unit-тест на `toData()`.

### Не делать

- Не передавать `$request->validated()` напрямую в сервис.
- Не передавать `array $data` в service, если сценарий уже оформлен через request.
- Не использовать `$request->all()` для создания DTO.
- Не смешивать transport DTO и response resource.
- Не добавлять бизнес-логику в `FormRequest`.

## Когда использовать prepareForValidation

Использовать `prepareForValidation()`, если нужно:

- trim строк;
- привести типы;
- склеить или разложить поля;
- подставить дефолтные значения до запуска validator.

Пример:

```php
protected function prepareForValidation(): void
{
    $this->merge([
        'email' => strtolower(trim((string) $this->input('email'))),
    ]);
}
```

Если после этого request возвращает DTO, то в `toData()` уже попадет нормализованный и провалидированный input.

## Когда использовать Data mapping

Использовать mapping через `MapInputName`, если:

- внешний API-контракт должен остаться `snake_case`;
- внутри PHP-кода нужен `camelCase`;
- мы хотим убрать transport naming из сервиса.

Не использовать mapping, если:

- имя поля и так уже нормальное и не создает шума;
- DTO создается не из HTTP payload, а из внутреннего PHP-контракта.

## Тестовая стратегия

Минимум для нового сценария:

1. Feature-тест API на happy path.
2. Feature-тест API на validation error.
3. Если request содержит отдельный conversion contract, unit-тест на `toData()`.

Для особо важных сценариев:

4. Feature-тест, что контроллер/контейнер передает в service именно `Data`, а не массив.

## Шаблон для новых сценариев

### Data

```php
<?php

namespace App\Data\SomeContext;

use Spatie\LaravelData\Data;

class CreateSomethingData extends Data
{
    public function __construct(
        public string $name,
        public int $ownerId,
    ) {
    }
}
```

### Request

```php
<?php

namespace App\Http\Requests\Api;

use App\Data\SomeContext\CreateSomethingData;
use Illuminate\Foundation\Http\FormRequest;

class StoreSomethingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'owner_id' => ['required', 'integer'],
        ];
    }

    public function toData(): CreateSomethingData
    {
        return CreateSomethingData::from($this->safe()->all());
    }
}
```

### Controller

```php
public function store(StoreSomethingRequest $request, CreateSomethingService $service): JsonResponse
{
    $result = $service->handle($request->toData());

    return response()->json($result, 201);
}
```

### Service

```php
public function handle(CreateSomethingData $data): Model
{
    // business logic
}
```

## Итоговый стандарт для проекта

Во всех новых API write-flow придерживаемся правила:

- `FormRequest` валидирует;
- `FormRequest` возвращает `toData()`;
- `Controller` остается тонким;
- `Service` принимает только `Data`;
- массивы из request не выходят за boundary HTTP-слоя.
