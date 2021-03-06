<?php
/**
 * GD_Tab_Guitar generates guitar tab scores.
 * This module is inspired by GD::Tab::Guitar (perl module), and it's almost its copy.
 *
 *
 * SYNOPSIS
 *
 * $gtr = new GD_Tab_Guitar();
 * 
 * // print png image
 * print $gtr->chord('D#sus4')->png();
 * 
 * // other tab generate
 * $gtr->generate('G7', array(1,0,0,0,2,3))->png();
 * 
 * // above fret style (1st string to 6th string) is unfamiliar to most guitarists.
 * // you can also specify the reversed and familiar style like this:
 * $gtr->generate('G7', '320001')->png();
 * 
 * // if you want to specify some mute strings, put "x" character.
 * $gtr->generate('D7', 'x00212')->png();
 * 
 * // set color
 * $gtr->setColor(255, 0, 0);
 * 
 * // set background-color and no interlace
 * $gtr->setBgcolor(200, 200, 200);
 * $gtr->setInterlaced(0);
 * 
 * // all tabs image save to file.
 * foreach ($gtr->allChords() as $chord) {
 *     $gtr->chord($chord)->png("$chord.png");
 * }
 *
 *
 * @author     NAKAMURA Satoru <clonedoppelganger@gmail.com>
 * @license    New BSD License
 * @version    0.0.1
 * @see http://search.cpan.org/dist/GD-Tab-Guitar/
 * @see http://search.cpan.org/~tateno/GD-Tab-Ukulele-0.01/
 */
class GD_Tab_Guitar
{

    const GD_TINY_FONT = 1;

    const GD_SMALL_FONT = 2;

    protected static $lines = array(
        array(5,  15, 46, 15),
        array(5,  21, 46, 21),
        array(5,  27, 46, 27),
        array(5,  33, 46, 33),
        array(5,  39, 46, 39),
        array(5,  45, 46, 45),
        array(4,  15, 4,  45),
        array(6,  15, 6,  45),
        array(14, 15, 14, 45),
        array(22, 15, 22, 45),
        array(30, 15, 30, 45),
        array(38, 15, 38, 45),
    );

