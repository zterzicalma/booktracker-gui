<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

require '../api/config.php'; // uporablja isto DB povezavo

require '/var/www/booktracker/vendor/autoload.php'; // AWS SDK

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Nastavitve S3
$env = parse_ini_file(__DIR__ . '/.env');
$region = $env['AWS_REGION'];
$bucket = $env['S3_BUCKET'];
$key    = $env['AWS_ACCESS_KEY'];
$secret = $env['AWS_SECRET_KEY'];

//var_dump($env);
//exit;

// 1. Preveri, da so podatki prisotni
if (!isset($_POST['isbn'], $_POST['title'], $_POST['author'], $_FILES['cover'])) {
  die('Manjkajoči podatki.');
}

$isbn = $_POST['isbn'];
$title = $_POST['title'];
$author = $_POST['author'];
$year = $_POST['year'] ?? null;

// 2. Naloži sliko na S3
if ($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
  die('Napaka pri nalaganju slike.');
}

// Inicializiraj S3
$s3 = new S3Client([
  'version' => 'latest',
  'region'  => $region,
  'credentials' => [
    'key' => $key,
    'secret' => $secret
  ]
]);

 try {
   $s3->putObject([
     'Bucket' => $bucket,
     'Key'    => "images/$isbn.png",
     'SourceFile' => $_FILES['cover']['tmp_name'],
     'ContentType' => 'image/png',
   ]);
 } catch (AwsException $e) {
   die('Napaka pri nalaganju slike v S3: ' . $e->getMessage());
 }

$data = [
    'isbn' => $isbn,
    'title' => $title,
    'author' => $author,
    'year_published' => $year
  ];
  
  $ch = curl_init('http://3.68.98.183/api/add.php');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $result = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if ($httpCode !== 200) {
    echo "Napaka pri klicu API-ja: HTTP $httpCode";
  } else {
    header('Location: /');
    exit;
  }
  
