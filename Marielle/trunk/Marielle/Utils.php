<?php
/**
 * Marielle フレームワークで使用されるユーティリティクラス
 *
 * @author takeshi kawamoto <kawamoto@digitalize.co.jp>
 * @package Marielle
 */

class Marielle_Utils
{

    /**
     * ファイルが存在するかどうかをチェックします
     * @param string $path チェックするファイルのパス
     * @return boolean ファイルが存在する場合はTrue、存在しない場合はFalse
     */
    public static function FileExists($path) {
        if (file_exists($path)) {
            return true;
        }

        $include = explode(PATH_SEPARATOR, get_include_path());
        foreach ($include as $dir) {
          if (file_exists(Marielle_Utils::PathCombine($dir,$path))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 2つのパス文字列を結合します
     * @param string $path1 パス文字列
     * @param string $path2 パス文字列
     * @return string 結合されたパス文字列
     */
    public static function PathCombine($path1, $path2) {
        $path = $path1;

        if (!preg_match('/(\/|\\\\)$/', $path)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= $path2;

        return $path;
    }

    /**
     * 配列の値のない項目を削除します
     * @param Array $array 対象の配列
     * @return Array 値のない項目を削除した配列
     */
    public static function ArrayTrim($array) {
        $new = array();
        $nid = 0;

        foreach ($array as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if (preg_match('/^[0-9]+$/', $key)) {
                $new[$nid] = $value;
                $nid++;
            } else {
                $new[$key] = $value;
            }
        }

        return $new;
    }

    /**
     * インクルードパスを追加します
     * @param string $path 追加するパス
     */
    public static function AddIncludePath() {
        $args = func_get_args();
        $exist = explode(PATH_SEPARATOR, get_include_path());

        $setpath = '';
        foreach($args as $path) {
            if (!in_array($path, $exist)) {
                if ($setpath !== '') {
                    $setpath .= PATH_SEPARATOR;
                }
                $setpath .= $path;
            }
        }

        if ($setpath !== '') {
            $setpath .= PATH_SEPARATOR . get_include_path();
            set_include_path($setpath);
        }
    }

    /**
     * クラス名を指定してクラスファイルを読み込みます
     * @param string $name クラス名
     */
    public static function classFileLoader($name)
    {
        // 既にクラスが定義されている場合は処理を行わない
        if (class_exists($name)) return true;

        $path = implode(DIRECTORY_SEPARATOR, explode('_', $name)) . '.php';
        if (Marielle_Utils::FileExists($path)) {
            // クラスファイルを読み込む
            require_once($path);
            return true;
        } else {
            // クラスファイルが見つからなかった場合
            return false;
        }
    }

    /**
     * 引数の値を表示します
     */
    public static function Debug() {
        $args = func_get_args();

        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
            echo '<hr />';
        }
        echo '</pre>';

    }
}