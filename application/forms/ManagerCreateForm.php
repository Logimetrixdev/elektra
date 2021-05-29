<?php

class ManagerCreateForm extends Zend_Form

{

	public function __construct($params = null)

	{

		

		/************ Trial  version name *****************/

		
		$trial = $this->createElement('submit','trial');

		$trial->setLabel('Start Trial')

				->setIgnore(true);
		/************ Bisiness name *****************/

		$biz_name = $this->createElement('text','biz_name')

						->setRequired(true)

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

		$biz_name->addErrorMessage('Business name can\'t be empty');

		/************ Bisiness type *****************/

		$biztype = array();

		$biztype = businesstype::getbiztype();				

		$biz_type = $this->createElement('select','biz_type')

						->setRequired(true)

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

		$biz_type->addMultiOption('','Select Business Type');

		foreach($biztype as $val)

		{

			$biz_type->addMultiOption($val->id,$val->name);

		}

		$biz_type->setValue($selected);

		$biz_type->addErrorMessage('Business type can\'t be empty');

		/************ Email address *****************/

		$email = $this->createElement('text','email')

						->setRequired(true)

						->addFilters(array('StringTrim', 'StripTags'))

						->addValidator('EmailAddress',  TRUE  )

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

		$email->addErrorMessage('Please enter a valid email address.');

		/************ Phone number *****************/				

		$phone = $this->createElement('text','phone')

					->setRequired(true)

					->addValidator('Digits')

					->addValidator('stringLength', false, array(10, 10))

					->addDecorators(array(

					'ViewHelper',

					'Errors',

					array('HtmlTag', array('tag' => 'div')),

					array('Label', array('tag' => 'Phone no. should have 10 digits.')),

					));
            $phone->getValidator('Digits')->setMessage('Phone no. should have digits only.');
            $phone->getValidator('stringLength')->setMessage('Phone no. should not be greater than or less than 10 digits.');
		// $phone->addErrorMessage('Please enter a valid phone number.');

		/************ Country *****************/			

		$countrylist = array();

		$countrylist = countries::getAllCountry();

		$country = $this->createElement('select','country')

						->setRequired(true)

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

		$country->addMultiOption('','Select Countries');

		foreach($countrylist as $val)

		{

			$country->addMultiOption($val->id,$val->name);

		}

		

		$country->setValue($selected);

		$country->addErrorMessage('Please select country.');

		/************ State *****************/

		$statelist = countries::fetchAllState($params['country']);

		$state = $this->createElement('select','state')

						->setRequired(true)

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

						

		$state->addMultiOption('','Select State');

		foreach($statelist as $val)

		{

			$state->addMultiOption($val->id,$val->name);

		}

		$state->setValue($selected);

		$state->addErrorMessage('Please select state.');

		/************ City *****************/

		$citylist = countries::fetchAllCity($params['state']);

		$city = $this->createElement('select','city')

					->setRequired(true)

					->addDecorators(array(

					'ViewHelper',

					'Errors',

					array('HtmlTag', array('tag' => 'div')),

					array('Label', array('tag' => '')),

					));

					

		$city->addMultiOption('','Select City');

		foreach($citylist as $val)

		{

			$city->addMultiOption($val->id,$val->name);

		}

		$city->setValue($selected);

		$city->addErrorMessage('Please select city.');

		/************ Street *****************/			

		$street = $this->createElement('text','street')

						->setRequired(true)

						->addDecorators(array(

						'ViewHelper',

						'Errors',

						array('HtmlTag', array('tag' => 'div')),

						array('Label', array('tag' => '')),

						));

		$street->addErrorMessage('Please enter street.');

		/************ Terms and conditions *****************/

		

		
		$this->addElements(array(

		$trial,

		$biz_name,

		$biz_type,

		$email,

		$phone,

		$country,

		$state,

		$city,

		$street,

		

		)); 

	}

}
