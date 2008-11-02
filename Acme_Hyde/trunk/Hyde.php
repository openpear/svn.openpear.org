<?php
/**
 * Acme_Hyde - Hyde Calculator
 *
 * @author  riaf <riafweb@gmail.com>
 * @see     http://search.cpan.org/~nozzzzz/Acme-Hyde-0.04/
 * @version $Id$
 */

class Acme_Hyde
{
    const size = 156;

    /**
     * cm を hyde に変換
     *
     * @param   $cm
     * @return  float $hyde
     */
    public static function to($cm){
        /***
         * eq(1.15, Acme_Hyde::to(180));
         */
        return (float) sprintf("%.2f", $cm / Acme_Hyde::size);
    }
    /**
     * hyde を cm に変換
     *
     * @param   $hyde
     * @return  int $cm
     */
    public static function from($hyde){
        /***
         * eq(180, Acme_Hyde::from(1.15));
         */
        return (int) ceil($hyde * Acme_Hyde::size);
    }
}
