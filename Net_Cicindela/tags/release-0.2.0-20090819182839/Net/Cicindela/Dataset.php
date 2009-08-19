<?php
/**
 * レコメンドエンジンCicindelaのデータセット
 * 
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt. If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category Net
 * @package Net_Cicindela
 * @author TANAKA Koichi <tanaka@ensites.com>
 * @copyright authors
 * @license http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version $Id$
 * @link http://d.hatena.ne.jp/Mugeso/
 * @see http://code.google.com/p/cicindela2/
 */
require_once 'Net/Cicindela.php';
class Net_Cicindela_Dataset
{
    /**
     *
     * @var string データセット名
     */
    protected $name;

    /**
     *
     * @var Net_Cicindela
     */
    protected $cicindela;

    /**
     * コンストラクタ
     *
     * @param string $name データセット名
     * @param mixed $cicindela Net_CicindelaインスタンスかベースURL
     */
    public function __construct($name, $cicindela = 'http://localhost/cicindela/') {
        $this->name = $name;

        if($cicindela instanceof Net_Cicindela) {
            $this->cicindela = $cicindela;
        } else {
            $this->cicindela = new Net_Cicindela($cicindela);
        }
    }

    /**
     * アイテムを購入/閲覧
     *
     * @param mixed $user_id 主体ユーザID
     * @param mixed $item_id 対象アイテムID
     */
    public function pickItem($user_id, $item_id)
    {
        return $this->record('insert_pick', compact('user_id', 'item_id'));
    }

    /**
     * アイテムへ投票
     *
     * @param mixed $user_id 主体ユーザID
     * @param mixed $item_id 対象アイテムID
     * @param int   $raging  評価
     */
    public function vote($user_id, $item_id, $rating)
    {
        return $this->record('insert_rating', compact('user_id', 'item_id', 'rating'));
    }

    /**
     * アイテムにタグ付け
     *
     * @param mixed $user_id 主体ユーザID
     * @param mixed $item_id 対象アイテムID
     * @param mixed $tag_id  タグのID
     */
    public function tag($user_id, $item_id, $tag_id)
    {
        return $this->record('insert_tag', compact('user_id', 'item_id', 'tag_id'));
    }

    /**
     * アイテムにカテゴリを設定
     *
     * @param mixed $item_id 対象アイテムID
     * @param mixed $category_id カテゴリID
     */
    public function setCategory($item_id, $category_id)
    {
        return $this->record('set_category', compact('item_id', 'category_id'));
    }

    /**
     * アイテムを購入/閲覧を取り消し
     *
     * @param mixed $user_id
     * @param mixed $item_id
     */
    public function unpickItem($user_id, $item_id) {
        return $this->record('delete_pick', compact('user_id', 'item_id'));
    }

    /**
     * アイテムへの評価を取り消し
     *
     * @param mixed $user_id
     * @param mixed $item_id
     */
    public function unvote($user_id, $item_id)
    {
        return $this->record('delete_rating', compact('user_id', 'item_id', 'rating'));
    }

    /**
     * アイテムのタグを取り消し
     *
     * @param mixed $user_id
     * @param mixed $item_id
     * @param mixed $tag_id
     */
    public function untag($user_id, $item_id, $tag_id)
    {
        return $this->record('delete_tag', compact('user_id', 'item_id', 'tag_id'));
    }

    /**
     * カテゴリ付を取り消し
     *
     * @param mixed $item_id
     * @param mixed $category_id
     */
    public function removeCategory($item_id, $category_id)
    {
        return $this->record('remove_category', compact('item_id', 'category_id'));
    }

    /**
     * データ入力を記録する
     * 
     * @param string $op 処理内容
     * @param array $param パラメータ
     */
    protected function record($op, $param)
    {
        $param['set'] = $this->name;
        $param['op'] = $op;

        return $this->cicindela->record($param);
    }

    /**
     * アイテムに対するレコメンドを取得する
     *
     * @param mixed $item_id   対象アイテムID
     * @param mixed $limit     取得するレコメンド数
     * @param mixed $categotry カテゴリを絞り込む場合に指定する
     * @return array
     */
    public function getRecommendForItem($item_id, $limit = 10, $categotry = null)
    {
        return $this->getRecommend('for_item', compact('item_id', 'limit', 'category'));
    }

    /**
     * ユーザに対するレコメンドを取得する
     *
     * @param mixed $user_id   対象ユーザID
     * @param mixed $limit     取得するレコメンド数
     * @param mixed $categotry カテゴリを絞り込む場合に指定する
     * @return array
     */
     public function getRecommendForUser($user_id, $limit = 20, $category = null)
    {
        return $this->getRecommend('for_user', compact('user_id', 'limit', 'category'));
    }

    /**
     * 似ているユーザを取得する
     *
     * @param mixed $user_id   対象ユーザID
     * @param mixed $limit     取得するユーザ数
     * @param mixed $categotry カテゴリを絞り込む場合に指定する
     * @return array
     */
    public function getSimilarUsers($user_id, $limit = 20, $category = null) {
        return $this->getRecommend('similar_users', compact('user_id', 'limit', 'category'));
    }

    /**
     * レコメンドを取得する
     *
     * @param array $op レコメンドの種類
     * @param array $param パラメータ
     * 
     * @return array
     */
    protected function getRecommend($op, $param)
    {
        $param['set'] = $this->name;
        $param['op'] = $op;

        return $this->cicindela->getRecommend(array_filter($param));
    }
}
?>
