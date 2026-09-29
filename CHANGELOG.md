# Changelog

## Unreleased

### Added

- Support Symfony Messenger 8, including the current stable 8.1 series, while
  retaining Symfony 6.4 and 7.x compatibility. Messenger 8.0 requires PHP 8.4 or
  later, and Messenger 8.1 requires PHP 8.4.1 or later; the library's PHP 8.2 minimum
  remains unchanged for earlier Symfony versions.
- Contract tests for command, event, and query buses and their traceable decorators:
  middleware and stamps, marker interfaces, query results, handler counts, nested
  failures, and exception identity.
- A CI matrix for PHP 8.2, 8.4, and 8.5 and Symfony Messenger 6.4, 7.0, 7.4, 8.0,
  and 8.1, plus a Docker development environment check.

### Changed

- Resolve stable development dependencies and provide `composer test`,
  `composer lint`, and `composer check` commands.
- Default the Docker development environment to PHP 8.5 with a compatible Xdebug.

### Compatibility

- No public API or exception behavior changes are required. Queries still require
  exactly one synchronous handler and return its result unchanged. Handler failures
  still expose the first original exception, including through nested Messenger
  wrappers. Middleware failures pass through unchanged.
- Message and handler marker interfaces remain local to this library. Applications
  must register handlers with Messenger (for example, using `#[AsMessageHandler]`
  or explicit configuration); the marker interfaces do not register handlers alone.
