<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=mactologywebapp;AccountKey=v4oZVbmnbqmLmBgiDnZGAI5jmdybXQninkBFWBPH44U+Y5xwoWzgRSLRYrkG6g/GnMqaxGeNpjPj/UupPN+iDw==";
$containerName = "blockblobshsgmut";



$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");

	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 

    <title>Blob Uploader</title>

  </head>
<body>
    		 <br>
        		<h1 align="center">Blob Uploader</h1><br>
				<p class="lead">Please choose a image. and then click button <b>Upload</b>
				<span class="border-top my-3"></span>
			
		<div>
			<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
			
		</div>
		<br>
		<br>
		<h4>List Files :</h4>
		<table align="center">
			<thead>
				<tr>
					<th>File Name</th>
					<th>File URL</th>
					<th>Action</th>
				</tr>
			</thead>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="ImageAnalyze.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Analyze" >
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
		</table>

	</div>

  </body>
</html>