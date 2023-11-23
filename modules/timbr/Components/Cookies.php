<?php

namespace TimBR\Components;

final class Cookies
{
    public static function toCurlFormat(string $input): string
    {
        $arrayCookies     = self::extractFromString($input);
        $extractedCookies = array_map(function ($cookie) {
            return trim($cookie['name']) . '=' . trim($cookie['value']);
        }, $arrayCookies);
        $formatted2       = implode(';', $extractedCookies);
        return utf8_encode($formatted2);
    }

    public static function extractFromString(string $string): array
    {
        $lines   = explode(PHP_EOL, $string);
        $cookies = [];
        foreach ($lines as $line) {
            $cookie = array();
            if (substr($line, 0, 10) == '#HttpOnly_') {
                $line               = substr($line, 10);
                $cookie['httponly'] = true;
            } else {
                $cookie['httponly'] = false;
            }
            if (strlen($line) > 0 && $line[0] != '#' && substr_count($line, "\t") == 6) {
                $tokens = explode("\t", $line);

                $tokens = array_map('trim', $tokens);

                $cookie['domain'] = $tokens[0];
                $cookie['flag']   = $tokens[1];
                $cookie['path']   = $tokens[2];
                $cookie['secure'] = $tokens[3];

                $cookie['expiration-epoch'] = $tokens[4];
                $cookie['name']             = $tokens[5];
                $cookie['value']            = $tokens[6];

                $cookie['expiration'] = date('Y-m-d h:i:s', $tokens[4]);

                $cookies[] = $cookie;
            }
        }

        return array_filter($cookies);
    }
}
