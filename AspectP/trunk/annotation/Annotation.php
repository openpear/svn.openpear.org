<?php
require_once '../exception/AspectException.php';
/*
 * Annotation
 *
 * @package annotation
 * @author  localdisk <smoochyinfo@gmail.com>
 * @author  devworks  <smoochynet@gmail.com>
 * @access  public
 * @version Release:  0.10.0
 */
class Annotation {
    const INTERCEPTER = 'intercepter';
    const ANNOTATION  = 'annotation';
    /**
     * アノテーションを取得します
     *   アノテーションサンプル
     *   @Aspect('intercepter' => 'LoggerIntercepter')
     *   指定されたインターセプターのインスタンスを返します。
     * @param  ReflectionClass|ReflectionMethod|ReflectionProperty $ref
     * @param  string                                              $key
     * @return array
     */
    public static function getAnnotation($ref, $key) {
        $comment = self::formatComment(self::getComment($ref));
        $ret = array();
        foreach ($comment as $c) {
            if (!preg_match("/@$key\s*/", $c, $matches)) {
                continue;
            }
            $annotation = self::newInstance($key, self::ANNOTATION);
            if (preg_match('/\((.*?\))/', $c, $matches)) {
                $expression = self::formatExpression(trim($matches[0]));
                if (array_key_exists(self::INTERCEPTER, $expression)) {
                    $interCepter = self::newInstance($expression[self::INTERCEPTER], self::INTERCEPTER);
                    $annotation->setInterCepters($interCepter, $ref->getName());
                } else {
                    throw new AspectException('intercepter is required.');
                }
            } else {
                throw new AspectException('intercepter is required.');
            }
            $ret[] = $annotation;
        }
        return $ret;
    }

    /**
     * DocCommentを取得します
     * 
     * @param ReflectionClass|ReflectionMethod|ReflectionProperty $ref
     * @return string
     */
    public static function getComment($ref) {
        return $ref->getDocComment();
    }

    /**
     * コメントをフォーマットします
     * 
     * @param  string $commentLine
     * @return array
     */
    public static function formatComment($commentLine) {
        $comments = preg_split('/[\n\r]/', $commentLine, -1, PREG_SPLIT_NO_EMPTY);
        $count = count($comments);
        $annotations = array();
        foreach ($comments as $comment) {
            $pos = strpos($comment, '@');
            if ($pos !== false) {
                $annotations[] = trim(substr($comment, $pos));
            }
        }
        return $annotations;
    }

    /**
     * アノテーションで指定されたオブジェクトのインスタンスを作成します
     *
     * @param  string $name
     * @param  string $path
     * @return object
     */
    public static function newInstance($name, $path) {
        if (class_exists($name, false)) {
            return new $name;
        }
        $fileName = '../' . $path . '/' . ucfirst($name) . '.php';
        if (file_exists($fileName) && is_readable($fileName)) {
            include_once $fileName;
            if (class_exists($name, false)) {
                return new $name;
            }
        }
        throw new AspectException('class not found.');
    }

    /**
     * 配列を作成します
     *
     * @param  string $src
     * @return array
     */
    public static function formatExpression($src) {
        $src = 'return array' . $src . ';';
        return eval($src);
    }
}