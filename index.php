<?php
session_start(); 

$host = "localhost";
$dbname = "yemek_tarifi";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Kullanıcı giriş yapmış mı kontrol et
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Kullanıcı doğrulama durumu kontrolü
    $sql = "SELECT is_verified FROM kullanicilar WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Eğer kullanıcı doğrulanmamışsa, verify.php'ye yönlendir
        if ($row['is_verified'] == 0) {
            header("Location: verify.php");  // Kullanıcıyı verify.php'ye yönlendir
            exit();
        }
    }
}

$sql = "SELECT * FROM tarifler";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hedefim.com</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>

    <header>
        <nav>
            <?php if (isset($_SESSION['kullanici_adi']) && $_SESSION['role'] === 'user'): ?>
            <a href="logout.php">Çıkış Yap</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'mod'): ?>
            <a href="new_recipe.php">Mektuo Ekle</a>
            <a href="recipe_list.php">Mektup Listesi</a>
            <a href="logout.php">Çıkış yap</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="new_recipe.php">Mektup Ekle</a>
            <a href="recipe_list.php">Mektup Listesi</a>
            <a href="user_list.php">Kullanıcı Listesi</a>
            <a href="logout.php">Çıkış yap</a>
            <?php else: ?>
            <a href="register.php">Kayıt Ol</a>
            <a href="login.php">Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['kullanici_adi']) && $_SESSION['role'] === 'user'): ?>
        <h1>Hoş Geldin
            <?php echo isset($_SESSION['kullanici_adi']) ? htmlspecialchars($_SESSION['kullanici_adi']) : 'Hoş Geldin!'; ?>
        </h1>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'mod'): ?>
        <h1>Hoş Geldin
            <?php echo isset($_SESSION['kullanici_adi']) ? htmlspecialchars($_SESSION['kullanici_adi']) : 'Hoş Geldiniz!'; ?>
            (Moderatör)</h1>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <h1>Hoş Geldin
            <?php echo isset($_SESSION['kullanici_adi']) ? htmlspecialchars($_SESSION['kullanici_adi']) : 'Hoş Geldiniz!'; ?>
            (Sahip)</h1>
        <?php else: ?>
        <h1> Şu Anda Misafir Hesapla Giriş Yapmaktasınız! Mektupları Görüntülemek İçin Lütfen Giriş Yapın! </h1>
        <?php endif; ?>
        <h2>Mektuplar</h2>

        <?php if ($result->num_rows > 0): ?>
        <div class="recipe-list">
            <?php while ($recipe = $result->fetch_assoc()): ?>
            <div class="recipe-card">
                <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>"
                    alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-image">
                <p><strong>Açıklama:</strong> <?php echo htmlspecialchars(substr($recipe['description'], 0, 100)); ?>...
                </p>
                <a href="recipe.php?id=<?php echo $recipe['id']; ?>">Devamını Oku</a>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <p>Henüz mektup bulunmamaktadır.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy;2024 <a href="mngultekn.com" style="color: white
        " target="_blank"> Hedefim</a> Her hakkı saklıdır.
        </p>
    </footer>

</body>

</html>