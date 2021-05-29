<?php
class CardinfoForm extends Zend_Form
{
	public function __construct($params = null)
	{
		/************ cardnumber  *****************/
		$cardtype = $this->createElement('select','cardtype')
						->setRequired(true)
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
		$cardtype->addMultiOption('','Select Card Type');
		$cardtype->addMultiOption('Visa','Visa');
		$cardtype->addMultiOption('MasterCard','Master Card');
		$cardtype->addMultiOption('AmericanExpress','American Express');
		$cardtype->addMultiOption('DinersClub','Diners Club');
        $cardtype->addErrorMessage('Please select card type.');
		/************ cardnumber  *****************/
		$cardnumber = $this->createElement('text','cardnumber')
						->setRequired(true)
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
    
		/************ cvv *****************/
		$cvv = $this->createElement('text','cvv')
						->setRequired(true)
						->addFilters(array('StringTrim', 'StripTags'))
						->addDecorators(array(
						'ViewHelper',
						'Errors',
						array('HtmlTag', array('tag' => 'div')),
						array('Label', array('tag' => '')),
						));
		/************ expirydate *****************/				
		$expirydate = $this->createElement('text','expirydate')
					->setRequired(true)
					->addDecorators(array(
					'ViewHelper',
					'Errors',
					array('HtmlTag', array('tag' => 'div')),
					array('Label', array('tag' => '')),
					));
		
		
		$this->addElements(array(
		$cardtype,
		$cardnumber,
		$cvv,
		$expirydate
		)); 
	}
}