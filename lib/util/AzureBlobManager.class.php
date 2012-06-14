<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lmaxim
 * Date: 6/13/12
 * Time: 4:20 PM
 * To change this template use File | Settings | File Templates.
 */

require_once '/home/lmaxim/clipclock/plugins/sfAzureSDKPlugin/lib/vendor/azure-sdk-for-php/WindowsAzure/WindowsAzure.php';

# AZURE configuration
use WindowsAzure\Common\Configuration;

# AZURE container
use WindowsAzure\Blob\Models\CreateContainerOptions;
use WindowsAzure\Blob\Models\PublicAccessType;
use WindowsAzure\Common\ServiceException;

# AZURE blob
use WindowsAzure\Blob\BlobSettings;
use WindowsAzure\Blob\BlobService;
use WindowsAzure\Blob\Models\CreateBlobOptions;


class AzureBlobManager {

	// http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
	const CONTAINER_ALREADY_EXISTS_CODE = 409;
	const CONFLICT_CODE = 409;

	protected
		$_blobRestProxy = null;

	public function __construct($account_name, $account_key, $uri)
	{

		//$azure_loader = new WindowsAzureLoader();
		$config = new Configuration();
		$config->setProperty(BlobSettings::ACCOUNT_NAME, $account_name);
		$config->setProperty(BlobSettings::ACCOUNT_KEY, $account_key);
		$config->setProperty(BlobSettings::URI, $uri);

		$this->_blobRestProxy = BlobService::create($config);

	}

	public function createContainerNX($container_name)
	{
		/**
		 * @var   $blobRestProxy BlobRestProxy
		 */

		try {
			$this->_blobRestProxy->createContainer($container_name);
		}
		catch(ServiceException $e){

			if (self::CONTAINER_ALREADY_EXISTS_CODE != $e->getCode())
				throw $e;
		}
	}

	public function putFile($container_name, $blob_name, $file_path)
	{
		# About naming and other
		# http://msdn.microsoft.com/en-us/library/windowsazure/dd135715.aspx
		$this->createContainerNX($container_name);

		try {
			$content = fopen($file_path, "r"); //file_get_contents($file_path); //<--avaible

			$mime_typer = new FileMimeType();

			$options = new CreateBlobOptions();
			$options->setContentType($mime_typer->getMimeType($file_path, 'image/png'));
			/**
			 * Если setBlobContentType не задан, то будет использоваться setContentType , причем azure в конце не
			 * определяет тот ли это mime-type
			 */

			$this->_blobRestProxy->createBlockBlob($container_name, $blob_name, $content, $options);
		}
		catch(ServiceException $e){
			/**
			 * BlobAlreadyExists

				Conflict (409)

				The specified blob already exists.
			 *
			 * But NEVER throws!
			 */

			throw $e;
		}

		return true;
	}

	public function getBlobListFromContainer($container_name)
	{
		try {
			// List blobs.
			$blob_list = $this->_blobRestProxy->listBlobs($container_name);
			$blobs = $blob_list->getBlobs();

			foreach($blobs as $blob)
			{
				echo $blob->getName().": ".$blob->getUrl()."<br />";
			}
		}
		catch(ServiceException $e){
			// must we catch any exception about continer unabilty ?
			throw $e;
		}
	}
}