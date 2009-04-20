<?php

class DB_SerializeDB
{
	private $model;

	private $index_filepath;

	private $data_filepath;

	private $space_filepath;

	private $lock_filepath;

	private $pre_lock_filepath;

	private $file_handles = array();

	const DATA_DIR = "data/";

	const INDEX_SIZE = 6;

	public function __construct($model)
	{
		$this->model = $model;

		$this->index_filepath = self::DATA_DIR . $model . ".index";

		$this->data_filepath = self::DATA_DIR . $model . ".dat";

		$this->space_filepath = self::DATA_DIR . $model . ".space";

		$this->lock_filepath = self::DATA_DIR . $model . ".lock";

		$this->pre_lock_filepath = uniqid( self::DATA_DIR . $model, true );
	}

	private function readIndex($id)
	{
		$ih = $this->getIndexHandle();

		$seek_offset = self::INDEX_SIZE * ($id - 1);

		$res = fseek($ih, $seek_offset);

		if( $res != 0 )
		{
			trigger_error("Invalid id: $id");
			return false;
		}

		$index = $this->readIndexRecord($ih);

		return $index;
	}

	private function readIndexRecord($ih)
	{
		$data = fread($ih, self::INDEX_SIZE);

		if( strlen($data) < self::INDEX_SIZE) return false;

		return unpack("N1start/n1size", $data);
	}

	private function getIndexSize()
	{
		$ih = $this->getIndexHandle();

		fseek($ih, 0, SEEK_END);

		$size = ftell($ih);

		return $size / self::INDEX_SIZE;
	}

	private function getIndexHandle()
	{
		$h = $this->getFileHandle($this->index_filepath);

		return $h;
	}

	private function getDataHandle()
	{
		return $this->getFileHandle($this->data_filepath);
	}

	private function getSpaceHandle()
	{
		return $this->getFileHandle($this->space_filepath);
	}

	private function getFileHandle($fp)
	{
		if(isset($this->file_handles[$fp])) return $this->file_handles[$fp];

		if(!file_exists($fp)) touch($fp);

		$this->file_handles[$fp] = fopen($fp, "rb+");

		return $this->file_handles[$fp];
	}

	private function byteLen($data)
	{
		return strlen(bin2hex($data)) / 2;
	}

	public function Add($data)
	{
		$this->lock();

		//データの書き込み
		$data = serialize($data);

		$dh = $this->getDataHandle();

		//スペースがあるか？
		$start = $this->getSpace($this->byteLen($data));

		$start = $start === false ? -1 : $start;

		$index = $this->writeData($dh, $start, $data);

		//インデックスに追加
		$id = $this->addIndex($index);

		$this->unlock();

		return $id;
	}

	public function Save($data, $id)
	{
		$this->lock();

		$data = serialize($data);

		$index = $this->readIndex($id);

		if($index === false)
		{
			$this->unlock();
			return false;
		}

		$dh = $this->getDataHandle();

		//元の場所に入るか？
		$len = $this->byteLen($data);
		if($len <= $index["size"])
		{
			//上書き
			$this->writeData($dh, $index["start"], $data);

			//スペースができるか？
			if($len < $index["size"])
			{
				//スペースの追加
				$this->addSpace($index["start"] + $len, $index["size"] - $len);

				//インデックスの更新
				$this->saveIndex($index["start"], $len, $id);
			}
		}
		else //追加
		{
			$new_index = $this->writeData($dh, -1, $data);

			//元のアドレスをスペースに
			$this->addSpace($index);

			//インデックスの上書き
			$this->saveIndex($new_index["start"], $new_index["size"], $id);
		}

		$this->unlock();

		return true;
	}

	private function addIndex($index)
	{
		$ih = $this->getIndexHandle();

		$this->writeIndexRecord($ih, -1, $index["start"], $index["size"]);

		$index_size = ftell($ih);

		return $index_size / self::INDEX_SIZE;
	}

	private function saveIndex($start, $size, $id)
	{
		$ih = $this->getIndexHandle();

		$this->writeIndexRecord($ih, $id - 1, $start, $size);
	}

	public function Read($id)
	{
		$index = $this->readIndex($id);

		if($index === false) return false;//エラー

		if($index["size"] == 0) return null;//空

		$hd = $this->getDataHandle();

		fseek($hd, $index["start"]);

		$data = fread($hd, $index["size"]);

		$data = unserialize($data);

		return $data;
	}

	public function Slice($condition = array(), $offset = 1, $length = 0)
	{
		$ih = $this->getIndexHandle();

		$index_size = $this->getIndexSize();

		//length処理
		if($length > $index_size || $length == 0) $length = $index_size;

		//offset処理
		$reverse = false;
		if($offset == -1)
		{
			$reverse = true;
			$offset = $index_size;
		}

		rewind($ih);

		$res = array();

		$id = $offset;
		while(count($res) < $length && $id > 0)
		{
			$data = $this->Read($id);

			if($data !== null)
			{
				$valid = true;

				foreach($condition as $key => $value)
				{
					$valid = isset($data[$key]) && $data[$key] == $value;
					if(!$valid) break;
				}

				if($valid) $res[$id] = $data;
			}
			(!$reverse) ? $id++ : $id--;
		}

		return $res;
	}

