
<?php
$host     = "127.0.0.1";     // ← changez localhost par 127.0.0.1
$port     = "3306";           // ← essayez aussi 3308 si ça marche pas
$user     = "root";
$password = "";
$database = "ponts_peage";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie !";
} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>