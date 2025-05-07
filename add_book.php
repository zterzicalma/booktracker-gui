<?php
require '../api/config.php'; // uporablja isto DB povezavo

require '/var/www/booktracker/vendor/autoload.php'; // AWS SDK

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Nastavitve S3
$bucket = 'book-tracker-images-zigat';
$region = 'eu-central-1';
$key = 'YOUR_AWS_ACCESS_KEY';
$secret = 'YOUR_AWS_SECRET_KEY';

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
    'ACL'    => 'public-read' // če želiš, da je javno dostopna
  ]);
} catch (AwsException $e) {
  die('Napaka pri nalaganju slike v S3: ' . $e->getMessage());
}

// 3. Vpiši knjigo v bazo
try {
  $stmt = $pdo->prepare("INSERT INTO books (isbn, title, author, year_published) VALUES (?, ?, ?, ?)");
  $stmt->execute([$isbn, $title, $author, $year]);
  echo "Knjiga uspešno dodana! <a href='/'>Nazaj</a>";
} catch (PDOException $e) {
  echo "Napaka pri shranjevanju knjige: " . $e->getMessage();
}
