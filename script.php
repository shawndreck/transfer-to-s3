<?php

require "./vendor/autoload.php";

use Aws\S3\S3Client;
use Aws\S3\Transfer;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$credentials = new Aws\Credentials\Credentials(
    getenv('AWS_ACCESS_KEY_ID'),
    getenv('AWS_SECRET_ACCESS_KEY')
);

$client = new S3Client([
    'version'     => 'latest',
    'region'      => 'ap-southeast-1',
    'credentials' => $credentials,
]);

// Where the files will be source from
$source = getenv('LOCAL_SOURCE_PATH');// '/path/to/source/files';

// Where the files will be transferred to
$dest = 's3://' . getenv('AWS_BUCKET') ;

// Create a transfer object
$manager = new Transfer($client, $source, $dest);

// Perform the transfer synchronously
$manager->transfer();