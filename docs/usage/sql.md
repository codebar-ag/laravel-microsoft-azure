# SQL

Azure SQL server/database ARM management, firewall rules, and AAD-token auth for connecting to a database. All ARM-scoped except the token audience noted below.

## Servers and databases

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance()->sqlServers($subscriptionId, 'my-rg')->list();

Azure::instance()->sqlServers($subscriptionId, 'my-rg')->createOrUpdate(
    serverName: 'my-sql-server',
    location: 'westeurope',
    administratorLogin: 'sqladmin',
);

Azure::instance()->sqlDatabases($subscriptionId, 'my-rg', 'my-sql-server')->createOrUpdate(
    database: 'appdb',
    location: 'westeurope',
);

Azure::instance()->sqlDatabases($subscriptionId, 'my-rg', 'my-sql-server')->get('appdb');
Azure::instance()->sqlDatabases($subscriptionId, 'my-rg', 'my-sql-server')->delete('appdb');
```

## Firewall rules

```php
Azure::instance()->sql($subscriptionId, 'my-rg', 'my-sql-server')->createOrUpdate(
    ruleName: 'allow-app-service',
    startIpAddress: '20.50.10.1',
    endIpAddress: '20.50.10.1',
);

Azure::instance()->sql($subscriptionId, 'my-rg', 'my-sql-server')->delete('allow-app-service');
```

## Connecting with AAD (Entra) token auth

A `Sql` token audience (`https://database.windows.net/.default`) was added for Azure SQL AAD-token authentication, for apps that connect to the database itself (not just manage it via ARM) using a token instead of a SQL login/password. This package issues the token; using it to open a database connection (e.g. via `pdo_sqlsrv`/`sqlsrv`) is the consuming app's responsibility — this package doesn't ship a SQL client.

> **Note (needs maintainer input):** this token audience shipped in v0.4.0 with no accompanying example of driving a database connection with it — the exact token-acquisition call to expose for this purpose (as opposed to the ARM management calls above) isn't yet documented anywhere in the repo.
