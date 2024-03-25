# Symfony 7 API Template

An extremely opinion-heavy Symfony 7 template designed for an API-only service.

The domain's deliberately trivial so it's easy to rip out and put actual code in.

## Things I Find Important

- Maintainability, first and foremost - everything else is in service of this
- Strict type everything - type bugs are awful, stop them happening
- Contract-first design - code should always work to the OpenAPI spec 
- Separate layers into:
  - Infrastructure - the outside ports into the system such as HTTP Controllers, queue messages, etc.
  - Domain - the business logic
  - Application - commands and queries fired from infrastructure to affect the domain
- Test pyramid - Unit tests at a class level, Integration at a few classes (normally handlers), Functional for the full flow

## Tools, And Why

#### Dockerisation & Deployment

This is using [FrankenPHP](https://frankenphp.dev/) as a base for ease. If I was using this in a team/company I'd likely change this to meet the companies deployment plans.

#### Code Standards

My general philosophy on code standards is that the important part is having a standard, not what the standard is. To that end I've stuck closely to pre-existing standards, which has a few benefits:

- Less thinking about standards
- Less arguing about standards
- Less caring about standards
- Easy to change to another standard if you accidentally do any of those things

Enforced by [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) using the Symfony and Symfony:risky standards.

#### Code Quality

To further standardise the code I'm using [rectorphp](https://github.com/rectorphp/rector) with a ruleset to auto-upgrade to the latest PHP features.

This is a bit of automated maintenance - as Rector improves and standardises more approaches we can easily apply this across the whole codebase, or upgrade to a newer PHP version in a consistent manner.

#### Strict Typing

Now that PHP has mostly-great support for strict types I want to enforce using them everywhere. Benefits:

- Better design - you're forced to think about types up front
- No worrying about random type coercion issues

[PHPStan](https://phpstan.org/) is my preference here - Psalm is good, but the community around PHPStan is leagues ahead. Extensions solve most of the common issues for free, it's easy to get support, and bug fixes are welcome and appreciated.

I'm running it at the max level (strict types are good! we want as much as possible!) with these extensions:
- [phpstan/phpstan-strict-rules](https://github.com/phpstan/phpstan-strict-rules)
- [phpstan/phpstan-symfony](https://github.com/phpstan/phpstan-symfony)
- [phpstan/phpstan-doctrine](https://github.com/phpstan/phpstan-doctrine) 
- [phpstan/phpstan-phpunit](https://github.com/phpstan/phpstan-phpunit)
- [phpstan/phpstan-mockery](https://github.com/phpstan/phpstan-mockery)

#### API Documentation

API Documentation should be written up front when building an API. This is the key point of contact between you and your users (even if you're building an internal API) and it's important to get it right. It also lets you work on API consumers before the API is actually implemented, which is a nice bonus.

[OpenAPI](https://www.openapis.org/what-is-openapi) 3.1 is the gold standard here, with [loads of tools](https://openapi.tools/) supporting it.

My contract's stored at `/public/openapi.json`. Some notes:

- Errors are using [RFC 7807](https://datatracker.ietf.org/doc/html/rfc7807) which isn't yet accepted but is widely in use
- Each response body uses a top level `data` key to wrap all the data to allow for possible expansions like `metadata` or `pagination` where needed
- `snake_case` for paths, `camelCase` for properties because standards are good

Every functional test checks our request/response format against this API documentation ([gertjuhh/symfony-openapi-validator](https://github.com/gertjuhh/symfony-openapi-validator)) to make sure we're actually following it.

#### Layering

Infrastructure, Application & Domain are kept separate both by convention and by tooling. This ensures our Domain only concerns with business logic and not database access, response handling, API calling etc.

[deptrac](https://github.com/qossmic/deptrac) is configured so each layer can only use lower layers, ie:
- Infrastructure can use Application and Domain layer classes
- Application can use Domain
- Domain cannot use any external layers
- There's also protection on Data and Test being used in any layer, just in case

#### Command-Query Responsibility Separation

[Tactician](league/tactician-bundle) is my preference for command bus as it's very straightforward and doesn't try to do too much out the box.

There's two separate busses configured:
- Command for anything changing state - this uses a database rollback middleware, and a validator middleware
- Query for retreiving data - this only has a validator middleware

These are manually wired in `commands.yaml` and `queries.yaml` to only be runnable on the correct bus.

#### Testing

[PHPUnit](https://phpunit.de/index.html) for all the things! Except mocking cause it's bad at that! Where mocks are needed I prefer [mockery](https://github.com/mockery/mockery).

General approach to testing is:
- Every entrance (HTTP endpoint, queue, console command) gets **at least** one happy path and one sad path functional test
- Handlers and Repositories get heavy Integration testing
- Unit tests wherever makes sense - don't test type mismatches, or classes with no real logic - and to as many boundaries as possible

#### Misc

- [Monolog](https://github.com/symfony/monolog-bundle) for logging so we can pass it to stdout,stderr, or the moon as needed
- [DoctrineFixturesBundle](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html) to manage database changes
  - Never write a "down" migration! Follow [expand-contract pattern](https://martinfowler.com/bliki/ParallelChange.html) so you can roll back if a release goes to hell
- [jms/serializer-bundle](https://github.com/schmittjoh/JMSSerializerBundle) handles serializing objects into our API responses
  - This totally separates our API presentation layer from our Domain layer
  - JMS is great as it can use either properties or getters at the same time, meaning zero concessions needed in our Domain objects (Symfony Serializer needs a getter for each value, for example)
- [StofDoctrineExtensionsBundle](https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html) auto-updating our updatedAt timestamps on saving an Entity
- RequestDtos are used to validate initial HTTP requests, and Command/Query messages are also validated before being ran

## Core Design Influences

- [The Twelve-Factor App](https://12factor.net/)
- Ports & Adapters architecture - Mattias Noback's writeup is good ([Part 1](https://matthiasnoback.nl/2017/07/layers-ports-and-adapters-part-1-introduction/), [Part 2](https://matthiasnoback.nl/2017/08/layers-ports-and-adapters-part-2-layers/), [Part 3](https://matthiasnoback.nl/2017/08/layers-ports-and-adapters-part-3-ports-and-adapters/))
- [Build APIs You Won't Hate](https://www.amazon.co.uk/dp/0692232699)
- Domain Driven Design - [the blue book](https://www.domainlanguage.com/ddd/blue-book/) or a thousand articles summarising it

## Things Not Done

- There's no auth implemented - ideally use an external auth service to integrate with
- No queueing functionality
- Should write an [Architecture Decision Record](https://github.com/joelparkerhenderson/architecture-decision-record) rather than this blob of text
- Having to manually wire CQRS is annoying, should write a compiler pass to do this by annotation
