<?php

namespace helena\entities\admin;

use helena\entities\BaseMapModelLabeled;

class Institution extends BaseMapModelLabeled
{
	public $Id;
	public $Name;
	public $Web;
	public $Email;
	public $Address;
	public $Phone;
	public $Country;

	public static function GetMapLabeled()
	{
		return array (
			array('ins_id', 'Id'),
			array('ins_caption', 'Name', 'Nombre'),
			array('ins_web', 'Web', 'Web'),
			array('ins_email', 'Email', 'Correo electr�nico'),
			array('ins_address', 'Address', 'Direcci�n postal'),
			array('ins_phone', 'Phone', 'Tel�fono'),
			array('ins_country', 'Country', 'Pa�s'));
	}
}