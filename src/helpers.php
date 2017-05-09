 <?php

function arr_add($array, $key, $value)
{
    if (is_null(get($array, $key)))
    {
        set($array, $key, $value);
    }

    return $array;
}

function arr_get($array, $key, $default = null)
{
    if (is_null($key)) return $array;

    if (isset($array[$key])) return $array[$key];

    foreach (explode('.', $key) as $segment)
    {
        if ( ! is_array($array) || ! array_key_exists($segment, $array))
        {
            return val($default);
        }

        $array = $array[$segment];
    }

    return $array;
}

function val($value)
{
    return $value instanceof Closure ? $value() : $value;
}