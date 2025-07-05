<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
    $error="";
    if(isset($_GET["error"])){
        $error = $_GET["error"];
    }
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="utf-8">
    <title>Projecte Final Azure Crear Usuari</title>
    <link rel="stylesheet" href="css/estils.css">
</head>

<body>
    <div id="wrapper">
        <header>
            <h1>Projecte Final Azure Crear Usuari</h1>
            <h2>Webapp02</h2>
        </header>
        <main>
            <?php
            if(strcmp($error,"")!=0){
                echo '<h4>Error: '.$error.'</h4>';
            }
            ?>
            <form id="formUsuari" name="formUsuari" method="POST" action="processaCreacio.php" enctype="multipart/form-data" action="./usuaris.php">
                <div class="filaform">
                    <label for="nom" class="obligatori">Nom:</label>
                    <input type="text" id="nom" name="nom" placeholder="nom de l'usuari" required>
                </div>
                <div  class="filaform">
                    <label for="cognoms" class="obligatori">Cognoms:</label>
                    <input type="text" id="cognoms" name="cognoms" placeholder="cognoms de l'usuari" required>
                </div>
                <div  class="filaform">
                    <label for="email" class="obligatori">Email:</label>
                    <input type="email" id="email" name="email" placeholder="email de l'usuari">
                </div>
                <div class="filaform">
                    <label for="cicle" class="obligatori">Cicle:</label>
                    <select name="cicle">
                        <option value="SMX">SMX</option>
                        <option value="ASIX">ASIX</option>
                        <option value="DAM">DAM</option>
                        <option value="DAW">DAW</option>
                    </select>
                </div>
                <div class="filaform">
                    <label for="document" class="obligatori">Document PDF:</label>
                    <input type="file" name="document" id="document" accept="application/pdf" required>
                </div>                
                <div class="filaform">
                    <button type="submit">Enviar</button>
                </div>
                
            </form>
        </main>
        <footer>
            <p>Curs Azure Cefire 2025</p>
        </footer>
    </div>
</body>

</html>