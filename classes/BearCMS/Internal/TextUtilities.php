<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

/**
 * @internal
 * @codeCoverageIgnore
 */
class TextUtilities
{

    /**
     * 
     * @param string $string
     * @return int
     */
    static function strlen(string $string): int
    {
        return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
    }

    /**
     * 
     * @param string $string
     * @param integer $start
     * @param integer|null $length
     * @return string
     */
    static function substr(string $string, int $start, ?int $length = null): string
    {
        return function_exists('mb_substr') ? mb_substr($string, $start, $length) : substr($string, $start, $length);
    }

    /**
     * 
     * @param string $string
     * @return string
     */
    static function strtolower(string $string): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
    }

    /**
     * 
     * @param string $html
     * @return string
     */
    static function htmlToText(string $html): string
    {
        $result = $html;
        $result = preg_replace('/<script.*?<\/script>/s', '', $result);
        $result = preg_replace('/<.*?>/', ' $0 ', $result);
        $result = preg_replace('/\s/u', ' ', $result);
        $result = strip_tags($result);
        while (strpos($result, '  ') !== false) {
            $result = str_replace('  ', ' ', $result);
        }
        $result = html_entity_decode(trim($result));
        return trim($result);
    }

    /**
     * 
     * @param string $text
     * @param integer $length
     * @return string
     */
    static function cropText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        $text = self::substr($text, 0, $length);
        $position = strrpos($text, " ");
        if ($position > 0) {
            $text = self::substr($text, 0, $position);
        }
        $text .= " ...";
        return $text;
    }

    /**
     * 
     * @param string $text
     * @return array
     */
    static function getKeywords(string $text): array
    {
        $wordsText = preg_replace("/[^[:alnum:][:space:]]/u", '', self::strtolower($text));
        $words = explode(' ', $wordsText);
        $wordsCount = array_count_values($words);
        arsort($wordsCount);
        $selectedWords = [];
        foreach ($wordsCount as $word => $wordCount) {
            $wordLength = self::strlen($word);
            if ($wordLength >= 3 && !is_numeric($word)) {
                $selectedWords[] = $word;
                if (count($selectedWords) === 10) {
                    break;
                }
            }
        }
        return $selectedWords;
    }
}
