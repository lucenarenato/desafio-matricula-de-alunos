<?php


namespace App\Enums;


enum CursoTypes: string
{
    case Online = "On-line";
    case InPerson = "Presencial";

    public  static function fromValue(string $name): string
    {
        foreach (self::cases() as $types) {
            if ($name === $types->name) {
                return $types->value;
            }
        }

        throw new \ValueError("$types is not a valid");
    }
}
