<?php
session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $seasons = $_POST['seasons'];
    $genre = $_POST['genre'];
    $platform = $_POST['platform'];

    $add_url = "http://localhost:5000/api/series";
    $data = array(
        'user_id' => $user_id,
        'title' => $title,
        'seasons' => $seasons,
        'genre' => $genre,
        'platform' => $platform
    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n"
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($add_url, false, $context);

    if ($result !== FALSE) {
        header("Location: home.php");
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $series_id = $_GET['id'];

    $delete_url = "http://localhost:5000/api/series/$series_id?user_id=$user_id";
    $options = array(
        'http' => array(
            'method'  => 'DELETE'
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($delete_url, false, $context);

    if ($result !== FALSE) {
        header("Location: home.php");
        exit();
    }
}

// Plattformen von der API abrufen 
$platforms_api_url = "http://localhost:5000/api/platforms?user_id=$user_id";
$platforms_json = file_get_contents($platforms_api_url);
$platforms = json_decode($platforms_json, true);

// Genres von der API abrufen
$genres_api_url = "http://localhost:5000/api/genres?user_id=$user_id"; 
$genres_json = file_get_contents($genres_api_url);
$genres = json_decode($genres_json, true);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Add/Delete Series</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="series_form.css">
</head>
<body>

<?php if (!isset($_GET['action']) || $_GET['action'] == 'add'): ?>
    <h2>Add New Series</h2>
    <form action="series_form.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="seasons">Seasons:</label>
            <input type="number" id="seasons" name="seasons" min="1" required>
        </div>
        <div>
            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" list="genreList" required>
            <datalist id="genreList">
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo $genre; ?>">
                <?php endforeach; ?>
            </datalist>
        </div>
        <div>
            <label for="platform">Platform:</label>
            <input type="text" id="platform" name="platform" list="platformList" required>
            <datalist id="platformList">
                <?php foreach ($platforms as $platform): ?>
                    <option value="<?php echo $platform; ?>">
                <?php endforeach; ?>
            </datalist>
        </div>
        <div>
            <input type="submit" value="Add Series">
        </div>
    </form>
<?php endif; ?>

<a href="home.php">Back to Home</a>

</body>
</html>