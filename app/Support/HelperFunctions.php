<?php
function obfuscate_email(?string $email = null): string
{
    if (is_null($email)) {
        return '';
    }
    $emailSplit = explode('@', $email);

    if (count($emailSplit) !== 2) {
        return $email;
    }

    $firstPart = $emailSplit[0];
    $lastPart  = $emailSplit[1];

    $obfuscatedFirstPart = obfuscate($firstPart);
    $obfuscatedLastPart  = obfuscate($lastPart, true);

    return $obfuscatedFirstPart . '@' . $obfuscatedLastPart;
}

function obfuscate(string $string, bool $invert = false): string
{
    $numberOfObfuscatedChars = (int)floor(strlen($string) * 0.75);
    $numberOfUnobscuredChars = strlen($string) - $numberOfObfuscatedChars;

    if ($invert) {
        return str_repeat('*', $numberOfObfuscatedChars) . substr($string, $numberOfUnobscuredChars * -1, $numberOfUnobscuredChars);
    }

    return substr($string, 0, $numberOfUnobscuredChars) . str_repeat('*', $numberOfObfuscatedChars);
}
