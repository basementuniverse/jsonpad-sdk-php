<?php

namespace JSONPad;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use \JSONPad\Models\Event;
use \JSONPad\Models\Identity;
use \JSONPad\Models\Index;
use \JSONPad\Models\Item;
use \JSONPad\Models\ItemList;
use \JSONPad\Models\User;

class JSONPad
{
    private $token;
    private $identityGroup;
    private $identityToken;
    private $client;

    /**
     * Create a new JSONPad client instance
     *
     * @param string $token Your JSONPad API token
     * @param string $identityGroup An optional identity group to use for requests
     * @param string $identityToken An optional identity token to use for requests
     */
    public function __construct($token, $identityGroup = null, $identityToken = null) {
        $this->token = $token;
        $this->identityGroup = $identityGroup;
        $this->identityToken = $identityToken;
        $this->client = new Client(['base_uri' => 'https://api.jsonpad.io']);
    }

    /**
     * Make a request to the JSONPad API
     *
     * @param string $method The HTTP method to use
     * @param string $uri The URI to request
     * @param array $parameters Query parameters to include
     * @param array $data Optional JSON data to include in the request body
     * @param string $identityGroup An optional identity group to include in the headers
     * @param string $identityToken An optional identity token to include in the headers
     */
    private function request(
        $method,
        $uri,
        $parameters = [],
        $data = null,
        $identityGroup = null,
        $identityToken = null
    ) {
        $options = [
            'headers' => [
                'x-api-token' => $this->token,
                'Content-Type' => 'application/json'
            ],
            'query' => $parameters,
            'json' => $data
        ];

        if ($identityGroup) {
            $options['headers']['x-identity-group'] = $identityGroup;
        }

        if ($identityToken) {
            $options['headers']['x-identity-token'] = $identityToken;
        }

        try {
            $response = $this->client->request($method, $uri, $options);
            if ($response->getStatusCode() === 204) {
                return null;
            }
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // LISTS
    // -------------------------------------------------------------------------

    /**
     * Create a new list
     *
     * @param array $data Data for the list to create
     */
    public function createList($data) {
        $response = $this->request('POST', '/lists', [], $data);
        return new ItemList($response);
    }

    /**
     * Fetch a page of lists
     *
     * @param array $parameters Query parameters to include
     */
    public function fetchLists($parameters = []) {
        $response = $this->request('GET', '/lists', $parameters);
        $response['data'] = array_map(function($datum) {
            return new ItemList($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch a list
     *
     * @param string $listId The ID of the list to fetch
     */
    public function fetchList($listId) {
        $response = $this->request('GET', "/lists/{$listId}");
        return new ItemList($response);
    }

    /**
     * Search a list
     *
     * @param string $listId The ID of the list to search
     * @param string $query The query to search for
     * @param array $parameters Query parameters to include
     */
    public function searchList($listId, $query, $parameters = []) {
        $parameters['query'] = $query;
        $response = $this->request('GET', "/lists/{$listId}/search", $parameters);
        return array_map(function($result) {
            if (isset($result['item'])) {
                return [
                    'relevance' => $result['relevance'],
                    'item' => new Item($result['item'])
                ];
            }
            return $result;
        }, $response);
    }

    /**
     * Fetch list stats
     *
     * @param string $listId The ID of the list to fetch stats for
     * @param array $parameters Query parameters to include
     */
    public function fetchListStats($listId, $parameters = []) {
        return $this->request('GET', "/lists/{$listId}/stats", $parameters);
    }

    /**
     * Fetch a page of events for a list
     *
     * @param string $listId The ID of the list to fetch events for
     * @param array $parameters Query parameters to include
     */
    public function fetchListEvents($listId, $parameters = []) {
        $response = $this->request('GET', "/lists/{$listId}/events", $parameters);
        $response['data'] = array_map(function($datum) {
            return new Event($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch a list event
     *
     * @param string $listId The ID of the list to fetch the event for
     * @param string $eventId The ID of the event to fetch
     */
    public function fetchListEvent($listId, $eventId) {
        $response = $this->request('GET', "/lists/{$listId}/events/{$eventId}");
        return new Event($response);
    }

    /**
     * Update a list
     *
     * @param string $listId The ID of the list to update
     * @param array $data Data to update the list with
     */
    public function updateList($listId, $data) {
        $response = $this->request('PUT', "/lists/{$listId}", [], $data);
        return new ItemList($response);
    }

    /**
     * Delete a list
     *
     * @param string $listId The ID of the list to delete
     */
    public function deleteList($listId) {
        $this->request('DELETE', "/lists/{$listId}");
    }

    // -------------------------------------------------------------------------
    // ITEMS
    // -------------------------------------------------------------------------

    /**
     * Create a new item
     *
     * @param string $listId The ID of the list to create the item in
     * @param array $data Data for the item to create
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function createItem($listId, $data, $parameters = [], $identity = null) {
        $response = $this->request(
            'POST',
            "/lists/{$listId}/items",
            $parameters,
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Fetch a page of items from a list
     *
     * @param string $listId The ID of the list to fetch items from
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function fetchItems($listId, $parameters = [], $identity = null) {
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items",
            $parameters,
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        $response['data'] = array_map(function($datum) {
            return new Item($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch a page of items from a list, and only return their data
     *
     * @param string $listId The ID of the list to fetch items data from
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function fetchItemsData($listId, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items/data{$pointerString}",
            array_diff_key($parameters, ['pointer' => '']),
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return $response;
    }

    /**
     * Fetch item stats
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to fetch stats for
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function fetchItem($listId, $itemId, $parameters = [], $identity = null) {
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items/{$itemId}",
            $parameters,
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Fetch data for an item
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to fetch data for
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function fetchItemData($listId, $itemId, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items/{$itemId}/data{$pointerString}",
            array_diff_key($parameters, ['pointer' => '']),
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return $response;
    }

    /**
     * Fetch item stats
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to fetch stats for
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function fetchItemStats($listId, $itemId, $parameters = []) {
        return $this->request('GET', "/lists/{$listId}/items/{$itemId}/stats", $parameters);
    }

    /**
     * Fetch a page of events for an item
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to fetch events for
     * @param array $parameters Query parameters to include
     */
    public function fetchItemEvents($listId, $itemId, $parameters = []) {
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items/{$itemId}/events",
            $parameters
        );
        $response['data'] = array_map(function($datum) {
            return new Event($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch an item event
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to fetch the event for
     * @param string $eventId The ID of the event to fetch
     */
    public function fetchItemEvent($listId, $itemId, $eventId) {
        $response = $this->request(
            'GET',
            "/lists/{$listId}/items/{$itemId}/events/{$eventId}"
        );
        return new Event($response);
    }

    /**
     * Update an item
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to update
     * @param array $data Data to update the item with
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function updateItem($listId, $itemId, $data, $parameters = [], $identity = null) {
        $response = $this->request(
            'PUT',
            "/lists/{$listId}/items/{$itemId}",
            $parameters,
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Update an item's data
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to update
     * @param array $data Data to update the item with
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function updateItemData($listId, $itemId, $data, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'POST',
            "/lists/{$itemId}/items/{$itemId}/data{$pointerString}",
            $parameters,
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Replace an item's data
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to replace the data for
     * @param array $data Data to replace the item's data with
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function replaceItemData($listId, $itemId, $data, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'PUT',
            "/lists/{$listId}/items/{$itemId}/data{$pointerString}",
            $parameters,
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Patch an item's data
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to patch the data for
     * @param array $patch JSON Patch data to apply to the item's data
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function patchItemData($listId, $itemId, $patch, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'PATCH',
            "/lists/{$listId}/items/{$itemId}/data{$pointerString}",
            $parameters,
            $patch,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    /**
     * Delete an item
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to delete
     * @param array $identity Identity information to include in the request
     */
    public function deleteItem($listId, $itemId, $identity = null) {
        $this->request(
            'DELETE',
            "/lists/{$listId}/items/{$itemId}",
            [],
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
    }

    /**
     * Delete an item's data, or part of an item's data
     *
     * @param string $listId The ID of the list the item is in
     * @param string $itemId The ID of the item to delete the data for
     * @param array $parameters Query parameters to include
     * @param array $identity Identity information to include in the request
     */
    public function deleteItemData($listId, $itemId, $parameters = [], $identity = null) {
        $pointerString = isset($parameters['pointer']) ? "/{$parameters['pointer']}" : '';
        $response = $this->request(
            'DELETE',
            "/lists/{$listId}/items/{$itemId}/data{$pointerString}",
            $parameters,
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Item($response);
    }

    // ---------------------------------------------------------------------------
    // INDEXES
    // ---------------------------------------------------------------------------

    /**
     * Create a new index in a list
     *
     * @param string $listId The ID of the list to create the index in
     * @param array $data Data for the index to create
     */
    public function createIndex($listId, $data) {
        $response = $this->request('POST', "/lists/{$listId}/indexes", [], $data);
        return new Index($response);
    }

    /**
     * Fetch a page of indexes from a list
     *
     * @param string $listId The ID of the list to fetch indexes from
     * @param array $parameters Query parameters to include
     */
    public function fetchIndexes($listId, $parameters = []) {
        $response = $this->request('GET', "/lists/{$listId}/indexes", $parameters);
        $response['data'] = array_map(function($datum) {
            return new Index($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch an index from a list
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to fetch
     */
    public function fetchIndex($listId, $indexId) {
        $response = $this->request('GET', "/lists/{$listId}/indexes/{$indexId}");
        return new Index($response);
    }

    /**
     * Fetch index stats
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to fetch stats for
     * @param array $parameters Query parameters to include
     */
    public function fetchIndexStats($listId, $indexId, $parameters = []) {
        return $this->request('GET', "/lists/{$listId}/indexes/{$indexId}/stats", $parameters);
    }

    /**
     * Fetch a page of events for an index
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to fetch events for
     * @param array $parameters Query parameters to include
     */
    public function fetchIndexEvents($listId, $indexId, $parameters = []) {
        $response = $this->request(
            'GET',
            "/lists/{$listId}/indexes/{$indexId}/events",
            $parameters
        );
        $response['data'] = array_map(function($datum) {
            return new Event($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch an index event
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to fetch the event for
     * @param string $eventId The ID of the event to fetch
     */
    public function fetchIndexEvent($listId, $indexId, $eventId) {
        $response = $this->request('GET', "/lists/{$listId}/indexes/{$indexId}/events/{$eventId}");
        return new Event($response);
    }

    /**
     * Update an index
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to update
     * @param array $data Data to update the index with
     */
    public function updateIndex($listId, $indexId, $data) {
        $response = $this->request('PUT', "/lists/{$listId}/indexes/{$indexId}", [], $data);
        return new Index($response);
    }

    /**
     * Delete an index
     *
     * @param string $listId The ID of the list the index is in
     * @param string $indexId The ID of the index to delete
     */
    public function deleteIndex($listId, $indexId) {
        $this->request('DELETE', "/lists/{$listId}/indexes/{$indexId}");
    }

    // ---------------------------------------------------------------------------
    // IDENTITIES
    // ---------------------------------------------------------------------------

    /**
     * Create a new identity
     *
     * @param array $data Data for the identity to create
     */
    public function createIdentity($data) {
        $response = $this->request('POST', '/identities', [], $data);
        return new Identity($response);
    }

    /**
     * Fetch a page of identities
     *
     * @param array $parameters Query parameters to include
     */
    public function fetchIdentities($parameters = []) {
        $response = $this->request('GET', '/identities', $parameters);
        $response['data'] = array_map(function($datum) {
            return new Identity($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch an identity
     *
     * @param string $identityId The ID of the identity to fetch
     */
    public function fetchIdentity($identityId) {
        $response = $this->request('GET', "/identities/{$identityId}");
        return new Identity($response);
    }

    /**
     * Fetch identity stats
     *
     * @param string $identityId The ID of the identity to fetch stats for
     * @param array $parameters Query parameters to include
     */
    public function fetchIdentityStats($identityId, $parameters = []) {
        return $this->request('GET', "/identities/{$identityId}/stats", $parameters);
    }

    /**
     * Fetch a page of events for an identity
     *
     * @param string $identityId The ID of the identity to fetch events for
     * @param array $parameters Query parameters to include
     */
    public function fetchIdentityEvents($identityId, $parameters = []) {
        $response = $this->request('GET', "/identities/{$identityId}/events", $parameters);
        $response['data'] = array_map(function($datum) {
            return new Event($datum);
        }, $response['data']);
        return $response;
    }

    /**
     * Fetch an identity event
     *
     * @param string $identityId The ID of the identity to fetch the event for
     * @param string $eventId The ID of the event to fetch
     */
    public function fetchIdentityEvent($identityId, $eventId) {
        $response = $this->request('GET', "/identities/{$identityId}/events/{$eventId}");
        return new Event($response);
    }

    /**
     * Update an identity
     *
     * @param string $identityId The ID of the identity to update
     * @param array $data Data to update the identity with
     */
    public function updateIdentity($identityId, $data) {
        $response = $this->request('PUT', "/identities/{$identityId}", [], $data);
        return new Identity($response);
    }

    /**
     * Delete an identity
     *
     * @param string $identityId The ID of the identity to delete
     */
    public function deleteIdentity($identityId) {
        $this->request('DELETE', "/identities/{$identityId}");
    }

    /**
     * Register a new identity
     *
     * @param array $data Data for the identity to register
     * @param array $identity Identity information to include in the request
     */
    public function registerIdentity($data, $identity = null) {
        $response = $this->request(
            'POST',
            '/identities/register',
            [],
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Identity($response);
    }

    /**
     * Login an identity
     *
     * @param array $data Authentication credentials to use when logging in
     * @param array $identity Identity information to include in the request
     * @return array A tuple containing the identity and an identity token
     */
    public function loginIdentity($data, $identity = null) {
        $response = $this->request(
            'POST',
            '/identities/login',
            [],
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        $this->identityGroup = $response['group'] ?? null;
        $this->identityToken = $response['token'] ?? null;
        return [new Identity(array_diff_key($response, ['token' => ''])), $this->identityToken];
    }

    /**
     * Logout an identity
     *
     * @param array $identity Identity information to include in the request
     */
    public function logoutIdentity($identity = null) {
        $this->request(
            'POST',
            '/identities/logout',
            [],
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        $this->identityGroup = null;
        $this->identityToken = null;
    }

    /**
     * Fetch the identity of the currently authenticated identity
     *
     * @param array $identity Identity information to include in the request
     */
    public function fetchSelfIdentity($identity = null) {
        $response = $this->request(
            'GET',
            '/identities/self',
            [],
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Identity($response);
    }

    /**
     * Update the currently authenticated identity
     *
     * @param array $data Data to update the identity with
     * @param array $identity Identity information to include in the request
     */
    public function updateSelfIdentity($data, $identity = null) {
        $response = $this->request(
            'PUT',
            '/identities/self',
            [],
            $data,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
        return new Identity($response);
    }

    /**
     * Delete the currently authenticated identity
     *
     * @param array $identity Identity information to include in the request
     */
    public function deleteSelfIdentity($identity = null) {
        $this->request(
            'DELETE',
            '/identities/self',
            [],
            null,
            isset($identity['ignore'])
                ? null
                : $identity['group'] ?? $this->identityGroup,
            isset($identity['ignore'])
                ? null
                : $identity['token'] ?? $this->identityToken
        );
    }
}
