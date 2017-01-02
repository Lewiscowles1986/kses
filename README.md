# kses
Port of KSES for PHP7 re-licensed under AGPLv3

## Background

Looking through WordPress code-base to clean it up, play with it over holidays I want to take some of the libs and update them for more modern PHP workflow

## Credit

 * WordPress project http://wordpress.org
 * Original KSES http://sourceforge.net/projects/kses/

## Changes

 * Incompatible API (this is a stripped KSES from WordPress Core)
  * More meaningful method names (for some reason someone chose to obfuscate the names in WP core)
  * More tests (Aim for 100% coverage)
  * Class-based (should make it safer to use badly)

## Downloading

 ```shell
 composer require lewiscowles1986/kses
 ```
 
## Testing

 ```shell
 git clone https://github.com/lewiscowles1986/kses
 composer install
 composer test
 ```

## Contributing
 * raise issue
 * fork
 * run tests & add tests if new functionality
 * submit PR

