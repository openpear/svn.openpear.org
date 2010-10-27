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
        $flist = implode(',', $fields);
        $idx = $indexid;
        $line = "P\t$idx\t$db\t$table\t$index\t$flist";
        if($this->send($line)){
            return $this->response;
        }
        return array();
    }
    public function executeSingle($idx, $op, $fields, $limit=1, $offset=0, $modop=null, $values=null){
        $flen = count($fields);
        $flist = implode("\t", $fields);
        if(is_null($modop)){
            if($op=='+')
                $line = "$idx\t+\t$flen\t$flist";
            else
                $line = "$idx\t$op\t$flen\t$flist\t$limit\t$offset";
        }else if($modop=='U'){
            if(is_null($values))
                throw Exception();
            $mks =implode("\t", $values);
            $line = "$idx\t+\t$flen\t$flist\t$limit\t$offset\tU\t$mks";
        }else if($modop=='D'){
            $line = "$idx\t+\t$flen\t$flist\t$limit\t$offset\tD";
        }
        if($this->send($line)){
            $r = $this->response;
            return $r;
        }
    }
    public function close(){
        fclose($this->socket);
    }
    private function send($line){
        unset($this->response);
        $line .= "\n";
        $max = strlen($line);
        for($done=0;$done<$max;$done+=$c){
            $c = fwrite($this->socket, $line);
            if($c===false){
                return false;
            }
        }
        fflush($this->socket);
        $rline = rtrim(stream_get_line($this->socket, 2048, "\n"));
        $res = explode("\t", $rline);
        if(!isset($res[0]))
            throw Exception('invalid respons');
        if($res[0]!=0){
            $this->errno = $res[0];
            $this->errstr = $res[2];
        }else{
            unset($this->errno); unset($this->errstr);
        }
        $this->response = $res;
        return true;
    }
}