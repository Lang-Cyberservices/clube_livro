<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! class_exists('Locale')) {
    class Locale
    {
        private static string $default = 'pt-BR';

        public static function getDefault(): string
        {
            return self::$default;
        }

        public static function setDefault(string $locale): bool
        {
            self::$default = $locale;

            return true;
        }
    }
}
