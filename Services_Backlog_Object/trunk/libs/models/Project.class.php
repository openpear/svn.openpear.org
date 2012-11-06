<?php
require_once dirname(__FILE__) . '/../BaseModel.class.php';

class Project extends BaseModel
{
    protected $fields = array(
            'id', // プロジェクトID
            'name', // プロジェクト名
            'key', // プロジェクトキー
            'url', // プロジェクトホームURL
            'archived', // ダッシュボードに表示しないかどうか 1: 表示しない 0: 表示する
            'users', // array of User
            'issues', // array of Issue
            );


    public function getIssues()
    {
        $backlog = new Services_BacklogObject('swx');
        $backlog->setCredentials('shinsaka', 'koyuki1127');

        return $backlog->findIssue(array('projectId' => $this->getId()));
    }
}