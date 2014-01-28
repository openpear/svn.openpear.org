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
        if (!$this->hasBacklog()) {
            return array();
        }
        return $this->backlog->findIssue(array('projectId' => $this->getId()));
    }
}