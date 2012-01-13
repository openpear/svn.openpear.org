<?php
class BinaryUtil
{
    public static function unpackLE($data, $length = 1) {
        return self::unpack('v*', $data, $length);
    }
    public static function unpackLE32($data, $length = 1) {
        return self::unpack('V*', $data, $length);
    }
    public static function unpack($format, $data, $length = 1) {
        $unpacked_data = unpack($format, $data);
        if ($length < 1) {
            return null;
        } else if ($length === 1) {
            return (isset($unpacked_data[1]) ? $unpacked_data[1] : null);
        } else {
            return array_slice($unpacked_data, 0, $length);
        }
    }
}
