<?php

declare(strict_types = 1);

namespace UserSystem\Layton;

class PasswordUtils {

    private static array $hashPasswords = [];

    public static function getHashPassword(string $password): string {
        return md5($password);
    }

    public static function verifyPassword(string $password, string $hashString): bool {
        return md5($password) === $hashString;
    }

    public static function addHashPassword(string $hashString): void {
        self::$hashPasswords[] = $hashString;
    }

    public static function checkString(string $checkString): string {
        $checkWords = explode(" ", $checkString);

        $words = [];
        foreach ($checkWords as $checkWord) {
            if (in_array(md5($checkWord), self::$hashPasswords)) {
                for ($i = 0; $i < strlen($checkWord); $i++) {
                    $checkWord[$i] = "*";
                }
            }

            $words[] = $checkWord;
        }

        return implode(" ", $words);
    }

}