    protected static $chordLists = array(
        'C'          => 'x32010',
        'C6'         => 'x32210',
        'C6(9)'      => 'x32233',
        'CM7'        => 'x32000',
        'CM7(9)'     => 'x30000',
        'C7'         => 'x32310',
        'C7(b5)'     => 'x34310',
        'C7(b9)'     => 'x32323',
        'C7(b9,13)'  => 'x21214',
        'C7(9)'      => 'x3233x',
        'C7(9,13)'   => 'x32335',
        'C7(#9)'     => 'x3234x',
        'C7(#11)'    => 'x1213x',
        'C7(b13)'    => 'x1211x',
        'C7(13)'     => 'x1221x',
        'Cm'         => 'x35543',
        'Cm6'        => 'x31213',
        'Cm6(9)'     => 'x3124x',
        'CmM7'       => 'x31003',
        'Cm7'        => 'x31313',
        'Cm7(b5)'    => 'x3434x',
        'Cm7(9)'     => 'x3133x',
        'Cm7(9,11)'  => 'x31331',
        'Cdim'       => 'x3424x',
        'Caug'       => 'x3211x',
        'Caug7'      => 'x323x4',
        'Csus4'      => 'x33011',
        'C7sus4'     => 'x33311',
        'Cadd9'      => 'x32030',
        'C#'         => 'x43121',
        'C#6'        => 'x4332x',
        'C#6(9)'     => 'x43344',
        'C#M7'       => 'x43111',
        'C#M7(9)'    => 'x4354x',
        'C#7'        => 'x4342x',
        'C#7(b5)'    => 'x4542x',
        'C#7(b9)'    => 'x43434',
        'C#7(b9,13)' => 'x43436',
        'C#7(9)'     => 'x4344x',
        'C#7(9,13)'  => 'x43445',
        'C#7(#9)'    => 'x4345x',
        'C#7(#11)'   => 'x4546x',
        'C#7(b13)'   => 'x2322x',
        'C#7(13)'    => 'x2332x',
        'C#m'        => 'x46654',
        'C#m6'       => 'x42324',
        'C#m6(9)'    => 'x4234x',
        'C#mM7'      => 'x4211x',
        'C#m7'       => 'x42100',
        'C#m7(b5)'   => 'x4545x',
        'C#m7(9)'    => 'x4244x',
        'C#m7(9,11)' => 'x42442',
        'C#dim'      => 'x4535x',
        'C#aug'      => 'x4322x',
        'C#aug7'     => 'x2322x',
        'C#sus4'     => 'x46674',
        'C#7sus4'    => 'x44422',
        'C#add9'     => 'x43141',
        'D'          => 'xx0232',
        'D6'         => 'xx0202',
        'D6(9)'      => 'x54455',
        'DM7'        => 'xx0222',
        'DM7(9)'     => 'xx0220',
        'D7'         => 'xx0212',
        'D7(b5)'     => 'xx0112',
        'D7(b9)'     => 'x5454x',
        'D7(b9,13)'  => 'x54547',
        'D7(9)'      => 'xx0210',
        'D7(9,13)'   => 'x54557',
        'D7(#9)'     => 'x5456x',
        'D7(#11)'    => 'xx0112',
        'D7(b13)'    => 'x3433x',
        'D7(13)'     => 'x3443x',
        'Dm'         => 'xx0231',
        'Dm6'        => 'xx0201',
        'Dm6(9)'     => 'x5345x',
        'DmM7'       => 'x5322x',
        'Dm7'        => 'xx0211',
        'Dm7(b5)'    => 'xx0111',
        'Dm7(9)'     => 'x5355x',
        'Dm7(9,11)'  => 'x53553',
        'Ddim'       => 'xx0101',
        'Daug'       => 'xx0332',
        'Daug7'      => 'xx0312',
        'Dsus4'      => 'x55033',
        'D7sus4'     => 'xx0213',
        'Dadd9'      => 'xx0230',
        'Eb'         => 'xx1343',
        'Eb6'        => 'xx1313',
        'Eb6(9)'     => 'x65566',
        'EbM7'       => 'xx1333',
        'EbM7(9)'    => 'xx1331',
        'Eb7'        => 'xx1323',
        'Eb7(b5)'    => 'xx1223',
        'Eb7(b9)'    => 'xx1020',
        'Eb7(b9,13)' => 'x65658',
        'Eb7(9)'     => 'xx1021',
        'Eb7(9,13)'  => 'x65668',
        'Eb7(#9)'    => 'xx1022',
        'Eb7(#11)'   => 'xx1223',
        'Eb7(b13)'   => 'x4544x',
        'Eb7(13)'    => 'x4554x',
        'Ebm'        => 'xx1342',
        'Ebm6'       => 'xx1312',
        'Ebm6(9)'    => 'x6456x',
        'EbmM7'      => 'xx1332',
        'Ebm7'       => 'xx1322',
        'Ebm7(b5)'   => 'xx1222',
        'Ebm7(9)'    => 'x6466x',
        'Ebm7(9,11)' => 'xx1121',
        'Ebdim'      => 'xx1212',
        'Ebaug'      => 'xx1003',
        'Ebaug7'     => 'x2102x',
        'Ebsus4'     => 'xx1344',
        'Eb7sus4'    => 'xx1324',
        'Ebadd9'     => 'xx1341',
        'E'          => '022100',
        'E6'         => '022120',
        'E6(9)'      => 'xx2122',
        'EM7'        => '02110x',
        'EM7(9)'     => '021102',
        'E7'         => '020100',
        'E7(b5)'     => 'xx2334',
        'E7(b9)'     => '020101',
        'E7(b9,13)'  => '020131',
        'E7(9)'      => '020132',
        'E7(9,13)'   => '020122',
        'E7(#9)'     => '020103',
        'E7(#11)'    => '6x675x',
        'E7(b13)'    => '020110',
        'E7(13)'     => '020120',
        'Em'         => '022000',
        'Em6'        => '022020',
        'Em6(9)'     => '022022',
        'EmM7'       => '021000',
        'Em7'        => '020000',
        'Em7(b5)'    => '0x2333',
        'Em7(9)'     => '020002',
        'Em7(9,11)'  => 'xx2232',
        'Edim'       => '012020',
        'Eaug'       => '03211x',
        'Eaug7'      => '032130',
        'Esus4'      => '022200',
        'E7sus4'     => '020200',
        'Eadd9'      => '024100',
        'F'          => '133211',
        'F6'         => '1x323x',
        'F6(9)'      => '100011',
        'FM7'        => 'xx3210',
        'FM7(9)'     => '1x2010',
        'F7'         => '131211',
        'F7(b5)'     => '1x120x',
        'F7(b9)'     => 'xx1212',
        'F7(b9,13)'  => '1x1232',
        'F7(9)'      => '131213',
        'F7(9,13)'   => '1x123x',
        'F7(#9)'     => '131214',
        'F7(#11)'    => '101201',
        'F7(b13)'    => '1x122x',
        'F7(13)'     => '1x123x',
        'Fm'         => '133111',
        'Fm6'        => '133131',
        'Fm6(9)'     => '1xx133',
        'FmM7'       => '13211x',
        'Fm7'        => '131111',
        'Fm7(b5)'    => '1x110x',
        'Fm7(9)'     => '131113',
        'Fm7(9,11)'  => '131313',
        'Fdim'       => '1x0101',
        'Faug'       => 'xx3221',
        'Faug7'      => '1x1221',
        'Fsus4'      => '133311',
        'F7sus4'     => '131311',
        'Fadd9'      => 'xx3213',
        'F#'         => '244322',
        'F#6'        => '2x434x',
        'F#6(9)'     => '2x112x',
        'F#M7'       => 'xx4321',
        'F#M7(9)'    => '2x312x',
        'F#7'        => '242322',
        'F#7(b5)'    => '2x231x',
        'F#7(b9)'    => '212020',
        'F#7(b9,13)' => 'x1204x',
        'F#7(9)'     => '21212x',
        'F#7(9,13)'  => '21213x',
        'F#7(#9)'    => '242325',
        'F#7(#11)'   => '2x231x',
        'F#7(b13)'   => '2x233x',
        'F#7(13)'    => '2x234x',
        'F#m'        => '244222',
        'F#m6'       => '244242',
        'F#m6(9)'    => 'xx1224',
        'F#mM7'      => '24322x',
        'F#m7'       => '242222',
        'F#m7(b5)'   => '2x221x',
        'F#m7(9)'    => '242224',
        'F#m7(9,11)' => '20210x',
        'F#dim'      => '2x121x',
        'F#aug'      => 'xx4332',
        'F#aug7'     => '2x2332',
        'F#sus4'     => '244422',
        'F#7sus4'    => '242422',
        'F#add9'     => 'xx4324',
        'G'          => '320003',
        'G6'         => '320000',
        'G6(9)'      => '3x223x',
        'GM7'        => '320002',
        'GM7(9)'     => '3x423x',
        'G7'         => '320001',
        'G7(b5)'     => '3x342x',
        'G7(b9)'     => 'x2313x',
        'G7(b9,13)'  => '3x3100',
        'G7(9)'      => '353435',
        'G7(9,13)'   => '3x3200',
        'G7(#9)'     => '353436',
        'G7(#11)'    => '3x342x',
        'G7(b13)'    => '3x344x',
        'G7(13)'     => '323000',
        'Gm'         => '355333',
        'Gm6'        => '3x233x',
        'Gm6(9)'     => 'xx2335',
        'GmM7'       => '354333',
        'Gm7'        => '353333',
        'Gm7(b5)'    => '3x332x',
        'Gm7(9)'     => '353335',
        'Gm7(9,11)'  => '3x321x',
        'Gdim'       => '3x232x',
        'Gaug'       => '321003',
        'Gaug7'      => '3x3443',
        'Gsus4'      => '330013',
        'G7sus4'     => '330011',
        'Gadd9'      => '3x0233',
        'G#'         => '431114',
        'G#6'        => '431111',
        'G#6(9)'     => '4x334x',
        'G#M7'       => 'xx6543',
        'G#M7(9)'    => '4x534x',
        'G#7'        => '464544',
        'G#7(b5)'    => '4x453x',
        'G#7(b9)'    => 'x3424x',
        'G#7(b9,13)' => '4x4211',
        'G#7(9)'     => '464546',
        'G#7(9,13)'  => '4x4311',
        'G#7(#9)'    => '464547',
        'G#7(#11)'   => '4x453x',
        'G#7(b13)'   => '4x455x',
        'G#7(13)'    => '434111',
        'G#m'        => '466444',
        'G#m6'       => 'xx1101',
        'G#m6(9)'    => 'xx3446',
        'G#mM7'      => 'xx1103',
        'G#m7'       => '464444',
        'G#m7(b5)'   => 'x20102',
        'G#m7(9)'    => '464446',
        'G#m7(9,11)' => '4x432x',
        'G#dim'      => '4x343x',
        'G#aug'      => 'xx6554',
        'G#aug7'     => '4x4554',
        'G#sus4'     => '466644',
        'G#7sus4'    => '464644',
        'G#add9'     => 'xx6546',
        'A'          => 'x02220',
        'A6'         => 'x02222',
        'A6(9)'      => 'x02202',
        'AM7'        => 'x02120',
        'AM7(9)'     => 'x02100',
        'A7'         => 'x02020',
        'A7(b5)'     => 'x0102x',
        'A7(b9)'     => 'x02323',
        'A7(b9,13)'  => 'x05322',
        'A7(9)'      => 'x02423',
        'A7(9,13)'   => 'x05422',
        'A7(#9)'     => '575658',
        'A7(#11)'    => 'x01023',
        'A7(b13)'    => 'x02021',
        'A7(13)'     => 'x02022',
        'Am'         => 'x02210',
        'Am6'        => 'x02212',
        'Am6(9)'     => 'xx4557',
        'AmM7'       => 'x02110',
        'Am7'        => 'x02010',
        'Am7(b5)'    => 'x01213',
        'Am7(9)'     => 'x02000',
        'Am7(9,11)'  => 'x02433',
        'Adim'       => 'x01212',
        'Aaug'       => 'x03221',
        'Aaug7'      => 'x03021',
        'Asus4'      => 'x02230',
        'A7sus4'     => 'x02030',
        'Aadd9'      => 'x02200',
        'Bb'         => 'x13331',
        'Bb6'        => 'x13333',
        'Bb6(9)'     => 'x10011',
        'BbM7'       => 'x13231',
        'BbM7(9)'    => 'x10211',
        'Bb7'        => 'x13131',
        'Bb7(b5)'    => 'x12131',
        'Bb7(b9)'    => 'x10101',
        'Bb7(b9,13)' => 'x10103',
        'Bb7(9)'     => 'x1011x',
        'Bb7(9,13)'  => 'x10113',
        'Bb7(#9)'    => 'x1012x',
        'Bb7(#11)'   => 'x10130',
        'Bb7(b13)'   => 'x13132',
        'Bb7(13)'    => 'x13133',
        'Bbm'        => 'x13321',
        'Bbm6'       => 'x1302x',
        'Bbm6(9)'    => 'xx5668',
        'BbmM7'      => 'x13221',
        'Bbm7'       => 'x13121',
        'Bbm7(b5)'   => 'x1212x',
        'Bbm7(9)'    => 'x13111',
        'Bbm7(9,11)' => '6x654x',
        'Bbdim'      => 'x12020',
        'Bbaug'      => 'x10443',
        'Bbaug7'     => 'x14132',
        'Bbsus4'     => 'x13341',
        'Bb7sus4'    => 'x13141',
        'Bbadd9'     => 'x13311',
        'B'          => 'x24442',
        'B6'         => 'x24444',
        'B6(9)'      => 'x21122',
        'BM7'        => 'x24342',
        'BM7(9)'     => 'x2132x',
        'B7'         => 'x21202',
        'B7(b5)'     => 'x2324x',
        'B7(b9)'     => 'x2121x',
        'B7(b9,13)'  => 'x21214',
        'B7(9)'      => 'x2122x',
        'B7(9,13)'   => 'x21224',
        'B7(#9)'     => 'x2123x',
        'B7(#11)'    => 'x2324x',
        'B7(b13)'    => 'x24243',
        'B7(13)'     => 'x24244',
        'Bm'         => 'x24432',
        'Bm6'        => 'x2413x',
        'Bm6(9)'     => 'x2012x',
        'BmM7'       => 'x20332',
        'Bm7'        => 'x20202',
        'Bm7(b5)'    => 'x2323x',
        'Bm7(9)'     => 'x2022x',
        'Bm7(9,11)'  => 'x20220',
        'Bdim'       => 'x2313x',
        'Baug'       => 'x2100x',
        'Baug7'      => 'x25243',
        'Bsus4'      => 'x24452',
        'B7sus4'     => 'x22202',
        'Badd9'      => 'x24422',
    );

