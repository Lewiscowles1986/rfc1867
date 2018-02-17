# rfc1867
Fresh PHP implementation of rfc1867

* Originally I had planned on using https://github.com/imiskolee/FormUpload
    * bound to CURL (we all use it, but I found the API cumbersome)
    * I wanted to be able to inject any PSR compliant Request source instead
    * Turns out it doesn't support multipart (this now passes the examples given in the RFC, I would like to make it more Robust)
    * I wanted to put a composer.json in and bundle it onto packagist

## Working with

### composer

```
composer require lewiscowles/rfc1867
composer install
composer dumpautoload
```

### dev testing

```
git clone https://github.com/Lewiscowles1986/rfc1867
cd rfc1867
composer install
composer dumpautoload
phpunit
```

### Class Diagram

![Class Relationship Diagram](diagram/class-relationship.png?raw=true "Class Relationship Diagram")

## Contributing

I'd really like to ensure this is more robust (presently it's a reference coded in a few hours)

* We need issues so that unit-test cases can be made
* We need testing with an endpoint that parses rfc1867
* We need clarification on binary encoding format and to encompass that into Attachment implementation
* We probably need to split into a few smaller projects
  * separate `NodeInterface` repo
  * separate `FormInput` and `Attachment`
* See https://www.rfc-editor.org/rfc-index.html and find related RFC's for interop & advancement
  * https://www.rfc-editor.org/rfc/rfc1867.txt
  * https://www.rfc-editor.org/rfc/rfc2854.txt
  * https://www.rfc-editor.org/rfc/rfc2388.txt
  * https://www.rfc-editor.org/rfc/rfc7578.txt
* All changes require the following
  * tests to pass
  * class-diagram to be updated (as necesarry)
  * documentation to be updated (as necesarry)

## Goals

* I'd love to be able to get a few more RFC's implemented with unit-tests.
* I'd like for this to be an alternative to reading the RFC documentation.
* I'd like to de-couple from the `guzzle/http-message` and have that only as a dev dependency for unit-tests.
