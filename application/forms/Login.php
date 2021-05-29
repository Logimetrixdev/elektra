<?php
class Application_Form_Login extends Zend_Form
{
	public function __construct($params = null)
	{
        
		/************ username  *****************/
        		$username = $this->createElement('text','username',
						array('value' => trim($params['username']),
							 'class' => 'span12',
								'id' => 'username'
										
							 ))
						->setValidators(array(array("Alpha", true, array("allowWhiteSpace" => false))))	 
						->setRequired(true)
						->setErrorMessages(array('Please fill username and white space not allowed'))
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
					->setValidators(array(array("Alnum", true, array("allowWhiteSpace" => false))))	
					->setErrorMessages(array('Please fill password and white space not allowed'))
					->addDecorators(array(
					'ViewHelper',
					'Errors',
					array('HtmlTag', array('tag' => 'div')),
					array('Label', array('tag' => '')),
					));
							//$password->addErrorMessage('Password can not be empty');		
		$this->addElements(array(
		$username,
		$password
		)); 
	}
}
