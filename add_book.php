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

echo "slika ni bila naložena, test uspešen";
// exit;

 try {
    if (!isset($_FILES['cover'])) {
        die("❌ Datoteka ni bila poslana.");
      }
      
      if ($_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
        die("❌ Napaka pri nalaganju datoteke: " . $_FILES['cover']['error']);
      }
      
      if (!is_uploaded_file($_FILES['cover']['tmp_name'])) {
        die("❌ Ni veljavna naložena datoteka.");
      }     


   $s3->putObject([
     'Bucket' => $bucket,
     'Key'    => "images/$isbn.png",
     'SourceFile' => $_FILES['cover']['tmp_name'],
     'ContentType' => 'image/png',
     'ACL'    => 'public-read' // če želiš, da je javno dostopna
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
  
  $options = [
    'http' => [
      'header'  => "Content-Type: application/json",
      'method'  => 'POST',
      'content' => json_encode($data)
    ]
  ];
  $context  = stream_context_create($options);
  $result = file_get_contents('http://localhost/api/add.php', false, $context);
  
  if ($result === FALSE) {
    echo "Napaka pri klicu API-ja.";
  } else {
    echo "Knjiga uspešno dodana! <a href='/'>Nazaj</a>";
  }