    protected static $synonyms = array(
        'C#' => 'Db',
        'Eb' => 'D#',
        'F#' => 'Gb',
        'G#' => 'Ab',
        'Bb' => 'A#',
    );

    protected static $synonymsRe = '/^([CFG]#|[EB]b)/';

    private static $initialized = false;

    private $bgcolor;

    private $color;

    private $interlaced;

    private $im;

    public function __construct()
    {
        if (!self::$initialized) {
            self::init();
        }
        $this->bgcolor    = array(255, 255, 255);
        $this->color      = array(0, 0, 0);
        $this->interlaced = true;
    }

    public function setBgcolor($r, $g, $b)
    {
        $this->bgcolor = array($r, $g, $b);
    }

    public function setColor($r, $g, $b)
    {
        $this->color = array($r, $g, $b);
    }

    public function setInterlaced($interlaced)
    {
        $this->interlaced = $interlaced;
    }

    public function chord($chord)
    {
        return $this->generate($chord, $this->getFrets($chord));
    }

    public function getFrets($chord)
    {
        if (!isset(self::$chordLists[$chord])) {
            throw new Exception("undefined chord $chord");
        }
        return str_split(self::$chordLists[$chord]);
    }

    public function generate($chord, $frets = null)
    {
        if ($frets !== null) {
            $frets = is_array($frets) ? array_reverse($frets) : array_reverse(str_split($frets));
        }

        $this->im = imagecreate(52, 56);
        $bgcolor = imagecolorallocate($this->im, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);
        $color = imagecolorallocate($this->im, $this->color[0], $this->color[1], $this->color[2]);

        if ($this->interlaced) {
            imagecolortransparent($this->im, $bgcolor);
            imageinterlace($this->im, true);
        }

        $this->drawLine($color);

        $fretMax = max(preg_grep('/^\d+$/', $frets));

        if ($fretMax > 5) {
            imagefilledrectangle($this->im, 4, 15, 6, 45, $bgcolor);
            $fretNum = $fretMax - 5;

            foreach ($frets as &$fret) {
                if (strtolower($fret) !== 'x') {
                    $fret -= $fretNum;
                }
            }

            for ($n = 0; $n <= 4; $n++) {
                imagestring($this->im, self::GD_TINY_FONT, 9 * $n + 4, 47, $fretNum + 1, $color);
                $fretNum++;
            }
        }

        $i = 0;
        foreach ($frets as &$fret) {
            if (strtolower($fret) === 'x') {
                imageline($this->im, 0, 14 + 6 * $i, 2, 16 + 6 * $i, $color);
                imageline($this->im, 2, 14 + 6 * $i, 0, 16 + 6 * $i, $color);
            } elseif ($fret > 0) {
                imagefilledrectangle($this->im,
                    9  + 8 * ($fret - 1),
                    14 + 6 * $i,
                    11 + 8 * ($fret - 1),
                    16 + 6 * $i, $color
                );
            }
            $i++;
        }

        imagestring($this->im, self::GD_SMALL_FONT, 4, 0, $chord, $color);

        return $this;
    }

