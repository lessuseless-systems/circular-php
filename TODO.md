# PHP SDK - TODO

## Missing Utility Methods

Add the following utility/configuration methods to match Python and Dart SDKs:

### Configuration Methods
- [x] `setNagUrl(string $url): void` - Update NAG endpoint URL at runtime
- [x] `getNagUrl(): string` - Get current NAG endpoint URL
- [x] `setNagKey(string $key): void` - Update NAG API key at runtime
- [x] `getNagKey(): string` - Get current NAG API key
- [x] `setHeader(string $key, string $value): void` - Set custom HTTP headers
- [x] `getVersion(): string` - Get SDK version

### Error Handling Methods
- [x] `getError(): string` - Get last error message from SDK
- [x] `handleError(\Throwable|string $error): void` - Handle API error responses

### Lifecycle Methods
- [x] `dispose(): void` - Clean up resources (HTTP client, etc.)

### Additional Utility Methods
- [x] `setNode(string $address): void` - Set primary node address for querying blockchain

## Notes
- ~~PHP SDK needs comprehensive alignment with other SDKs~~
- ~~Adding these 10 methods will bring PHP to full feature parity~~
- ~~These methods enable runtime configuration and better error handling~~
- **COMPLETED**: All 10 utility methods have been implemented in v1.0.9
- PHP SDK now has full feature parity with Python and Dart SDKs
