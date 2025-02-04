<?php

namespace App\Traits\Enums;

trait EnumTrait
{
    /**
     * Retorna um array com os valores dos enums no formato [valor => label]
     */
    public static function options(): array
    {
        foreach (self::cases() as $value) {
            $array[$value->value] = $value->getLabel();
        }

        return $array ?? [];
    }

    public static function random(): self
    {
        return self::cases()[array_rand(self::cases(), 1)];
    }
}
