<?php
class Application_Form_CircleHeadLogin extends Zend_Form
{
	public function __construct($params = null)
	{
        
		/************ username  *****************/
        		$email = $this->createElement('text','email',
						array('value' => $params['email'],
							 'class' => 'span12',
								'id' => 'email'
										
							 ))

						->setRequired(true)
						->addDecorators(
								array(
								'ViewHelper',
								'Errors',
								array('HtmlTag', array('tag' => 'div')),
								array('Label', array('tag' => '')),
							));

		/************ Password *****************/				
		$password = $this->createElement('password','password',
						array('autocomplete' => 'off',
							 'class' => 'span12',
								'id' => 'password'
								
						))
					->setRequired(true)
					->addDecorators(array(
					'ViewHelper',
					'Errors',
					array('HtmlTag', array('tag' => 'div')),
					array('Label', array('tag' => '')),
					));
							//$password->addErrorMessage('Password can not be empty');		
		$this->addElements(array(
		$email,
		$password
		)); 
	}
}
