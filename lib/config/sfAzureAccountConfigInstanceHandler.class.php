<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lmaxim
 * Date: 6/9/12
 * Time: 5:29 PM
 * To change this template use File | Settings | File Templates.
 */

class sfAzureAccountConfigInstanceHandler extends sfYamlConfigHandler
{

	public function __construct($parameters = null)
	{
		//die('sfsdfsd');

		parent::__construct($parameters);
	}

	public function execute($configFiles)
	{
		var_dump($configFiles);
	}
}
