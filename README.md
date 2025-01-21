# JSONPad SDK for PHP

This package allows you to connect to JSONPad and manage your lists, items, indexes, and identities without needing to use the RESTful API directly.

## Installation

You can install the SDK using Composer:

```bash
composer require basementuniverse/jsonpad-sdk
```

## Usage

Create an instance of the JSONPad SDK and pass in your API token:

```php
require 'vendor/autoload.php';

use JSONPad\JSONPad;

$jsonpad = new JSONPad('your-api-token');
```

You can also pass in an identity group and identity token if you're using identities and you want to cache an identity's credentials in the SDK instance for subsequent requests:

```php
$jsonpad = new JSONPad(
    'your-api-token',
    'your-identity-group',
    'your-identity-token'
);
```

## Contents

### Lists

- [Create a list](#create-a-list)
- [Fetch all lists](#fetch-all-lists)
- [Fetch a list](#fetch-a-list)
- [Search a list](#search-a-list)
- [Fetch list stats](#fetch-list-stats)
- [Fetch list events](#fetch-list-events)
- [Fetch a list event](#fetch-a-list-event)
- [Update a list](#update-a-list)
- [Delete a list](#delete-a-list)

### Items

- [Create an item](#create-an-item)
- [Fetch all items](#fetch-all-items)
- [Fetch all items data](#fetch-all-items-data)
- [Fetch an item](#fetch-an-item)
- [Fetch an item's data](#fetch-an-items-data)
- [Fetch item stats](#fetch-item-stats)
- [Fetch item events](#fetch-item-events)
- [Fetch an item event](#fetch-an-item-event)
- [Update an item](#update-an-item)
- [Update an item's data](#update-an-items-data)
- [Replace an item's data](#replace-an-items-data)
- [Patch an item's data](#patch-an-items-data)
- [Delete an item](#delete-an-item)
- [Delete part of an item's data](#delete-part-of-an-items-data)

### Indexes

- [Create an index](#create-an-index)
- [Fetch all indexes](#fetch-all-indexes)
- [Fetch an index](#fetch-an-index)
- [Fetch index stats](#fetch-index-stats)
- [Fetch index events](#fetch-index-events)
- [Fetch an index event](#fetch-an-index-event)
- [Update an index](#update-an-index)
- [Delete an index](#delete-an-index)

### Identities

- [Create an identity](#create-an-identity)
- [Fetch all identities](#fetch-all-identities)
- [Fetch an identity](#fetch-an-identity)
- [Fetch identity stats](#fetch-identity-stats)
- [Fetch identity events](#fetch-identity-events)

- [Fetch an identity event](#fetch-an-identity-event)
- [Update an identity](#update-an-identity)
- [Delete an identity](#delete-an-identity)
- [Register an identity](#register-an-identity)
- [Login using an identity](#login-using-an-identity)
- [Logout from an identity](#logout-from-an-identity)
- [Fetch the currently logged in identity](#fetch-the-currently-logged-in-identity)
- [Update the currently logged in identity](#update-the-currently-logged-in-identity)
- [Delete the currently logged in identity](#delete-the-currently-logged-in-identity)

## SDK Reference

### Create a list

```php
createList(array $data): JSONPad\List
```

Example:

```php
$list = $jsonpad->createList([
    'name' => 'My List',
    'description' => 'This is my list',
    'schema' => [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'age' => ['type' => 'number'],
        ],
        'required' => ['name', 'age'],
    ],
]);
```

### Fetch all lists

```php
fetchLists(array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchLists([
    'page' => 1,
    'limit' => 10,
    'order' => 'createdAt',
    'direction' => 'desc',
]);
```

### Fetch a list

```php
fetchList(string $listId): JSONPad\List
```

Example:

```php
$list = $jsonpad->fetchList('3e3ce22b-ec32-4c9d-956b-27ba00f38aa9');
```

### Search a list

```php
searchList(string $listId, string $query, array $parameters = []): array
```

Example:

```php
$results = $jsonpad->searchList(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    'search query',
    [
        'includeItems' => true,
        'includeData' => true,
    ]
);
```

### Fetch list stats

```php
fetchListStats(string $listId, array $parameters = []): array
```

Example:

```php
$stats = $jsonpad->fetchListStats(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'days' => 7,
    ]
);
```

### Fetch list events

```php
fetchListEvents(string $listId, array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchListEvents(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
        'startAt' => '2021-01-01',
        'endAt' => '2021-12-31',
    ]
);
```

### Fetch a list event

```php
fetchListEvent(string $listId, string $eventId): JSONPad\Event
```

Example:

```php
$event = $jsonpad->fetchListEvent(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    'b87aacfb-15b3-43d3-8ffc-a21443ee05f2'
);
```

### Update a list

```php
updateList(string $listId, array $data): JSONPad\List
```

Example:

```php
$list = $jsonpad->updateList(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'name' => 'My Updated List',
        'description' => 'This is my updated list',
    ]
);
```

### Delete a list

```php
deleteList(string $listId): void
```

Example:

```php
$jsonpad->deleteList('3e3ce22b-ec32-4c9d-956b-27ba00f38aa9');
```

### Create an item

```php
createItem(string $listId, array $data, array $parameters = [], array $identity = null): JSONPad\Item
```

Example:

```php
$item = $jsonpad->createItem(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'data' => [
            'name' => 'Alice',
            'age' => 30,
        ],
        'description' => 'This is Alice',
    ],
    [
        'generate' => false,
        'includeData' => true,
    ]
);
```

### Fetch all items

```php
fetchItems(string $listId, array $parameters = [], array $identity = null): array
```

Example:

```php
$response = $jsonpad->fetchItems(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
    ]
);
```

### Fetch all items data

```php
fetchItemsData(string $listId, array $parameters = [], array $identity = null): array
```

Example:

```php
$response = $jsonpad->fetchItemsData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
    ]
);
```

### Fetch an item

```php
fetchItem(string $listId, string $itemId, array $parameters = [], array $identity = null): JSONPad\Item
```

Example:

```php
$item = $jsonpad->fetchItem(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b'
);
```

### Fetch an item's data

```php
fetchItemData(string $listId, string $itemId, array $parameters = [], array $identity = null): array
```

Example:

```php
$itemData = $jsonpad->fetchItemData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b'
);
```

### Fetch item stats

```php
fetchItemStats(string $listId, string $itemId, array $parameters = []): array
```

Example:

```php
$stats = $jsonpad->fetchItemStats(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'days' => 7,
    ]
);
```

### Fetch item events

```php
fetchItemEvents(string $listId, string $itemId, array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchItemEvents(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
        'startAt' => '2021-01-01',
        'endAt' => '2021-12-31',
    ]
);
```

### Fetch an item event

```php
fetchItemEvent(string $listId, string $itemId, string $eventId): JSONPad\Event
```

Example:

```php
$event = $jsonpad->fetchItemEvent(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    'b87aacfb-15b3-43d3-8ffc-a21443ee05f2'
);
```

### Update an item

```php
updateItem(string $listId, string $itemId, array $data, array $parameters = [], array $identity = null): JSONPad\Item
```

Example:

```php
$item = $jsonpad->updateItem(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'data' => [
            'name' => 'Alice',
            'age' => 31,
        ],
        'description' => 'This is Alice',
    ]
);
```

### Update an item's data

```php
updateItemData(string $listId, string $itemId, array $data, array $parameters = [], array $identity = null): array
```

Example:

```php
$itemData = $jsonpad->updateItemData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'name' => 'Alice',
        'age' => 31,
    ],
    [
        'pointer' => '<JSON Pointer>',
    ]
);
```

### Replace an item's data

```php
replaceItemData(string $listId, string $itemId, array $data, array $parameters = [], array $identity = null): array
```

Example:

```php
$itemData = $jsonpad->replaceItemData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'name' => 'Alice',
        'age' => 31,
    ],
    [
        'pointer' => '<JSON Pointer>',
    ]
);
```

### Patch an item's data

```php
patchItemData(string $listId, string $itemId, array $patch, array $parameters = [], array $identity = null): array
```

Example:

```php
$itemData = $jsonpad->patchItemData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        ['op' => 'add', 'path' => '/name', 'value' => 'Alice'],
        ['op' => 'add', 'path' => '/age', 'value' => 31],
    ],
    [
        'pointer' => '<JSON Pointer>',
    ]
);
```

### Delete an item

```php
deleteItem(string $listId, string $itemId, array $identity = null): void
```

Example:

```php
$jsonpad->deleteItem(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b'
);
```

### Delete part of an item's data

```php
deleteItemData(string $listId, string $itemId, array $parameters = [], array $identity = null): JSONPad\Item
```

Example:

```php
$itemData = $jsonpad->deleteItemData(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '098e58bc-05f6-4a59-a755-fb9bc54f4a5b',
    [
        'pointer' => '<JSON Pointer>',
    ]
);
```

### Create an index

```php
createIndex(string $listId, array $data): JSONPad\Index
```

Example:

```php
$index = $jsonpad->createIndex(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'name' => 'Name',
        'description' => 'Name index',
        'pathName' => 'name',
        'valueType' => 'string',
        'alias' => false,
        'sorting' => true,
        'filtering' => true,
        'searching' => true,
        'defaultOrderDirection' => 'asc',
    ]
);
```

### Fetch all indexes

```php
fetchIndexes(string $listId, array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchIndexes(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
    ]
);
```

### Fetch an index

```php
fetchIndex(string $listId, string $indexId): JSONPad\Index
```

Example:

```php
$index = $jsonpad->fetchIndex(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6'
);
```

### Fetch index stats

```php
fetchIndexStats(string $listId, string $indexId, array $parameters = []): array
```

Example:

```php
$stats = $jsonpad->fetchIndexStats(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6',
    [
        'days' => 7,
    ]
);
```

### Fetch index events

```php
fetchIndexEvents(string $listId, string $indexId, array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchIndexEvents(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
        'startAt' => '2021-01-01',
        'endAt' => '2021-12-31',
    ]
);
```

### Fetch an index event

```php
fetchIndexEvent(string $listId, string $indexId, string $eventId): JSONPad\Event
```

Example:

```php
$event = $jsonpad->fetchIndexEvent(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6',
    'b87aacfb-15b3-43d3-8ffc-a21443ee05f2'
);
```

### Update an index

```php
updateIndex(string $listId, string $indexId, array $data): JSONPad\Index
```

Example:

```php
$index = $jsonpad->updateIndex(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6',
    [
        'name' => 'Name',
        'description' => 'Name index',
        'pathName' => 'name',
        'valueType' => 'string',
        'alias' => false,
        'sorting' => true,
        'filtering' => true,
        'searching' => true,
        'defaultOrderDirection' => 'asc',
    ]
);
```

### Delete an index

```php
deleteIndex(string $listId, string $indexId): void
```

Example:

```php
$jsonpad->deleteIndex(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    '9963146e-aa36-46f9-9f63-497ab9e5d1c6'
);
```

### Create an identity

```php
createIdentity(array $data): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->createIdentity([
    'name' => 'Alice',
    'group' => 'my-group',
    'password' => 'secret',
]);
```

### Fetch all identities

```php
fetchIdentities(array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchIdentities([
    'page' => 1,
    'limit' => 10,
    'order' => 'createdAt',
    'direction' => 'desc',
]);
```

### Fetch an identity

```php
fetchIdentity(string $identityId): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->fetchIdentity('3e3ce22b-ec32-4c9d-956b-27ba00f38aa9');
```

### Fetch identity stats

```php
fetchIdentityStats(string $identityId, array $parameters = []): array
```

Example:

```php
$stats = $jsonpad->fetchIdentityStats(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'days' => 7,
    ]
);
```

### Fetch identity events

```php
fetchIdentityEvents(string $identityId, array $parameters = []): array
```

Example:

```php
$response = $jsonpad->fetchIdentityEvents(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'page' => 1,
        'limit' => 10,
        'order' => 'createdAt',
        'direction' => 'desc',
        'startAt' => '2021-01-01',
        'endAt' => '2021-12-31',
    ]
);
```

### Fetch an identity event

```php
fetchIdentityEvent(string $identityId, string $eventId): JSONPad\Event
```

Example:

```php
$event = $jsonpad->fetchIdentityEvent(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    'b87aacfb-15b3-43d3-8ffc-a21443ee05f2'
);
```

### Update an identity

```php
updateIdentity(string $identityId, array $data): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->updateIdentity(
    '3e3ce22b-ec32-4c9d-956b-27ba00f38aa9',
    [
        'name' => 'Alice',
        'group' => 'my-group',
        'password' => 'secret',
    ]
);
```

### Delete an identity

```php
deleteIdentity(string $identityId): void
```

Example:

```php
$jsonpad->deleteIdentity('3e3ce22b-ec32-4c9d-956b-27ba00f38aa9');
```

### Register an identity

```php
registerIdentity(array $data, array $identity = null): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->registerIdentity([
    'name' => 'Alice',
    'group' => 'my-group',
    'password' => 'secret',
]);
```

### Login using an identity

```php
loginIdentity(array $data, array $identity = null): array
```

Example:

```php
[$identity, $token] = $jsonpad->loginIdentity([
    'name' => 'Alice',
    'group' => 'my-group',
    'password' => 'secret',
]);
```

### Logout from an identity

```php
logoutIdentity(array $identity = null): void
```

Example:

```php
$jsonpad->logoutIdentity();
```

### Fetch the currently logged in identity

```php
fetchSelfIdentity(array $identity = null): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->fetchSelfIdentity();
```

### Update the currently logged in identity

```php
updateSelfIdentity(array $data, array $identity = null): JSONPad\Identity
```

Example:

```php
$identity = $jsonpad->updateSelfIdentity(
    [
        'name' => 'Alice',
        'group' => 'my-group',
        'password' => 'secret',
    ],
    [
        'group' => 'my-group',
        'token' => 'your-identity-token',
    ]
);
```

### Delete the currently logged in identity

```php
deleteSelfIdentity(array $identity = null): void
```

Example:

```php
$jsonpad->deleteSelfIdentity([
    'group' => 'my-group',
    'token' => 'your-identity-token',
]);
```

See the [JSONPad API documentation](https://jsonpad.io/docs/api-reference) for more information.
