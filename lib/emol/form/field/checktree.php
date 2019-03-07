<?php

abstract class emol_form_field_checktree extends emol_form_field {
	/**
	 * local reference to tree manager
	 * @var emol_tree $tree
	 */
	protected $tree;


	protected $value = array();

	/**
	 * construct the field and set the configuration
	 */
	public function __construct( $config = array() ) {
		parent::__construct( $config );
	}

	protected function ensureTree( $force = false ) {
		if ( $force || empty( $this->tree ) ) {
			$this->tree = new emol_tree();
			$this->tree->setRootId( $this->getConfig( 'treeroot_id' ) );
			$this->fillTree();
		}
	}

	/**
	 * fill the current tree with content
	 */
	abstract protected function fillTree();

	/**
	 * rewritten default value action because string was enforced
	 */
	public function setValue( $value ) {
		$this->value = $value;
	}

	/**
	 * detect the value of this field in the postObject
	 */
	public function detectPostValue() {
		$value = emol_post( $this->getName(), array() );
		$value = is_array( $value ) ? $value : array();
		$this->setValue( $value );
	}

	public function setTreeItems( $treeItems ) {
		$this->tree->set( $treeItems );
	}

	public function getTreeItems() {
		return $this->tree->get();
	}

	public function getElement() {
		// make sure tree data is present
		$this->ensureTree();

		// get the currently known tree items
		$tree = $this->getTreeItems();

		$rendered = '<div id="' . $this->getId() . '" class="emol_checktree ' . $this->getClass() . '">' . $this->renderTreeItems( $tree ) . '</div>';
		$rendered = str_replace( "\n", '', $rendered );

		return $rendered;
	}

	protected function renderTreeItems( $treeItems, $level = 0 ) {
		$rendered = '';
		$value    = $this->getValue();

		foreach ( $treeItems as $treeItem ) {
			$itemId  = $this->getId() . '_' . $treeItem['id'];
			$checked = in_array( $treeItem['id'], $value ) ? 'checked="checked"' : '';

			$rendered .= '<li class="emol_checktree_leaf emol_checktree_leaf_' . $level . '" id="emol_checktree_id_' . $treeItem['id'] . '">';
			$rendered .= '<input
					type="checkbox"  
					value="' . $treeItem['id'] . '"  
					name="' . $this->getName() . '[]" 
					id="' . $itemId . '" 
					' . $checked . '
				/>';
			$rendered .= '<label for="' . $itemId . '" id="emol_checktree_label_' . $treeItem['id'] . '">';
			$rendered .= $treeItem['name'];
			$rendered .= '</label>';

			if ( isset( $treeItem['children'] ) && is_array( $treeItem['children'] ) ) {
				$rendered .= $this->renderTreeItems( $treeItem['children'], $level + 1 );
			}

			$rendered .= '</li>';
		}

		if ( ! empty( $rendered ) ) {
			$rendered = '<ul class="emol_checktree_map emol_checktree_map_' . $level . '">' . $rendered . '</ul>';
		}

		return $rendered;
	}
}
