<?php
	
abstract class Services_Shitaraba_Model_Abstract{
	/*
		適当にメソッド増やしすぎたな…
		
		getThreadの返り値はスレッドクラスなのにgetNGThreadDataは配列のままとか
		ちょっと適当ですがきっと誰かがなおしてくれるにちがいない
	*/
	
	
    /**
     * ジャンル名をセットする
     *
     * @param string $bbsGenre    というかカテゴリ名
     * @return bool true
     */
	abstract public function setGenre($bbsGenre);
	
    /**
     * BBS名をセットする
     *
     * @param string $bbsNum    ジャンル名の後の数値
     * @return bool true
     */
	abstract public function setBbsnum($bbsNum);
	
    /**
     * 管理画面パスワードをセットする
     *
     * @param string $bbsPassword  パスワード
     * @return bool true
     */
	abstract public function setPw($bbsPassword);
	
    /**
     * 1スレッドの最大件数をセットする
	 * DAT落ち時にこの件数以上の書き込みがあるスレを対象にする
     *
     * @param int $bbsMaxNum  件数
     * @return bool 数値以外であればfalse
     */
	abstract public function setMaxNumber($bbsMaxNum);

    /**
     * DAT落ちさせるスレッドのリストを取得
	 * 
     * @return array スレッドIDの配列
     */
	abstract public function getDatThreadList();
	
    /**
	 * DAT落ちを実行する
     *
     * @param array $threadIdArray  スレッドIDの配列、getDatThreadListの返り値
     * @return bool true
     */
	abstract public function datThread($threadIdArray);
	
    /**
	 * スレッドのリストを取得する
     *
     * @return array スレッドIDの配列
     */
	abstract public function getThreadList();
	
    /**
	 * スレッドIDを指定して内容を取得する
     *
     * @param int    $threadId  スレッドID
     * @param string $ngWord    NGワードがあればNGワードが含まれ"る"発言のみを取得
     * @return Services_Shitaraba_Thread_Abstract  見つからなければfalse
     */
	abstract public function getThread($threadId, $ngWord);
	
    /**
	 * NGワードが含まれる発言を取得
     *
     * @param string $ngWord  NGワード
     * @return array array(スレッドID=>(NGワードのレス番の配列))
     */
	abstract public function getNGThreadData($ngWord);
	
    /**
	 * NGワードが含まれる発言を削除
     *
     * @param arrray $ngThreadData  NGarray(スレッドID=>(NGワードのレス番の配列))、getNGThreadDataの返り値
     * @return bool
     */
	abstract public function deleteNGThreadData($ngThreadData);
	
    /**
	 * 全スレッドの全データを取得
     *
     * @return array Services_Shitaraba_Thread_Abstractの配列
     */
	abstract public function getAllThreadData();
}