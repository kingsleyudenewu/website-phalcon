<?php

/**
 * Get the path to a versioned Elixir file.
 *
 * @param  string $file
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function elixir($file)
{
    static $manifest = null;

    if (is_null($manifest))
    {
        $manifest = json_decode(file_get_contents(laravelPath('public/build/rev-manifest.json')), true);
    }

    if (isset($manifest[$file]))
    {
        return getenv('ROOT_URL') . '/build/' . $manifest[$file];
    }

    throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
}

function laravelPath($path)
{
    return getenv('LARAVEL_PATH') . '/' . $path;
}