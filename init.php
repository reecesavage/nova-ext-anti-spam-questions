<?php 
$this->require_extension('jquery');


require_once dirname(__FILE__).'/events/location_main_contact.php';
require_once dirname(__FILE__).'/events/location_main_join.php';

require_once dirname(__FILE__) . '/controllers/Installer.php';
$manager = ( new \nova_ext_honeypot\Installer() )->install();
