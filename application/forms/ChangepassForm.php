<?php
class ChangepassForm extends Zend_Form
{
	public function __construct($params = null)
	{
						
		$oldpass = $this->createElement('password','oldpass')
						->setRequired(true)
						->setErrorMessages(array('Please enter current password'))
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
		
		
		$newpass = $this->createElement('password','newpass')
						->setRequired(true)
						->setErrorMessages(array('Please enter new password'))
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
						
		$renewpass = $this->createElement('password','renewpass')
					->setRequired(true)
					->setErrorMessages(array('Please enter confirm password'))
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
		$oldpass,
		$newpass,
		$renewpass,
		$changepass
		)); 
	}
}