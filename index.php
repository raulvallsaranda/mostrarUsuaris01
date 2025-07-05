<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
    $error="";
    if(isset($_GET["error"])){
        $error = $_GET["error"];
    }

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

// DSN amb charset utf8mb4
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <title>Projecte Final Azure Mostrar Usuaris</title>
    <link rel="stylesheet" href="css/estils.css">
</head>

<body>
    <div id="wrapper">
        <header>
            <h1>Projecte Final Azure Mostrar Usuaris</h1>
            <h2>Webapp01</h2>
        </header>
        <main>
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

                    echo "<div id=\"contUsuaris\">";
                    $sql = "SELECT * FROM usuari";
                    $stmt = $pdo->query($sql);
                    $i=0;
                    while ($fila = $stmt->fetch()) {
                        $i++;
                        echo '<div class="cardUsuari">';
                        echo '<p><strong>ID: </strong>' . $fila['id'] . '</p>';
                        echo '<p>' . $fila['nom'] . '</p>';
                        echo '<p>' . $fila['cognoms'] . '</p>';
                        echo '<p>' . $fila['email'] . '</p>';
                        echo '<p>' . $fila['data'] . '</p>';
                        echo '<p><a href="https://cefirestorage02raul.blob.core.windows.net/documentspdf/' . $fila['document'] . '">'. $fila['document'] .'</a></p>';
                        echo '</div>';
                    }
                    if($i==0){
                         echo '<p>No hi ha usuaris per a mostrar.</p>';
                    }
                    echo "</div>";
                } catch (PDOException $e) {
                    error_log('Error de connexió PDO: ' . $e->getMessage());
                    echo "Error connectant amb la base de dades: " . htmlspecialchars($e->getMessage());
                    exit;
                }
                ?>
        </main>
        <footer>
            <p>Curs Azure Cefire 2025</p>
        </footer>
    </div>
</body>

</html>