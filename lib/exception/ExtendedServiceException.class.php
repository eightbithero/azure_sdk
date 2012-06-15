<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lmaxim
 * Date: 6/15/12
 * Time: 5:11 PM
 * To change this template use File | Settings | File Templates.
 */

use WindowsAzure\Common\ServiceException;

class ExtendedServiceException extends ServiceException {

	protected $_error_code = '';

	public function __construct(ServiceException $e)
    {
		$matches = array();

		preg_match("|(?<=<Code>)(\w)+?(?=</Code>)|", $e->getErrorReason(), $matches);

		if (!empty($matches))
		{
			$this->_error_code = $matches[0];
		}

		parent::__construct($e->getCode(), $e->getErrorText(), $e->getErrorReason());
    }

	public function getErrorCode()
    {
        return $this->_error_code;
    }
}