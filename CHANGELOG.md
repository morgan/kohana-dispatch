# 0.2.0

- Created new `Dispatch_Connection` class for reusing core request configuration across requests.
- Refactored `Dispatch::factory` and `Dispatch_Request` to take connections into consideration.
- Updated unit tests to use `Dispatch_Connection::factory`. 
- All tests pass "OK (7 tests, 18 assertions)"

# 0.1.0 - 10/23/2011

- Initial release of Dispatch
- Support for internal or external requests
- Internal requests have an option for failover to external
- User Guide documentation
- Unit Test coverage with sample `Controller_Dispatch_Test` reflection service
- Internal requests support pass-through allowing for access to Response body before being casted 
to a string. This prevents unneeded encoding and decoding.