<?php
class Application_Form_NationalHeadForgot extends Zend_Form
{
	public function __construct($params = null)
	{
        
		/************ Email  *****************/
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
	
		$this->addElements(array(
		$email
		)); 
	}
}
