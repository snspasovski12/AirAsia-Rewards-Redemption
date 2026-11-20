
<?php

require_once dirname(__DIR__) . '/includes/validation.php';

$tests = [
    [
        'name' => 'Valid Gift Card data passes validation',
        'expected' => [
            'cardName' => null,
            'cardType' => null,
            'cardValue' => null,
            'points' => [
                'value' => 500,
                'error' => null,
            ],
        ],
        'run' => static function (): array {
            return [
                'cardName' => validateCardName('AirAsia Gift Card'),
                'cardType' => validateCardType('Digital'),
                'cardValue' => validateCardValue('25.50'),
                'points' => validatePointsRequired('500'),
            ];
        },
    ],
    [
        'name' => 'Blank Card Name fails validation',
        'expected' => 'Card Name is required.',
        'run' => static fn () => validateCardName(''),
    ],
    [
        'name' => 'Card Type over 100 characters fails validation',
        'expected' => 'Card Type must be no more than 100 characters.',
        'run' => static fn () => validateCardType(str_repeat('T', 101)),
    ],
    [
        'name' => 'Zero Card Value fails validation',
        'expected' => 'Card Value must be a positive number.',
        'run' => static fn () => validateCardValue('0'),
    ],
    [
        'name' => 'Negative Card Value fails validation',
        'expected' => 'Card Value must be a positive number.',
        'run' => static fn () => validateCardValue('-10'),
    ],
    [
        'name' => 'Zero Points Required fails validation',
        'expected' => [
            'value' => null,
            'error' => 'Points Required must be a positive whole number.',
        ],
        'run' => static fn () => validatePointsRequired('0'),
    ],
    [
        'name' => 'Fractional Points Required fails validation',
        'expected' => [
            'value' => null,
            'error' => 'Points Required must be a positive whole number.',
        ],
        'run' => static fn () => validatePointsRequired('10.5'),
    ],
    [
        'name' => 'Positive integer card_id passes validation',
        'expected' => 42,
        'run' => static fn () => validateCardId('42'),
    ],
    [
        'name' => 'Zero card_id fails validation',
        'expected' => false,
        'run' => static fn () => validateCardId('0'),
    ],
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    try {
        $actual = ($test['run'])();
        $matches = $actual === $test['expected'];
    } catch (Throwable $exception) {
        $actual = get_class($exception) . ': ' . $exception->getMessage();
        $matches = false;
    }

    if ($matches) {
        $passed++;
        printf("PASS: %s\n", $test['name']);
        continue;
    }

    $failed++;

    printf("FAIL: %s\n", $test['name']);
    printf("  Expected: %s\n", var_export($test['expected'], true));
    printf("  Actual:   %s\n", var_export($actual, true));
}

printf("\nTotal: %d passed, %d failed\n", $passed, $failed);

exit($failed === 0 ? 0 : 1);
