<?php
require_once 'XML/RPC.php';
//require_once 'Services/Backlog/Exception.php';
require_once 'Exception/Exception.php';
/*
 * Backlog(http://backlog.jp)のAPIにアクセスするクラス
 * PHP5以上対象
 *
 * @package Services_Backlog
 * @author  devworks <smoochynet@gmail.com>
 * @access  public
 * @version Release: 0.10.0
 * @uses    XML_RPC
 */
class Services_Backlog {
    /**
     * パス
     * @var string PATH
     */
    const PATH = '/XML-RPC';

    /**
     * プレフィックス
     *
     * @var string PREFIX
     */
    const PREFIX = 'backlog.';

    /**
     * ポート
     */
    const PORT = 443;

    /**
     * XML
     *
     * @var XML_RPC_Client $client
     */
    protected $client;

    /**
     * エラーコード
     */
    const INVALID_ARGMENTS = 101;
    /**
     * コンストラクタ
     * 
     * @param string $url
     * @param string $user
     * @param string $password
     */
    public function __construct($host, $user, $password) {
        if (is_null($host) || is_null($user) || is_null($password)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $this->client = new XML_RPC_Client(self::PATH, $host, self::PORT);
        $this->client->setCredentials($user, $password);
    }

    /**
     * 参加プロジェクトを取得する
     * 
     * @return array APIの結果
     */
    public function getProjects() {
        $message = new XML_RPC_Message(self::PREFIX . 'getProjects');
        return $this->_send($message);
    }

    /**
     * 指定したキーのプロジェクトを取得する
     * 
     * @param  string|integer $projectKey
     * @return array          APIの結果
     */
    public function getProject($projectKey) {
        if (is_null($projectKey)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramKey = null;
        if (is_numeric($projectKey)) {
            $paramKey = new XML_RPC_Value(intval($projectKey), 'int');
        } else {
            $paramKey = new XML_RPC_Value($projectKey, 'string');
        }
        $message = new XML_RPC_Message(self::PREFIX . 'getProject', array($paramKey));
        return $this->_send($message);
    }

    /**
     * プロジェクトのカテゴリを取得する
     *
     * @param  integer $projectId
     * @return array   APIの結果
     */
    public function getComponents($projectId) {
        if (is_null($projectId)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = new XML_RPC_Value($projectId, 'int');
        $message = new XML_RPC_Message(self::PREFIX . 'getComponents', array($paramId));
        return $this->_send($message);
    }
    /**
     * プロジェクトの発生バージョン/マイルストーンを取得する
     * 
     * @param  integer $projectId
     * @return array   APIの結果
     */
    public function getVersions($projectId) {
        if (is_null($projectId)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = new XML_RPC_Value($projectId, 'int');
        $message = new XML_RPC_Message(self::PREFIX . 'getVersions', array($paramId));
        return $this->_send($message);
    }

    /**
     * プロジェクトの発生バージョン/マイルストーンを取得する
     *
     * @param  integer $projectId
     * @return array   APIの結果
     */
    public function getUsers($projectId) {
        if (is_null($projectId)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = new XML_RPC_Value($projectId, 'int');
        $message = new XML_RPC_Message(self::PREFIX . 'getUsers', array($paramId));
        return $this->_send($message);
    }

    /**
     * プロジェクトの種別を取得する
     *
     * @param  integer $projectId
     * @return array   APIの結果
     */
    public function getIssueTypes($projectId) {
        if (is_null($projectId)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = new XML_RPC_Value($projectId, 'int');
        $message = new XML_RPC_Message(self::PREFIX . 'getIssueTypes', array($paramId));
        return $this->_send($message);
    }
    /**
     * プロジェクトの種別を取得する
     *
     * @param  string|integer  $issueKey
     * @return array           APIの結果
     */
    public function getIssue($issueKey) {
        if (is_null($issueKey)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = null;
        if (is_numeric($issueKey)) {
            $paramId = new XML_RPC_Value(intval($issueKey), 'int');
        } else {
            $paramId = new XML_RPC_Value($issueKey, 'string');
        }
        $message = new XML_RPC_Message(self::PREFIX . 'getIssue', array($paramId));
        return $this->_send($message);
    }

    /**
     * 課題のコメントを取得する
     *
     * @param  integer $issueId
     * @return array   APIの結果
     */
    public function getComments($issueId) {
        if (is_null($issueId)) {
            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
        }
        $paramId = new XML_RPC_Value($issueId, 'int');
        $message = new XML_RPC_Message(self::PREFIX . 'getComments', array($paramId));
        return $this->_send($message);
    }
//    public function countIssue($params = array()) {
//        if (!array_key_exists('projectId', $params) || !isset ($params['projectId'])) {
//            throw new Services_Backlog_Exception('invalid argments', self::INVALID_ARGMENTS);
//        }
//        $xmlParams = array();
//        array_push($xmlParams, new XML_RPC_Value($params['projectId'], 'int'));
//        $message = new XML_RPC_Message(
//            self::PREFIX . 'countIssue', array(
//                new XML_RPC_Value(array('projectId' => new XML_RPC_Value($params['projectId'], 'int')), 'struct')));
//        return $this->_send($message);
//        if (array_key_exists('issueTypeId', $params) && isset ($params['issueTypeId'])) {
//            array_push($xmlParams, array('projectId' => new XML_RPC_Value($params['issueTypeId'], 'string')));
//        }
//        if (array_key_exists('componentId', $params) && isset ($params['componentId'])) {
//            if (is_array($params['componentId'])) {
//                $c = array();
//                foreach ($params['componentId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['issueTypeId'], 'int'));
//            }
//        }
//        if (array_key_exists('versionId', $params) && isset ($params['versionId'])) {
//            if (is_array($params['versionId'])) {
//                $c = array();
//                foreach ($params['versionId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['versionId'], 'int'));
//            }
//        }
//        if (array_key_exists('milestoneId', $params) && isset ($params['milestoneId'])) {
//            if (is_array($params['milestoneId'])) {
//                $c = array();
//                foreach ($params['milestoneId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, array('milestoneId' => new XML_RPC_Value($c, 'struct')));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['milestoneId'], 'int'));
//            }
//        }
//        if (array_key_exists('statusId', $params) && isset ($params['statusId'])) {
//            if (is_array($params['statusId'])) {
//                $c = array();
//                foreach ($params['statusId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['statusId'], 'int'));
//            }
//        }
//        if (array_key_exists('priorityId', $params) && isset ($params['priorityId'])) {
//            if (is_array($params['priorityId'])) {
//                $c = array();
//                foreach ($params['priorityId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['priorityId'], 'int'));
//            }
//        }
//        if (array_key_exists('assignerId', $params) && isset ($params['assignerId'])) {
//            if (is_array($params['assignerId'])) {
//                $c = array();
//                foreach ($params['assignerId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['assignerId'], 'int'));
//            }
//        }
//        if (array_key_exists('createdUserId', $params) && isset ($params['createdUserId'])) {
//            if (is_array($params['createdUserId'])) {
//                $c = array();
//                foreach ($params['createdUserId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['createdUserId'], 'int'));
//            }
//        }
//        if (array_key_exists('resolutionId', $params) && isset ($params['resolutionId'])) {
//            if (is_array($params['resolutionId'])) {
//                $c = array();
//                foreach ($params['resolutionId'] as $component) {
//                    array_push($c, new XML_RPC_Value($component, 'int'));
//                }
//                array_push($xmlParams, new XML_RPC_Value($c, 'array'));
//            } else {
//                array_push($xmlParams, new XML_RPC_Value($params['resolutionId'], 'int'));
//            }
//        }
//        if (array_key_exists('created_on_min', $params) && isset ($params['created_on_min'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['created_on_min'], 'string'));
//        }
//        if (array_key_exists('created_on_max', $params) && isset ($params['created_on_max'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['created_on_max'], 'string'));
//        }
//        if (array_key_exists('updated_on_min', $params) && isset ($params['updated_on_min'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['updated_on_min'], 'string'));
//        }
//        if (array_key_exists('updated_on_max', $params) && isset ($params['updated_on_max'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['updated_on_max'], 'string'));
//        }
//        if (array_key_exists('start_date_min', $params) && isset ($params['start_date_min'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['start_date_min'], 'string'));
//        }
//        if (array_key_exists('start_date_max', $params) && isset ($params['start_date_max'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['start_date_max'], 'string'));
//        }
//        if (array_key_exists('due_date_min', $params) && isset ($params['due_date_min'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['due_date_min'], 'string'));
//        }
//        if (array_key_exists('due_date_max', $params) && isset ($params['due_date_max'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['due_date_max'], 'string'));
//        }
//        if (array_key_exists('query', $params) && isset ($params['query'])) {
//            array_push($xmlParams, new XML_RPC_Value($params['query'], 'string'));
//        }
//        $message = new XML_RPC_Message(self::PREFIX . 'countIssue', $xmlParams);
//        return $this->_send($message);
//    }
    /**
     * XML-RPCサーバへリクエストを送信する
     *
     * @access private
     * @param  XML_RPC_Message $message
     * @return array
     */
    private function _send(XML_RPC_Message $message) {
        $result = $this->client->send($message);
        if ($result->faultCode()) {
            throw new Services_Backlog_Exception($result->faultString(), $result->faultCode());
        }
        $xml = $result->serialize();
        return xmlrpc_decode($xml);
    }
}
?>
