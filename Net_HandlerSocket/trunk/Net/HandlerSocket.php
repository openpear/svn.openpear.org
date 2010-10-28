<?php
class HandlerSocket{
    protected $socket;
    public function __construct($host, $port){
        $soc = fsockopen($host, $port, $errno, $errstr);
        if(!$soc){
            throw Exception("$errstr($errno)");
        }else{
            $this->socket = $soc;
        }
    }
    public function openIndex($indexid, $db, $table, $index, $fields){
        $flist = implode(",", array_map(array($this, 'escape'), $fields));
        $main = implode("\t", array_map(array($this, 'escape'), array($indexid, $db, $table, $index)));
        $line = "P\t$main\t$flist";
        if($this->send($line)){
            return $this->recv();
        }
        return array();
    }
    public function executeSingle($idx, $op, $fields, $limit=1, $offset=0, $modop=null, $values=null){
        $line = $this->buildLine($idx, $op, $fields, $limit, $offset, $modop, $values);
        if($this->send($line)){
            return $this->recv();
        }
    }
    public function executeMulti($requests){
        $r = array_fill(0, count($requests), -1);
        foreach ($requests as $req) {
            $line = call_user_func_array(array($this, 'buildLine'), $req);
            if(!$this->send($line))
                return $r;
        }
        for($i=0, $l=count($requests);$i<$l;++$i){
            $r[$i] = $this->recv();
        }
        return $r;
    }
    public function close(){
        fclose($this->socket);
    }
    public function getError(){
        return sprintf("%s(%s)", $this->errstr, $this->errno);
    }
    private function send($line){
        unset($this->response);
        $line .= "\n";
        $max = strlen($line);
        for($done=0;$done<$max;$done+=$c){
            $c = fwrite($this->socket, substr($line, $done));
            if($c===false){
                return false;
            }
        }
        fflush($this->socket);
        return true;
    }
    private function recv(){
        $rline = rtrim(stream_get_line($this->socket, 2048, "\n"));
        $res = array_map(array($this, 'unescape'), explode("\t", $rline));
        if(!isset($res[0]))
            throw Exception('invalid respons');
        if($res[0]!=0){
            $this->errno = $res[0];
            $this->errstr = $res[2];
        }else{
            unset($this->errno); unset($this->errstr);
        }
        return $this->response = $res;
    }
    private function buildLine($idx, $op, $fields, $limit=1, $offset=0, $modop=null, $values=null){
        $flen = count($fields);
        $flist = implode("\t", array_map(array($this, 'escape'), $fields));
        $idx = $this->escape($idx);
        if(is_null($modop)){
            if($op=='+')
                $line = "$idx\t+\t$flen\t$flist";
            else
                $line = "$idx\t$op\t$flen\t$flist\t$limit\t$offset";
        }else if($modop=='U'){
            if(is_null($values))
                throw Exception();
            $mks =implode("\t", array_map(array($this, 'escape'), $values));
            $line = "$idx\t$op\t$flen\t$flist\t$limit\t$offset\tU\t$mks";
        }else if($modop=='D'){
            $line = "$idx\t$op\t$flen\t$flist\t$limit\t$offset\tD";
        }
        return $line;
    }
    private function unescape($str){
        if($str==="\x00")
            return null;
        else{
            return strtr($str, array("\x01\x40" => "\x00",
                                     "\x01\x41" => "\x01",
                                     "\x01\x42" => "\x02",
                                     "\x01\x43" => "\x03",
                                     "\x01\x44" => "\x04",
                                     "\x01\x45" => "\x05",
                                     "\x01\x46" => "\x06",
                                     "\x01\x47" => "\x07",
                                     "\x01\x48" => "\x08",
                                     "\x01\x49" => "\x09",
                                     "\x01\x4A" => "\x0A",
                                     "\x01\x4B" => "\x0B",
                                     "\x01\x4C" => "\x0C",
                                     "\x01\x4D" => "\x0D",
                                     "\x01\x4E" => "\x0E",
                                     "\x01\x4F" => "\x0F"));
        }
    }
    private function escape($str){
        if(!is_null($str)){
            return strtr($str, array("\x00" => "\x01\x40",
                                     "\x01" => "\x01\x41",
                                     "\x02" => "\x01\x42",
                                     "\x03" => "\x01\x43",
                                     "\x04" => "\x01\x44",
                                     "\x05" => "\x01\x45",
                                     "\x06" => "\x01\x46",
                                     "\x07" => "\x01\x47",
                                     "\x08" => "\x01\x48",
                                     "\x09" => "\x01\x49",
                                     "\x0A" => "\x01\x4A",
                                     "\x0B" => "\x01\x4B",
                                     "\x0C" => "\x01\x4C",
                                     "\x0D" => "\x01\x4D",
                                     "\x0E" => "\x01\x4E",
                                     "\x0F" => "\x01\x4F"));
        }else
            return "\x00";
    }
}