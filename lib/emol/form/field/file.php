<?php

class emol_form_field_file extends emol_form_field_input {
	protected $inputType = 'file';

	protected $postValue;

	public function getElement() {
		// prevent value of field being added to file input
		$value = $this->getValue();

		$this->setValue( '' );

		$element = parent::getElement();

		$this->setValue( $value );

		if ( is_array( $value ) && isset( $value['id'] ) && $value['id'] > 0 ) {
			$downloadUrl = str_replace( '{id}', $value['id'], $this->getConfig( 'downloadUrl', '' ) );
			if ( $downloadUrl != '' ) {
				//$downloadLink = '<a href="' . $downloadUrl . '" title="' . $value['originalname'] . '"><img alt="'.$value['originalname'].'" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACHUlEQVR42n2Tu4sTURTG584rmdm8iEYRrERQUWELa9m/II0gW0S2FSxsBEGxUQsFLSy2lQjLEsRexG20URAtV7EREatIkASZR2bu+DvjzTLGx4WPk73n+77z2DvK+vMcBgNwFhwAORiDl+Ah+FYlqyXxBrihlHKImRHLkb/doihS4nXw5DcDIxgSpeoP4pyolwopDHxiSHwKLu4Z2LZ9E9EAiFgqWxDuZlk2kt+u665zf9Xce2AFbGqt7ynER8GziphcUZvP56eqs3me95q8ZzoREx+DNYX7NcdxLmASiVjIJMIkSU5UDXzffwPPXowknDzPNxWJHUwOmrnLQyKM4/h41aBer781BoUZxWfEjyoIgvcYJBhoLssKJLIois5UDeC9gheKdsGFFqtGo7FLIuVC2roym82eW/85zWZzjXEfYFKwp1QMdhhjHwYuF99xXaf9r38TM8Yhim2D/ZjoNE0/qVardYsN93GU1gOWN2UH50h+XlriEXbwuFartTCIpGM4W4qWTnI5IpkzgoNJHZMIk/PEDyImv0r+ETFEHFM9h2eT75cPqdvt3qZCX9oSE0YRkwSSfBMriIa0by3EcGyqb08mkzulQbvd9jAYMspp2bAQMAlA+VDJ5UasTZcv8L88nU7zvY+p0+k4EDcgXoIoL03Y5b+V9vWv96UTTO+Px+PRv75Gq9frybIGYJVujplHs8tO3oEtxF+q/J8UyDX70PhePwAAAABJRU5ErkJggg==" /></a>';
				$downloadLink = '<a href="' . $downloadUrl . '" title="' . $value['originalname'] . '">' . substr( $value['originalname'], 0, 40 ) . '.' . $value['extension'] . '</a>';


				$element = '<div style="width: 100%; float: left;">
						<div style="float: left; text-align: right;">' . $downloadLink . '</div>
					</div>';
			}
		}

		return $element;
	}

	/**
	 * detect the value of this field in the postObject
	 */
	public function detectPostValue() {
		if ( isset( $_FILES[ $this->getName() ] ) && isset( $_FILES[ $this->getName() ]['tmp_name'] ) && ! empty( $_FILES[ $this->getName() ]['tmp_name'] ) ) {
			$postValue = $_FILES[ $this->getName() ];

			$this->setPostValue( $postValue );
		}
	}

	public function setPostValue( $fileRef ) {
		$this->postValue = array(
			'name'    => $fileRef['name'],
			'content' => base64_encode( file_get_contents( $fileRef['tmp_name'] ) ),
			'type'    => $fileRef['type']
		);

		$this->setValue( $this->postValue );
	}

	public function getPostValue() {
		return empty( $this->postValue ) ? false : $this->postValue;
	}

	/**
	 * the setValue method is overwritten because the
	 * parent setValue will convert everything to a string
	 */
	public function setValue( $value ) {
		$this->value = $value;
	}
}