<?php
/*
 * Advice
 *
 * @package advice
 * @author  localdisk <smoochyinfo@gmail.com>
 * @author  devworks  <smoochynet@gmail.com>
 * @access  public
 * @version Release:  0.10.0
 */

/**
 * BeforeAdvice
 * Joinpointの実行前に呼び出されるアドバイス
 */
interface BeforeAdvice {
    /**
     * Joinpointの実行前に呼び出します
     * 
     * @param object           $target
     * @param ReflectionMethod $method
     * @param array            $args
     * @@return                void
     */
    public function before($target, ReflectionMethod $method = null, array $args = array());
}
/**
 * AfterAdvice
 * Joinpointの実行後に呼び出されるアドバイス
 */
interface AfterAdvice {
    /**
     * Joinpointの実行後に呼び出します
     *
     * @param mixed            $returnValue
     * @param object           $target
     * @param ReflectionMethod $method
     * @param array            $args
     * @return                 void
     */
    public function after($returnValue, $target, ReflectionMethod $method = null, array $args = array());
}
/**
 * AfterAdvice
 * Joinpointの実行前後に呼び出されるアドバイス
 */
interface AroundAdvice {
    /**
     * Joinpointの実行前に呼び出します
     *
     * @param object           $target
     * @param ReflectionMethod $method
     * @param array            $args
     * @@return                void
     */
    public function before($target, ReflectionMethod $method, array $args = array());
    /**
     * Joinpointの実行後に呼び出します
     *
     * @param mixed            $returnValue
     * @param object           $target
     * @param ReflectionMethod $method
     * @param array            $args
     * @return                 void
     */
    public function after($returnValue, $target, ReflectionMethod $method, array $args = array());
}
/**
 * Joinpointで例外が発生した時に呼び出されるアドバイス
 */
interface ThrowAdvice {
    /**
     * Joinpointで例外が発生したときに呼び出します
     *
     * @param Exception $ex
     * @return          void
     */
    public function throwing(Exception $ex);
}