    public function png($path = null)
    {
        return $this->output('png', $path);
    }

    public function jpeg($path = null)
    {
        return $this->output('jpeg', $path);
    }

    public function gif($path = null)
    {
        return $this->output('gif', $path);
    }

    public function allChords()
    {
        return array_keys(self::$chordLists);
    }

    protected function output($type, $path = null)
    {
        switch ($type) {
        case 'png':
            $func = 'imagepng';
            break;
        case 'jpeg':
            $func = 'imagejpeg';
            break;
        case 'gif':
            $func = 'imagegif';
            break;
        default:
            $func = 'imagepng';
        }
        if ($path === null) {
            ob_start();
            call_user_func($func, $this->im);
            $contents = ob_get_clean();
            imagedestroy($this->im);
            return $contents;
        } else {
            call_user_func($func, $this->im, $path);
            imagedestroy($this->im);
        }
    }

    protected function drawLine($color)
    {
        foreach (self::$lines as $line) {
            imageline($this->im, $line[0], $line[1], $line[2], $line[3], $color);
        }
    }

    protected static function init()
    {
        foreach (self::$chordLists as $chord => $frets) {
            if (preg_match(self::$synonymsRe, $chord, $matches)) {
                $matche = $matches[1];
                $sameChord = preg_replace('/^' . $matche . '/', self::$synonyms[$matche], $chord);
                self::$chordLists[$sameChord] = $frets;
            }
        }
        self::$initialized = true;
    }

}
