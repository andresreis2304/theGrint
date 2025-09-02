<?php
namespace App\Domain\Ads;

final class Condition
{
    // canonical values stored in DB (Spanish)
    public const NUEVO = 'nuevo';
    public const USADO = 'usado';
    public const RESTAURADO = 'restaurado';
    public const COMO_NUEVO = 'como_nuevo';

    private static array $inputMap = [
        'new' => self::NUEVO, 'nuevo' => self::NUEVO,
        'used' => self::USADO, 'usado' => self::USADO,
        'refurbished' => self::RESTAURADO, 'restaurado' => self::RESTAURADO, 'reacondicionado' => self::RESTAURADO,
        'like_new' => self::COMO_NUEVO, 'como_nuevo' => self::COMO_NUEVO, 'como nuevo' => self::COMO_NUEVO,
    ];

    private static array $toEnglish = [
        self::NUEVO => 'new',
        self::USADO => 'used',
        self::RESTAURADO => 'refurbished',
        self::COMO_NUEVO => 'like_new',
    ];

    public static function normalize(string $raw): string {
        $k = mb_strtolower(trim($raw));
        return self::$inputMap[$k] ?? $k;
    }
    public static function toEnglish(string $stored): string {
        return self::$toEnglish[$stored] ?? $stored;
    }
}
