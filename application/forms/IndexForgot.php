<?php
class Application_Form_IndexForgot extends Zend_Form
{
	public function __construct($params = null)
	{
		/************ phone  *****************/
        		$phone = $this->createElement('text','phone',
						array('value' => $params['phone'],
							 'class' => 'span12',
								'id' => 'phone'			
							 ))

						->setRequired(true)
						->setFilters(array('StringTrim'))
						->setValidators(array('Digits'))
						->setErrorMessages(array('Please fill valid mobile number'))
						->addDecorators(
								array(
								'ViewHelper',
								'Errors',
								array('HtmlTag', array('tag' => 'div')),
								array('Label', array('tag' => '')),
							));
	
		$this->addElements(array(
		$phone
		)); 
	}
}
