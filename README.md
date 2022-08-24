# Multibyte PHP Strings
An ongoing helper library to make working with multibyte strings easier in PHP.

## Purpose
By default, the majority of PHP functions revolve around byte count instead of character count. If, for example, you want to find the position of a string stub inside a larger string that include multibyte characters (UTF-8), then the returning positions will be incorrect.

## Usage

### Finding a Stub in a String
```php

require_once __DIR__ . "/vendor/autoload.php";

$string = <<<HTML
    <title>♥ Love Your Home’s Pipes! Best Plumbing Around!</title>
HTML;

$multibyte = new MultiByteString($string);
$matches = $multibyte->findAllOccurrences("Plumbing");

// The $matches array is the same array result you would get from preg_match_all with the OFFSET flag used.
// This library corrects the string positions for you.
print("The first occurrence of {$matches[0][0]} starts at character {$matches[0][1]}");
```

### Getting a Padded Stub Result
In this case, you can get a padding string surrounding your found stub with proper care for multibyte characters.

```php

require_once __DIR__ . "/vendor/autoload.php";

$string = <<<HTML
    <title>♥ Love Your Home’s Pipes! Best Plumbing Around!</title>
HTML;

$query = "Plumbing";
$multibyte = new MultiByteString($string);
$matches = $multibyte->findAllOccurrences($query);
$firstMatch = $matches[0];
$multibyteLengthOfQuery = mb_strlen($query);

$stubResult = $multibyte->getSubStringWithPadding(
    start: $firstMatch[1],
    stubMultiByteLength: $multibyteLengthOfQuery,
    padding: 35,
);

// $stubResult is an instance of MultibyteStrings\StubResult
printf("Before: {$stubResult->beforeStub}\nStub: {$stubResult->stub}\nAfter: {$stubResult->afterStub}");
```