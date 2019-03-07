<?php

/**
 * simple class to generalize tree management
 */
class emol_tree {
	/**
	 * current tree, use the config option
	 * $rootId to show an subset of the tree
	 * @var mixed[]
	 */
	protected $data = array();

	/**
	 * optionally, rootId to filter on
	 * @var int
	 */
	protected $rootId;

	public function setRootId( $id ) {
		$this->rootId = (int) $id;
	}

	public function getRootId() {
		return (int) $rootId;
	}

	protected function findSubTree( $treeItems, $rootId ) {
		foreach ( $treeItems as $treeItem ) {
			if ( $treeItem['id'] == $rootId ) {
				return isset( $treeItem['children'] ) ? $treeItem['children'] : array();
			} elseif ( isset( $treeItem['children'] ) ) {
				$subtree = $this->findSubTree( $treeItem['children'], $rootId );
				if ( $subtree !== false ) {
					return $subtree;
				}
			}
		}

		return false;
	}

	public function set( $tree ) {
		$rootId = $this->rootId;

		if ( ! is_numeric( $rootId ) || $rootId == 0 ) {
			$this->data = $tree;
		} else {
			$subTree = $this->findSubTree( $tree, $rootId );

			if ( $subTree !== false ) {
				$this->data = $subTree;
			}
		}

		return $this->data;
	}

	public function get() {
		return $this->data;
	}
}
