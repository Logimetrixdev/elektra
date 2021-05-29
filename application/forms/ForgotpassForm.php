<?php
class ForgotpassForm extends Zend_Form
{
	public function __construct($params = null)
	{
		
		$newpass = $this->createElement('password','newpass')
						->setRequired(true)
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
						
		$renewpass = $this->createElement('password','renewpass')
					->setRequired(true)
					->addDecorators(array(
					'ViewHelper',
					'Errors',
					array('HtmlTag', array('tag' => 'div')),
					array('Label', array('tag' => '')),
					));
					
		
				 
		$changepass = $this->createElement('submit','changepass');
		$changepass->setLabel('Change password')
				->setIgnore(true);
		
		$this->addElements(array(
		$newpass,
		$renewpass,
		$changepass
		)); 
	}
}