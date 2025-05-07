<?php
require '/var/www/booktracker/vendor/autoload.php';
use Aws\S3\S3Client;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// testna lokalna slika
$tmpFile = '/tmp/test-cover.png';
file_put_contents($tmpFile, file_get_contents('https://via.placeholder.com/150x220.png'));

$env = parse_ini_file(__DIR__ . '/.env');

$region = $env['AWS_REGION'];
$bucket = $env['S3_BUCKET'];
$key    = $env['AWS_ACCESS_KEY'];
$secret = $env['AWS_SECRET_KEY'];

$s3 = new S3Client([
  'version' => 'latest',
  'region'  => $region,
  'credentials' => [
    'key'    => $key,
    'secret' => $secret
  ]
]);

try {
  $s3->putObject([
    'Bucket' => $bucket,
    'Key'    => 'images/test-file.png',
    'SourceFile' => $tmpFile,
    'ContentType' => 'image/png',
    'ACL'    => 'public-read'
  ]);
  echo "✅ Testna slika uspešno naložena v S3!";
} catch (Exception $e) {
  echo "❌ Napaka: " . $e->getMessage();
}
