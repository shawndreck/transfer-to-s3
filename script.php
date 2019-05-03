<?php

require "./vendor/autoload.php";

use Aws\S3\S3Client;

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

function get_content_files($path)
{
    $path = realpath($path);
    $files = array_diff(scandir($path), ['.', '..', '.DS_Store', '.gitignore', '.gitkeep', '.htaccess']);
    $filtered = [];
    foreach ($files as $file) {
        if (strstr($file, '.zip')) {
            continue;
        }
        $filtered[] = $path . DIRECTORY_SEPARATOR . $file;
    }
    return $filtered;
}

function get_relative_path($file)
{
    $marker = '/upload/';
    return $relative_path = substr($file, strpos($file, $marker));
}

function is_file_exists_on_s3($file)
{
    global $client;
    static $register = null;
    if ($register === null) {
        $client->registerStreamWrapper();
        $register = true;
    }
    $relative_path = get_relative_path($file);
    return file_exists('s3://' . getenv('AWS_BUCKET') . "/$relative_path");
}

function transfer_to_s3($files)
{
    $files = is_array($files) ? $files : [$files];
    foreach ($files as $file) {
        if (is_dir($file)) {
            echo "$file is a directory\n";
            $contents = get_content_files($file);
            return transfer_to_s3($contents);
        }

        if (is_file_exists_on_s3($file)) {
            continue;
        }

        $key = get_relative_path($file);
        global $client;
        $client->putObject([
            'ACL'    => 'bucket-owner-full-control',
            'Body'   => file_get_contents($file),
            'Bucket' => getenv('AWS_BUCKET'),
            'Key'    => $key,
        ]);
        echo "{$file} ==> s3://" . getenv('AWS_BUCKET') . "/{$key}" . PHP_EOL;
    }
    return true;
}

$base_path = "$source/upload";
$files = get_content_files($base_path);
transfer_to_s3($files);


// Where the files will be transferred to
//$dest = 's3://' . getenv('AWS_BUCKET') . '/upload';

// Create a transfer object
//$manager = new Transfer($client, $source, $dest);

// Perform the transfer synchronously
//$manager->transfer();