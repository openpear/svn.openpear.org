<?php

class NorfPathUtilities
{

    const EXT_SEP = '.';

    static function pathSeparator()
    {
        $os = php_uname('s');
        if (stripos($os, 'windows') !== false)
            return '\\';
        else
            return '/';
    }

    static function lengthOfPathSeparator()
    {
        return strlen(self::pathSeparator());
    }

    static function homeDirectory()
    {
        return getenv('HOME');
    }

    static function lastPathComponent($path)
    {
        $path = self::stringByDeletingLastPathSeparator($path);
        return array_pop(self::pathComponentsBySeparatingString($path));
    }

    static function isDirectoryPath($path)
    {
        return NorfStringUtilities::hasSuffix($path, self::pathSeparator());
    }

    static function isAbsolutePath($path)
    {
        return NorfStringUtilities::hasPrefix($path, self::pathSeparator());
    }

    static function pathExtension($path)
    {
        $comp = self::lastPathComponent($path);
        if (strpos($comp, self::EXT_SEP) === false)
            return '';
        else
            return array_pop(explode(self::EXT_SEP, $comp));
    }

    static function stringByAppendingPathComponent($path, $comp)
    {
        if (!$path)
            return $comp;
        elseif (self::isDirectoryPath($path))
            if (self::isAbsolutePath($comp))
                return $path . substr($comp, 1);
            else
                return $path . $comp;
        elseif (self::isAbsolutePath($comp))
            return $path . $comp;
        else
            return $path . self::pathSeparator() . $comp;
    }

    static function stringByAppendingPathExtension($path, $ext)
    {
        $path = self::stringByDeletingLastPathSeparator($path);
        return $path . self::EXT_SEP . $ext;
    }

    static function stringsByAppendingPaths($str, $paths)
    {
        $strs = array();
        $strip = self::stringByDeletingLastPathSeparator($str);
        foreach ($paths as $path) {
            if (!$path or self::pathSeparator() === $path)
                $strs[] = $str;
            elseif ($path[0] === self::pathSeparator())
                $strs[] = $strip . $path;
            else
                $strs[] = $str . self::pathSeparator() . $path;
        }
        return $strs;
    }

    static function stringByDeletingLastPathComponent($path)
    {
        if (self::pathSeparator() === $path)
            return $path;
        else {
            $path = self::stringByDeletingLastPathSeparator($path);
            $comps = self::pathComponentsBySeparatingString($path);
            array_pop($comps);
            return self::stringByJoiningPathComponents($comps);
        }
    }

    static function stringByDeletingPathExtension($path)
    {
        if (($i = strrpos($path, self::EXT_SEP)) !== false) {
            return substr($path, 0, $i);
        } elseif (self::pathSeparator() === $path)
            return $path;
        else
            return self::stringByDeletingLastPathSeparator($path);
    }

    static function stringByDeletingLastPathSeparator($path)
    {
        if (self::isDirectoryPath($path))
            return substr($path, 0, strlen($path) - self::lengthOfPathSeparator());
        else
            return $path;
    }

    static function stringByJoiningPathComponents($comps)
    {
        if (count($comps) === 1 and $comps[0] === '')
            return self::pathSeparator();
        else
            return join(self::pathSeparator(), $comps);
    }

    static function pathComponentsBySeparatingString($path)
    {
        return split(self::pathSeparator(), $path);
    }

    static function stringByNormalizingPath($path)
    {
        $path = preg_replace('/\A\.\./', dirname(getcwd()), $path);
        $path = preg_replace('/\A\./', getcwd(), $path);
        while (true) {
            $path = preg_replace('/[^\/]+\/\.\.\//', '', $path, -1, $count);
            if (!$count)
                break;
        }
        while (true) {
            $path = preg_replace('/\.\//', '', $path, -1, $count);
            if (!$count)
                break;
        }
        return $path;
    }

    static function relativePathWithPath($path, $cwd=null)
    {
        $path = realpath($path);
        if ($cwd === null)
            $cwd = getcwd();

        if (NorfStringUtilities::hasPrefix($path, $cwd)) {
            $path = substr($path, strlen($cwd));
            if ($path[0] == self::pathSeparator())
                return substr($path, 1);
            else
                return $path;
        } else
            return null;
    }
        
    static function scriptExistsAtPath($path)
    {
        $path = preg_replace('/\//', self::pathSeparator(), $path);
        $incPaths = explode(':', get_include_path());
        foreach ($incPaths as $incPath) {
            $scriptPath = self:: stringByAppendingPathComponent
                ($incPath, $path);
            if (file_exists($scriptPath))
                return true;
        }
        return false;
    }

}

