<?php
/**
 * @package Image_GifColorConverter
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */
class Image_GifColorConverter
{
    /**
     * 色違いのgif画像を$outputに出力する
     * @param resource
     * @param resource 
     * @param Array
     * @return void
     */
    function convert($input, $output, Array $colorMap)
    {
        if (!is_resource($input) || !is_resource($output)) throw new InvalidArgumentException;
        $binaryColorMap = $this->buildBinaryColorMap($colorMap);
        $this->processHeader($input, $output, $binaryColorMap);
        while ($this->processBlock($input, $output, $binaryColorMap));
    }
    
    protected function buildBinaryColorMap(Array $colorMap)
    {
        $binaryColorMap = array();
        foreach ($colorMap as $key => $val) {
            $binaryColorMap[$this->packColor($key)] = $this->packColor($val);
        }
        return $binaryColorMap;
    }
    
    /**
     * 結果をファイルに書き出す
     * @param string ファイルパス
     * @param string ファイルパス
     * @param Array
     * @return void
     */
    function put($inputPath, $outputPath, Array $colorMap)
    {
        $input = fopen($inputPath, 'rb');
        flock($input, LOCK_SH);
        $output = fopen($outputPath, 'wb');
        flock($input, LOCK_EX);
        try {
            $this->convert($input, $output, $colorMap);
        } catch (Exception $e) {
            fclose($input);
            fclose($output);
            throw $e;
        }
        fclose($input);
        fclose($output);
    }
    
    /**
     * 結果を標準出力に書き出す
     * HTTPヘッダを吐かない
     * @param string ファイルパス
     * @param Array 
     * @return void
     */
    function write($inputPath, Array $colorMap)
    {
        $input = fopen($inputPath, 'rb');
        flock($input, LOCK_SH);
        try {
            $this->convert($input, STDOUT, $colorMap);
        } catch (Exception $e) {
            fclose($input);
            throw $e;
        }
        fclose($input);
    }
    
    /**
     * 結果のバイナリを得る
     * @param string ファイルパス
     * @param Array
     * @return string
     */
    function get($inputPath, Array $colorMap)
    {
        $output = fopen('php://memory', 'w+b');
        $input = fopen($inputPath, 'rb');
        flock($input, LOCK_SH);
        try {
            $this->convert($input, $output, $colorMap);
        } catch (Exception $e) {
            fclose($input);
            fclose($output);
            throw $e;
        }
        fclose($input);
        $buffer = array();
        rewind($output);
        while(!feof($output)) $buffer[] = fread($output, 0xffff);
        fclose($output);
        return implode('', $buffer);
    }
    
    
    protected function packColor($color)
    {
        return pack('C3', ($color >> 16) & 0xff, ($color >> 8) & 0xff, $color & 0xff);
    }
    
    protected function processHeader($input, $output, Array $binaryColorMap)
    {
        $buffer = fread($input, 13);
        fwrite($output, $buffer);
        $buffer = $this->c2i(substr($buffer, 10, 1));
        
        $hasGlobalColorTable = !!(($buffer >> 7) & 1);
        $globalColorTableSize = pow(2, 1 + ($buffer & 7)) * 3;
        
        if ($hasGlobalColorTable) {
            $tableBuffer = fread($input, $globalColorTableSize);
            foreach (str_split($tableBuffer, 3) as $color) {
                fwrite($output, isset($binaryColorMap[$color]) ? $binaryColorMap[$color] : $color);
            }
        }
    }
    
    protected function processBlock($input, $output, Array $binaryColorMap)
    {
        $buffer = fread($input, 1);
        fwrite($output, $buffer);
        $header = $this->c2i($buffer);
        
        if ($header === 0x3b) return false;
        
        if ($header === 0x2c) {
            $this->processImageBlockBody($input, $output, $binaryColorMap);
        } elseif($buffer === pack('C', 0x21)) {
        
            $buffer = fread($input, 1);
            fwrite($output, $buffer);
            
            $header = ($header << 8) + $this->c2i($buffer);
            
            if ($header === 0x21f9) {
                $this->processGraphicControlExtensionBody($input, $output);
            } elseif ($header === 0x21fe) {
                $this->processCommentExtensionBody($input, $output);
            } elseif ($header === 0x2101) {
                $this->processPlainTextExtensionBody($input, $output);
            } elseif ($header === 0x21ff) {
                $this->processApplicationExtensionBody($input, $output);
            } else {
                throw new Image_GifColorConverter_Exception(sprintf('unknown block header: %x', $header));
            }
        } else {
            $msg = sprintf('invalid block header: %x%s', $header, PHP_EOL) .
                   sprintf('input stream offset: %d%s', ftell($input), PHP_EOL) . 
                   sprintf('output stream offset: %d', ftell($output));
            throw new Image_GifColorConverter_Exception($msg);
        }
        
        return true;
    }
    
    protected function processImageBlockBody($input, $output, Array $binaryColorMap)
    {
        $buffer = fread($input, 9);
        fwrite($output, $buffer);

        $buffer = $this->c2i(substr($buffer, 8, 1));
        $hasColorTable = !!(($buffer >> 7) & 1);
        $colorTableSize = pow(2, 1 + ($buffer & 7));
        
        if ($hasColorTable) {
            $tableBuffer = fread($input, $colorTableSize);
            foreach (str_split($tableBuffer, 3) as $color) {
                fwrite($output, isset($binaryColorMap[$color]) ? $binaryColorMap[$color] : $color);
            }
        }
        fwrite($output, fread($input, 1));
        $this->processMiniBlock($input, $output);
    }
    
    protected function processGraphicControlExtensionBody($input, $output)
    {
        fwrite($output, fread($input, 6));
    }
    
    protected function processCommentExtensionBody($input, $output)
    {
        $this->processMiniBlock($input, $output);
    }
    
    protected function processPlainTextExtensionBody($input, $output)
    {
        $this->processMiniBlock($input, $output);
    }
    
    protected function processApplicationExtensionBody($input, $output)
    {
        $this->processMiniBlock($input, $output);
    }
    
    protected function c2i($char)
    {
        list(, $buffer) = unpack('C1', $char);
        return $buffer;
    }
    
    protected function processMiniBlock($input, $output)
    {
        while (!feof($input)) {
            fwrite($output, $buffer = fread($input, 1));
            $size = $this->c2i($buffer);
            if ($size === 0) return;
            fwrite($output, fread($input, $size));
        }
    }
    
    static function rgb($r, $g, $b)
    {
        $r &= 0xff;
        $g &= 0xff;
        $b &= 0xff;
        return ($r << 16) + ($g << 8) + $b; 
    }
}

class Image_GifColorConverter_Exception extends Exception { }
