<?php
/**
 * Twitter API アクセスライブラリ
 *
 * @author Yuki Kisaragi <yuki@transrain.net>
 */
require_once('HTTP/OAuth/Consumer.php');

require_once(dirname(__FILE__) . '/Twitter/Config.php');
require_once(dirname(__FILE__) . '/Twitter/Connection.php');

/**
 * Twitter APIへのアクセスを行うためのクラスライブラリ
 *
 * @author Yuki Kisaragi <yuki@transrain.net>
 * @package Services_Twitter
 * @version 0.0.5
 */
class Services_Twitter {

	/**
	 * Services_Twitterの設定情報を保持する
	 * @var Services_Twitter_Config 設定情報
	 */
    private $_config;

    /**
     * Services_Twitterの接続情報を保持する
     * @var Services_Twitter_Connection 接続情報
     */
    private $_connection;

    // データ保持 ==================================================================================

    /**
     * APIの設定を保持する
     * @var SimpleXMLElement APIの定義情報
     */
    private $_apiConfigs;

    /**
     * APIの通信によって取得できたコンテンツの内容を保持する
     * @var array コンテンツ内容
     */
    private $_body = null;

    /**
     * APIの通信時のステータスコードを保持する。
     * @var int ステータスコード
     */
    private $_status = null;

    /**
     * Services_Twitterをインスタンス化します
     * @return Services_Twitter インスタンス
     */
    public function __construct(&$config = null) {
        $this->_apiConfigs =
            simplexml_load_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Twitter/API.xml');

        if ($config !== null) {
        	$this->_config = $config;
        } else {
        	$this->_config = new Services_Twitter_Config();
        }

        $this->_connection = new Services_Twitter_Connection($this->_config);
    }

	/**
	 * OAuthにてTwitterへのアクセスを行う
	 */
	public function connect() {
		$this->_connection->authorize();
	}

	/**
	 * 通信した結果のステータスコードを取得する
	 * @return int ステータスコード
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * 通信した結果の本文を取得する
	 * @return array 結果本文
	 */
	public function getBody() {
		return $this->_body;
	}

	/**
	 * Twitterにリクエストを送信するメソッド
	 * @param array $config 通信内容配列
	 * @return bool 通信が正常に終了したかどうかを返す
	 */
	private function sendRequest($config) {
		$req = $this->_connection->sendRequest($config['Target'], $config['Args'], $config['Method']);

		$this->_status = $req->getStatus();

		if ($this->_status === 200) {
			$this->_body = json_decode($req->getBody(), true);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 指定したメソッドで利用される設定情報配列を返す
	 * @param string $method 使用するメソッド名
	 * @return array 設定情報配列
	 */
	public function getOptions($method) {
		foreach ($this->_apiConfigs->API as $api) {
			$attr = $api->attributes();

			if ($attr['function'] == $method) {
				$args = $api->args;

				if (count($args) === 0) {
					return null;
				} else {
					$result = array();

					foreach($args->require as $req) {
						$attr = $req->attributes();
						$name = (string)$attr['name'];

						$result[$name] = null;
					}
					foreach($args->option as $req) {
						$attr = $req->attributes();
						$name = (string)$attr['name'];

						$result[$name] = null;
					}

					return $result;
				}
			}
		}
	}

	/**
	 * API.xmlに記述されているAPI情報を利用してAPI通信を行うマジックメソッド
	 * @param string $method メソッド名
	 * @param array $args 設定情報配列
	 * @return SimpleXMLElement 取得された内容(エラー時はnull)
	 */
	public function __call($method, $args = null) {

		if (is_array($args)) $args = $args[0];
		if ($args === null) $args = array(null);

		// 使用API決定
		foreach ($this->_apiConfigs->API as $api) {
			$attr = $api->attributes();

			if ($attr['function'] == $method) {
				$config = $api;
				break;
			} else {
				continue;
			}
		}

		// ターゲット決定
		$linked = null;
		$target = $api->target;
		if (count($target) === 1) {
			$attr = $target->attributes();
			if (count($attr) === 0) {
				$target = (string) $target;
			}
		} else {
			foreach($target as $t) {
				$attr = $t->attributes();
				if (count($attr) === 0) {
					$t = (string) $t;
				} else {
					$link = (string)$attr['link'];
					if (isset($args[$link])) {
						$t = sprintf((string) $t, $args[$link]);
						$linked = $link;
					}
				}
			}
			$target = $t;
		}

		// メソッド決定
		$method = $api->method;
		if (count($method) === 0) {
			$method = 'GET';
		} else {
			$method = (string)$method;
		}

		// 引数調整
		$new_args = array();
		foreach ($args as $key => $value) {
			if ($key === $linked) {
				continue;
			} else if ($value !== null) {
				$new_args[$key] = $value;
			}
		}
		$args = $new_args;

		// 引数チェック
		$checklist = array();
		$check_args = $api->args;
		$allcheck = true;

		if (count($check_args) !== 0) {
			// チェックリストの作成
			foreach($check_args->children() as $check) {
				$type = $check->getName();
				$attr = $check->attributes();
				$name = (string) $attr['name'];
				$select = (string)$attr['select'];
				if ($select === '') $select = $name;

				if (!isset($checklist[$select])) {
					$checklist[$select] = array('type' => $type);
				}

				$checklist[$select][] = $name;
			}

			// 引数のチェック
			foreach($checklist as $check) {
				$type = $check['type'];
				$checked = false;

				$prevkey = null;
				foreach ($check as $key => $value) {
					if ($key === 'type') continue;

					if ($type === 'require') {
						if (isset($args[$value])) {
							$checked = $checked || true;
							if (!empty($args[$prevkey])) {
								unset($args[$prevkey]);
							}
						}
					} else {
						$checked = true;
						if (isset($args[$value]) && !empty($args[$prevkey])) {
							unset($args[$prevkey]);
						}
					}
					$prevkey = $value;
				}

				$allcheck = $allcheck && $checked;
			}
		}

		if (!$allcheck) {
			return null;
		}

		$send = array(
			 'Target' => $target
			,'Args'   => $args
			,'Method' => $method
		);

		if ($this->sendRequest($send)) {
			return $this->getBody();
		} else {
			return null;
		}
	}

	/**
	 * APIを利用するメソッドのリストを取得する
	 * @return array APIメソッドリスト
	 */
	public function getAPIFunctionList() {
		$result = array();
		foreach ($this->_apiConfigs->API as $api) {
			$attr = $api->attributes();

			$args = $api->args;
			if (count($args) === 0) {
				$args = null;
			} else {
				$new = array();
				foreach ($args->children() as $arg) {
					$at = $arg->attributes();
					$group = null;
					if (isset($at['select'])) {
						$group = (string)$at['select'];
					}
					$new[(string)$at['name']] = array(
					     'comment' => (string) $at['comment']
						,'type' => $arg->getName()
						,'group' => $group
					);
				}
				$args = $new;
			}

			$result[(string)$attr['function']] = array(
				 'comment' => (string)$attr['comment']
			    ,'args' => $args
			);
		}
		return $result;
	}

	public function getAuthInfo() {
        return $this->_connection->getAuthInfo();
	}

	public function isAuthorized() {
		return $this->_connection->isAuthorized();
	}

}
?>