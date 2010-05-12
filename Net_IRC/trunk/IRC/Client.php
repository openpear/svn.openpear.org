<?php
require_once 'Net/IRC.php';

class Net_IRC_Client
{
    const TIMEOUT = 30;

    protected $stream;
    protected $server;
    protected $port;
    protected $options = array();
    protected $channels = array();

    private $required_options = array('user', 'nick');

    /**
     * 初期化
     *
     * @param   string  $server 　接続先
     * @param   int     $port   　接続先ポート
     * @param   array   $options  設定
     **/
    public function __construct($server, $port=6667, $options=array()) {
        $this->server = $server;
        $this->port = $port;
        $this->options = $options;
    }

    /**
     * 接続
     *
     * @return void
     **/
    public function connect() {
        foreach ($this->required_options as $r) {
            if (!isset($this->options[$r]))
                throw new Net_IRC_Exception($r. ' is required.');
        }

        $stream = stream_socket_client(
            sprintf("tcp://%s:%d", $this->server, $this->port),
            $errno, $errmsg, self::TIMEOUT
        );
        if ($stream === false) {
            throw new Net_IRC_Exception($errmsg, $errno);
        }
        $this->stream = $stream;
        $this->on_connected();
        if (isset($this->options['password'])) {
            $this->post('PASS', $this->options['password']);
        }
        $this->post('NICK', $options['nick']);
        $this->post('USER', $options['user']);

        while ($l = fgets($this->stream)) {
            try {
                $msg = $this->parse_message($l);
                if ($this->on_message($msg) === true) continue;
                $method = strtolower('on_'. $msg->command);
                if (method_exists(array($this, $method))) {
                    $this->$method($msg);
                }
            } catch (Net_IRC_Exception $e) {
                $this->on_error($e);
            }
        }
    }

    /**
     * データを送信
     *
     * @param   可変
     **/
    protected function post() {
        if (!$this->stream) {
            throw new Net_IRC_Exception('connection not found');
        }
        if (fwrite($this->stream, implode(' ', func_get_args()))) {
            return true;
        }
        throw new Net_IRC_Exception('post error');
    }

    /**
     * 接続時のアクション
     *
     * @return void
     **/
    protected function on_connected() {
        # pass
    }

    /**
     * メッセージ受信
     *
     * @return bool trueだと他のアクションを行わない
     **/
    protected function on_message($msg) {
        # pass
    }

    /**
     * PING PONG
     **/
    protected function on_ping($arg) {
        $this->post('PONG '. $arg);
    }

    /**
     * 切断時のアクション
     */
    protected function on_disconnected() {
        # pass
    }

    /**
     * エラーが発生したとき
     **/
    protected function on_error(Exception $e) {
        echo $e->getMessage(), PHP_EOL;
    }

    public function __destruct() {
        if ($this->stream) {
            fclose($this->stream);
            $this->on_disconnected();
        }
    }
}
