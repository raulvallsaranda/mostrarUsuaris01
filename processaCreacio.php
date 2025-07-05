<?php

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

// Recuperar variables d'entorn
$dbHost = getenv('DB_HOST');
$dbName = "usuaris";
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD');

if (!$dbHost || !$dbUser || $dbPass === false) {
    //throw new \RuntimeException('Falten variables d\'entorn per a la connexió a la base de dades.');
    header("Location: index.php?error=connexio");
    die();
}

require 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

// Configuració
$connectionString = getenv("AZURE_STORAGE_CONNECTION_STRING");
$containerName = "documentspdf";  // Canvia açò pel nom del teu contenidor

$blobClient = BlobRestProxy::createBlobService($connectionString);

//obtenir camps del formulari
$nom = "";
if (isset($_POST['nom'])) {
    $nom = $_POST['nom'];
}
$cognoms = "";
if (isset($_POST['cognoms'])) {
    $cognoms = $_POST['cognoms'];
}
$email = "";
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
$cicle = "";
if (isset($_POST['cicle'])) {
    $cicle = $_POST['cicle'];
}
$nom = "";
if (isset($_POST['nom'])) {
    $nom = $_POST['nom'];
}
//echo "$nom $cognoms $email cicle";

$extensio = "";
$nomDocument = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["document"])) {

    $uploadedFile = $_FILES["document"];
    //echo $uploadedFile["type"];
    $nomDocument = $uploadedFile["name"];
    $extensio = substr($uploadedFile["name"], strrpos($uploadedFile["name"], "."));
    if ($uploadedFile["type"] !== "application/pdf") {
        header("Location: index.php?error=documenttipus");
        //    echo "<p style='color:red;'>Només es permeten arxius d'imatge.</p>";
        die();
    } else {
        $blobName = basename($uploadedFile["name"]);
        $content = fopen($uploadedFile["tmp_name"], "r");

        try {
            $blobClient->createBlockBlob($containerName, $blobName, $content);
            //echo "<p style='color:green;'>Arxiu $blobName pujat correctament.</p>";
        } catch (ServiceException $e) {
            // echo "<p style='color:red;'>Error en pujar: " . $e->getMessage() . "</p>";
            header("Location: index.php?error=documentpujar");
            die();
        }
    }
}

try {
    $listOptions = new ListBlobsOptions();
    $blobList = $blobClient->listBlobs($containerName, $listOptions);
    $blobs = $blobList->getBlobs();
} catch (ServiceException $e) {
    die("Error en llistar arxius: " . $e->getMessage());
}

// DSN amb charset utf8mb4
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";


?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <title>Creació d'usuaris Projecte Final</title>
    <link rel="stylesheet" href="./css/estils.css">
</head>

<body>
    <div id="wrapper">
        <header>
            <h1>Creació d'usuaris Projecte Final</h1>
            <h2>Webapp02</h2>
        </header>
        <main>
            <div>
                <?php
                try {
                    $options = [
                        // Excepcions en errors
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Fetch com a array associatiu
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Desactivar emulació de prepares
                        PDO::ATTR_EMULATE_PREPARES => false,

                        // Assegurar la connexió TLS cap a Azure Database for MySQL
                        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem',
                        // Desactivem la validació del certificat SSL
                        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                    ];

                    // Crear la connexió PDO
                    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

                    // Exemple: consulta sencilla
                    // $stmt = $pdo->query('SELECT NOW() AS data_actual;');
                    //extensio
                    $sql = "INSERT INTO usuari (`nom`, `cognoms`,`email`,`cicle`,`document`) VALUES ('" . $nom . "','" . $cognoms . "','" . $email . "','" . $cicle . "','" . $nomDocument . "')";
                    //$stmt = $pdo->query('SELECT NOW() AS data_actual;');
                    $stmt = $pdo->query($sql);

                    echo "<div id=\"contUsuaris\">";
                    echo "<p>Usuari creat correctament</p>";
                    echo "</p>";

                    //echo '</div>';
                    //$fila = $stmt->fetch();
                    //echo "Connectat correctament. Hora del servidor: " . $fila['data_actual'];
                    /*echo "<ul>";

                    //llistar imatges
                    /*foreach ($blobs as $blob) {
                        ?>
                        <li>
                            <a href="<?= htmlspecialchars($blob->getUrl()) ?>" target="_blank">
                                <?= htmlspecialchars($blob->getName()) ?>
                            </a>
                        </li>
                        <?php
                    }
                    echo "</ul>";*/
                    /*echo "<div id=\"contUsuaris\">";
                    $sql = "SELECT * FROM usuari";
                    $stmt = $pdo->query($sql);
                    while ($fila = $stmt->fetch()) {
                        echo '<div class="cardUsuari">';
                        echo '<p><strong>ID: </strong>' . $fila['id'] . '</p>';
                        echo '<p>' . $fila['nom'] . '</p>';
                        echo '<p>' . $fila['cognoms'] . '</p>';
                        echo '<p>' . $fila['email'] . '</p>';
                        echo '<p>' . $fila['data'] . '</p>';
                        echo '<p><a href="https://cefirestorage02raul.blob.core.windows.net/imatges/' . $fila['imatge'] . '"><img src="https://cefirestorage02raul.blob.core.windows.net/imatges/' . $fila['imatge'] . '" alt="' . $fila['imatge'] . '" class="imatgeUsuari"></a></p>';
                        echo '</div>';
                    }
                    echo "</div>";*/
                } catch (PDOException $e) {
                    error_log('Error de connexió PDO: ' . $e->getMessage());
                    echo "Error connectant amb la base de dades: " . htmlspecialchars($e->getMessage());
                    exit;
                }
                ?>
            </div>
            <p>
                <a href="../index.php">Tornar a index.php</a>
            </p>
        </main>
        <footer>
            <p>Curs Azure Cefire 2025</p>
        </footer>
    </div>
</body>

</html>