	private function addSpace($index)
	{
		//初期値の準備
		$index["end"] = $index["start"] + $index["size"];
		$start_matched_at = false;
		$end_matched_at = false;
		$empty_index = null;

		//全部検索
		$sh = $this->getSpaceHandle();
		fseek($sh, 0);

		for($i = 0; true; $i++)
		{
			$n_space = $this->readIndexRecord($sh);

			if($n_space == false) break;

			if($n_space["size"] > 0)
			{
				if( $index["start"] == $n_space["start"] + $n_space["size"]) //始点がどっかの終端と一致するか。
				{
					$start_matched_at = array($i, $n_space);
				}
				elseif($index["end"] == $n_space["start"]) //終端がどっかの始点に一致するか
				{
					$end_matched_at = array($i, $n_space);
				}
			}
			else // size == 0 追加用に取っておく
			{
				if($empty_index === null) $empty_index = $i;
			}

			if($start_matched_at !== false && $end_matched_at !== false) break;
		}

		//始点.終端処理
		if($start_matched_at !== false && $end_matched_at !== false)
		{
			list($i, $n_space) = $start_matched_at;
			list($i_end, $space_end) = $end_matched_at;

			//始点の結合先を上書き（サイズ）
			$this->writeIndexRecord($sh, $i, $n_space["start"] ,$index["size"] + $n_space["size"] + $space_end["size"]);

			//終端の結合先インデックスの削除
			$this->writeIndexRecord($sh, $i_end);
		}
		//始点処理
		elseif($start_matched_at !== false)
		{
			//始点の結合先を上書き（サイズ）
			list($i, $n_space) = $start_matched_at;

			$this->writeIndexRecord($sh, $i, $n_space["start"] ,$index["size"] + $n_space["size"]);
		}
		//終端処理
		elseif($end_matched_at !== false)
		{
			//終端の結合先を上書き（始点、サイズ）
			list($i, $n_space) = $end_matched_at;

			$this->writeIndexRecord($sh, $i, $index["start"] ,$index["size"] + $n_space["size"]);
		}
		//隣接しなかった場合追加
		else
		{
			$i = ($empty_index === null) ? -1 : $empty_index;
			$this->writeIndexRecord($sh, $i, $index["start"], $index["size"]);
		}
	}

	/*
	 * サイズ以下のスペースがあれば、その始点を返す。
	 * なければfalse
	 * また、あった場合は、サイズ分のスペースを始点から削除する。
	 */
	private function getSpace($size)
	{
		$sh = $this->getSpaceHandle();
		rewind($sh);

		for($i = 0; true; $i++)
		{
			$index = $this->readIndexRecord($sh);

			if($index === false) break;

			if($index["size"] >= $size)
			{
				//スペースの削除
				$space_size = $index["size"] - $size;
				$space_start = ($space_size == 0) ? 0 : $index["start"] + $size;
				$this->writeIndexRecord($sh, $i, $space_start, $space_size);

				return $index["start"];
			}
		}
		return false;
	}

	private function writeData($handle, $start, $data)
	{
		if($start != -1)
		{
			fseek($handle, $start);
		}
		else
		{
			fseek($handle, 0, SEEK_END);
			$start = ftell($handle);
		}

		$size = fwrite($handle, $data);

		return array("start" => $start, "size" => $size);
	}

	private function writeIndexRecord($handle, $i, $start = 0, $size = 0)
	{
		if($i != -1)
		{
			fseek($handle, $i * self::INDEX_SIZE);
		}
		else
		{
			fseek($handle, 0, SEEK_END);
		}

		$data = pack("Nn", $start, $size);

		fwrite($handle, $data);
	}

	public function Remove($id)
	{
		$this->lock();

		$index = $this->readIndex($id);

		if($index !== false)
		{
			$this->addSpace($index);

			$ih = $this->getIndexHandle();

			$this->writeIndexRecord($ih, $id - 1, 0, 0);
		}

		$this->unlock();

		if($index === false) return false;
	}

	private function lock()
	{
		$break = false;
		while(!$break)
		{
			file_put_contents($this->pre_lock_filepath, $this->pre_lock_filepath);

			while( file_exists($this->lock_filepath) )
			{
				usleep(100);
			}
			rename($this->pre_lock_filepath, $this->lock_filepath);

			//リネームが衝突していないか確認
			if($this->pre_lock_filepath == file_get_contents($this->lock_filepath))
			{
				$break = true;
			}
		}
	}

	private function unlock()
	{
		unlink($this->lock_filepath);

		if(file_exists($this->pre_lock_filepath)) unlink($this->pre_lock_filepath);
	}
}
?>
