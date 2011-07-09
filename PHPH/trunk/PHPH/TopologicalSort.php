<?php

class PHPH_TopologicalSort
{
	public $edges = null;
	public $nodes = null;

	public function __construct(array $edges=array())
	{
		$this->edges = $edges;
		$this->nodes = array();
	}

	public function addEdge($edge)
	{
		if (!in_array($edge, $this->edges)) {
			$this->edges[] = $edge;
		}
	}

	public function addNode($u, $v=null)
	{
		if (!isset($u) && isset($v)) {
			$u = $v;
			unset($v);
		}
		if (!in_array($u, $this->edges)) {
			$this->edges[] = $u;
		}
		if (isset($v) && !in_array($v, $this->edges)) {
			$this->edges[] = $v;
		}

		if (!isset($this->nodes[$u])) {
			$this->nodes[$u] = array();
		}
		if (isset($v) && !in_array($v, $this->nodes[$u])) {
			$this->nodes[$u][] = $v;
		}
	}

	public function sort($edges=null, $nodes=null, $result=array())
	{
		if (is_null($edges)) {
			$edges = $this->edges;
		}
		if (is_null($nodes)) {
			$nodes = $this->nodes;
		}
		$diff = self::arrayDiff($edges, array_values($nodes));
		foreach ($diff as $item) {
			$result[] = $item;
			unset($nodes[$item]);
		}
		$next_edges = self::arrayDiff($edges, $diff);
		if (0<count($next_edges)) {
			$result = $this->sort($next_edges, $nodes, $result);
		}
		return $result;
	}

	public static function arrayFlatten($arr)
	{
		$result = array();
		if (is_array($arr)) {
			foreach ($arr as $item) {
				if (is_array($item)) {
					$result = array_merge($result, self::arrayFlatten($item));
				} else {
					$result[] = $item;
				}
			}
		} else {
			$result[] = $arr;
		}
		return $result;
	}

	public static function arrayDiff($arr1, $arr2)
	{
		return array_diff($arr1, array_unique(self::arrayFlatten($arr2)));
	}
}
