<?php
require_once dirname(__FILE__) . '/../BaseModel.class.php';

class Issue extends BaseModel
{
    protected $fields = array(
            'id', // 課題ID
            'key', //課題キー
            'summary', //件名
            'description', //詳細
            'url', //課題のURL
            'due_date', // 期限日
            'start_date', // 開始日
            'estimated_hours', //予定時間
            'actual_hours', // 実績時間
            'issueType', //種別
            '├ id', //種別ID
            '├ name', //種別名
            '└ color', // 種別色
            'priority', // 優先度
            '├ id', //優先度ID
            '└ name', //優先度
            'resolution', // 完了理由
            '├ id', //完了理由ID
            '└ name', //完了理由
            'status', // 状態
            '├ id', //状態ID
            '└ name', //状態
            'components', // カテゴリ
            '├ id', //カテゴリID
            '└ name', //カテゴリ名
            'versions', // 発生バージョン
            '├ id', //発生バージョンID
            '├ name', //発生バージョン名
            '└ date', //リリース予定日
            'milestones', // マイルストーン
            '├ id', //マイルストーンID
            '├ name', //マイルストーン名
            '└ date', //リリース予定日
            'created_user', // 登録者
            '├ id', //ユーザID
            '└ name', //ハンドルネーム
            'assigner', // 担当者
            '├ id', //ユーザID
            '└ name', //ハンドルネーム
            'created_on', // 登録日時
            'updated_on', // 最終更新日時
    );
}