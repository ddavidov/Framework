<?php

namespace Zoolanders\System\Language;

/**
 * Class Language
 * @package Zoolanders\System\Language
 */
class Language
{
    /**
     * Proxy function calls to JLanguage
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([\JFactory::getLanguage(), $name], $arguments);
    }

    /**
     * Translate a string into the current language
     *
     * @param string $string The string to translate
     * @param booolean $js_safe If the string should be made js safe (default: true)
     *
     * @return string The translated string
     *
     * @since 1.0.0
     */
    public function l($string, $js_safe = false) {
        return $this->_($string, $js_safe);
    }
}