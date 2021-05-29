<?php
class Application_Form_IndexLogin extends Zend_Form
{
	public function __construct($params = null)
	{
		/************ username  *****************/
        		$loginid = $this->createElement('text','loginid',
						array('value' => $params['loginid'],
						     'autocomplete' => 'off',
							 'class' => 'span12',
								'id' => 'loginid'			
							 ))
                        ->setValidators(array(array("Alnum", true, array("allowWhiteSpace" => false))))	 
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
					//->setValidators(array(array("Alnum", true, array("allowWhiteSpace" => false))))	
					->setErrorMessages(array('Please fill password'))
					->addDecorators(array(
					'ViewHelper',
					'Errors',
					array('HtmlTag', array('tag' => 'div')),
					array('Label', array('tag' => '')),
					));
							//$password->addErrorMessage('Password can not be empty');		
		$this->addElements(array(
		$loginid,
		$password
		)); 
	}
}