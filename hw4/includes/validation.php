
<?php

function validateCardName(string $value): ?string
{
    if ($value === '') {
        return 'Card Name is required.';
    }

    $length = preg_match_all('/./us', $value);

    if ($length === false) {
        return 'Card Name must contain valid text.';
    }

    if ($length > 100) {
        return 'Card Name must be no more than 100 characters.';
    }

    return null;
}

function validateCardType(string $value): ?string
{
    if ($value === '') {
        return 'Card Type is required.';
    }

    $length = preg_match_all('/./us', $value);

    if ($length === false) {
        return 'Card Type must contain valid text.';
    }

    if ($length > 100) {
        return 'Card Type must be no more than 100 characters.';
    }

    return null;
}

function validateCardValue(string $value): ?string
{
    if (
        $value === ''
        || !is_numeric($value)
        || !is_finite((float) $value)
        || (float) $value <= 0
    ) {
        return 'Card Value must be a positive number.';
    }

    return null;
}

function validatePointsRequired(string $value): array
{
    $points = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($points === false) {
        return [
            'value' => null,
            'error' => 'Points Required must be a positive whole number.',
        ];
    }

    return [
        'value' => $points,
        'error' => null,
    ];
}

function validateCardId($value)
{
    if (!is_string($value)) {
        return false;
    }

    return filter_var(
        $value,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );
}
