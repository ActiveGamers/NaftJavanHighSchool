<?php

use App\Support\PersianDigits;

test('the latin digits of a number are printed with persian digits', function (string|int|float|null $value, string $expected) {
    expect(PersianDigits::toPersian($value))->toBe($expected);
})->with([
    'a year' => [1403, '۱۴۰۳'],
    'a count with a unit' => ['7 مورد', '۷ مورد'],
    'a decimal' => [12.5, '۱۲.۵'],
    'nothing' => [null, ''],
]);

test('the persian and the arabic-indic digits of a typed value become latin', function () {
    expect(PersianDigits::toLatin('۰۹۱۲٣٤٥'))->toBe('0912345');
});
