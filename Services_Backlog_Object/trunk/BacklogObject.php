<?php
require_once 'XML/RPC.php';
require_once 'libs/models/Project.class.php';
require_once 'libs/models/Issue.class.php';

class Services_BacklogObject {
    protected $space_id;
    protected $client;  /* @var $this->client XML_RPC_Client */
    protected $method_prefix = 'backlog';

    protected $api_path = '/XML-RPC';
    protected $server_suffix = 'backlog.jp';
    protected $port = 443;

    protected $user;
    protected $password;

    public function __construct($space_id = null)
    {
        if (!is_null($space_id)) {
            $this->space_id = $space_id;
        }
    }

    protected function getClient()
    {
        if (!$this->client instanceof XML_RPC_Client) {
            if (is_null($this->space_id)) {
                $this->client = null;
            } else {
                $this->client = new XML_RPC_Client($this->api_path, sprintf('%s.%s',$this->space_id ,$this->server_suffix), $this->port);
            }
        }
        return $this->client;
    }

    /**
     * credentials set
     *
     * @param string $user
     * @param string $password
     * @throws Services_BacklogObjectException
     */
    public function setCredentials($user, $password)
    {
        try {
            $this->user = $user;
            $this->password = $password;

            $this->getClient()->setCredentials($this->user, $this->password);
        } catch (Exception $e) {
            throw new Services_BacklogObjectException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * XML-RPCサーバへリクエストを送信する
     *
     * @access private
     * @param  XML_RPC_Message $message
     * @return array
     */
    protected function sendMessage($method, $parameters = array())
    {
        $response = $this->getClient()->send(new XML_RPC_Message(sprintf('%s.%s', $this->method_prefix, $method), $parameters)); /* @var $response XML_RPC_Response */
        if ($response->faultCode()) {
            throw new Services_BacklogObjectException($response->faultString(), $response->faultCode());
        }
        $xml = $response->serialize();
        return xmlrpc_decode($xml);
    }

    /**
     * 参加プロジェクトを取得する
     *
     * @return array
     */
    public function getProjects()
    {
        $xml_projects = $this->sendMessage('getProjects');
        $projects = array();
        foreach ($xml_projects as $xml_project) {
            $projects[] = new Project($xml_project);
        }
        return $projects;
    }

    /**
     * 課題を検索
     *
     * @param array $params
     *
     * @return array
     */
    public function findIssue($params = array())
    {
        if (!array_key_exists('projectId', $params)) {
            throw new Services_BacklogObjectException('no project id', 101);
        }
        $xmlParams = array();
        $xmlParams['projectId'] = new XML_RPC_Value($params['projectId'], 'int');

        $xml_issues = $this->sendMessage('findIssue', array(new XML_RPC_Value($xmlParams, 'struct')));
        $issues = array();
        foreach ($xml_issues as $xml_issue) {
            $issues[] = new Issue($xml_issue);
        }
        return $issues;
    }
}

/**
 * Exception
 */
class Services_BacklogObjectException extends Exception
{
}
