<?php

/**
 * @deprecated Use html() instead. This class will be removed in future versions.
 */
final class Form
{
    public static function open($parameters = [])
    {
        $string = '';
        foreach ($parameters as $key => $value) {
            if ($key === 'route') {
                if (\is_array($value)) {
                    $string .= \sprintf('action="%s" ', \route($value[0], $value[1]));
                } else {
                    //$string .= \sprintf('action="%s" ', \url('/'));
                    $string .= \sprintf('action="%s" ', \route($value));
                }
                continue;
            }

            if ($key === 'method') {
                if (\strtolower($value) !== 'get') {
                    $string .= 'method="post" ';
                    continue;
                }
            }

            if ($key === 'files') {
                $string .= 'enctype="multipart/form-data" ';
                continue;
            }

            $string .= \sprintf('%s="%s" ', $key, e($value));
        }

        $form = \sprintf('<form %s>', $string);

        $method = \strtolower($parameters['method']) ?? 'get';
        if ($method !== 'get') {
            $form .= \csrf_field();
        }
        if (\in_array($method, ['put', 'patch', 'delete'])) {
            $form .= \method_field($method);
        }

        return $form;
    }

    public static function close()
    {
        return '</form>';
    }

    public static function label($for = null, $contents = null)
    {
        return \html()->label($contents, $for)->class('form-label');
    }

    public static function text($name = null, $value = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->text($name, $value)->attributes($attributes);
    }

    public static function password($name = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->password($name)->attributes($attributes);
    }

    public static function email($name = null, $value = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->email($name, $value)->attributes($attributes);
    }

    public static function number($name = null, $value = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->number($name, $value)->attributes($attributes);
    }

    public static function textarea($name = null, $value = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->textarea($name, $value)->attributes($attributes);
    }

    public static function file($name = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        return \html()->file($name)->attributes($attributes);
    }

    public static function select($name = null, $options = [], $value = null, $attributes = [])
    {
        $attributes = self::sanitizeAttributes($attributes);

        $options = (array)$options;

        if (isset($attributes['placeholder'])) {
            $options = ['' => $attributes['placeholder']] + $options;

            unset($attributes['placeholder']);
        }

        if (isset($attributes['multiple'])) {
            unset($attributes['multiple']);

            return \html()->multiselect($name, $options, $value)->attributes($attributes);
        }

        return \html()->select($name, $options, $value)->attributes($attributes);
    }

    public static function submit($text = null, $attributes = [])
    {
        return \html()->submit($text)->attributes($attributes);
    }

    private static function sanitizeAttributes($attributes = [])
    {
        if (isset($attributes['required'])) {
            if ($attributes['required'] !== false) {
                $attributes['required'] = true;
            } else {
                unset($attributes['required']);
            }
        }

        return $attributes;
    }
}
