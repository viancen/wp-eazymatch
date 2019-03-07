<?php

class emol_form_admin {
	private $adminPage = '';

	private $formInstanceName = '';

	private $listId = false;

	function __construct( $formInstanceName ) {
		$this->formInstanceName = $formInstanceName;
		$this->detectAdminPage();
		$this->detectListId();
	}

	public function detectAdminPage() {
		if ( array_key_exists( 'page', $_GET ) ) {
			$this->setAdminPage( $_GET['page'] );
		}
	}

	public function setAdminPage( $pageName ) {
		$this->adminPage = $pageName;
	}

	public function getAdminPage() {
		return $this->adminPage;
	}

	public function detectListId() {
		if ( array_key_exists( 'listId', $_GET ) ) {
			$this->setListId( $_GET['listId'] );
		} else {
			// set first forminstance if present
			$formManager = emol_form_manager::getInstance();
			$listIds     = $formManager->getListIds( $this->formInstanceName );

			if ( count( $listIds ) > 0 ) {
				$this->setListId( $listIds[0] );
			}
		}
	}

	public function setListId( $listId ) {
		$formManager = emol_form_manager::getInstance();
		$listIds     = $formManager->getListIds( $this->formInstanceName );

		if ( in_array( $listId, $listIds ) || $listId == 'create' ) {
			$this->listId = $listId;
		}
	}

	public function getListId() {
		return $this->listId;
	}

	public function display() {
		$this->detectRequest();
		$this->render();
	}

	public function detectRequest() {
		if ( count( $_POST ) == 0 || ! isset( $_POST['emol_action'] ) ) {
			return;
		}

		$listId      = $this->getListId();
		$formManager = emol_form_manager::getInstance();

		if ( $listId == 'create' ) {
			$formInstance = $formManager->create( $this->formInstanceName );
		} elseif ( $listId === false ) {
			return;
		} else {
			$formInstance = $formManager->get( $listId );
		}


		switch ( $_POST['emol_action'] ) {
			case 'forminstance_save':

				if ( $_POST['submit'] == __( 'Delete' ) ) {
					$formManager->remove( $formInstance );
					// dirty dirty hack, wordpress has headers already send so redirect is via javascript
					echo '<script>window.location = "' . get_admin_url() . 'admin.php?page=' . $_GET['page'] . '"</script>';
					exit();
				}

				$formInstance->setFieldConfig( isset( $_POST['fieldconfig'] ) ? $_POST['fieldconfig'] : array() );
				$formInstance->setLabel( htmlspecialchars( $_POST['label'] ) );
				$formManager->persistInstanceConfig( $formInstance );
				$instanceId = $formManager->findInstanceId( $formInstance );

				// dirty dirty hack, wordpress has headers already send so redirect is via javascript
				echo '<script>window.location = "' . get_admin_url() . 'admin.php?page=' . $_GET['page'] . '&listId=' . $instanceId . '"</script>';
				exit();
				break;
		}
	}

	public function render() {
		// get all currently available forms and the currently selected form
		$formManager = emol_form_manager::getInstance();
		$listIds     = $formManager->getListIds( $this->formInstanceName );
		$listId      = $this->getListId();

		$formInstance = false;
		if ( is_numeric( $listId ) ) {
			$formInstance = $formManager->get( $listId );
		} elseif ( $listId == 'create' ) {
			$formInstance = new $this->formInstanceName( array() );
		}

		echo emol_view_load( 'form/admin.php', array(
			'adminPage'     => $this->getAdminPage(),
			'listIds'       => $listIds,
			'currentListId' => $listId,
			'formInstance'  => $formInstance
		) );
	}
}