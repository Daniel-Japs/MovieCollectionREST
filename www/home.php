<?php
session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Rating aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $series_id = $_POST['series_id'];
    $rating = $_POST['rating'];

    $update_rating_url = "http://localhost:5000/api/series/$series_id/rating";
    $data = array(
        'user_id' => $user_id,
        'rating' => $rating
    );

    $options = array(
        'http' => array(
            'method'  => 'PUT',
            'content' => http_build_query($data),
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($update_rating_url, false, $context);
}

// Serien von der API abrufen
$api_url = "http://localhost:5000/api/series?user_id=$user_id";

// Suchparameter hinzufügen, wenn vorhanden
if (isset($_GET['title'])) {
    $title = urlencode($_GET['title']);
    $api_url .= "&title=$title";
}
if (isset($_GET['genre'])) {
    $genre = urlencode($_GET['genre']);
    $api_url .= "&genre=$genre";
}
if (isset($_GET['platform'])) {
    $platform = urlencode($_GET['platform']);
    $api_url .= "&platform=$platform";
}

$json_data = file_get_contents($api_url);
$series_data = json_decode($json_data, true);

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
    <title>Series Collection</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="home.css">
</head>
<body>

<h2>Series Collection</h2>

<form action="home.php" method="get">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="text" name="title" placeholder="Search by title" value="<?php echo isset($_GET['title']) ? htmlspecialchars($_GET['title']) : ''; ?>">
    <select name="genre">
        <option value="">All Genres</option>
        <?php foreach ($genres as $genre): ?>
            <option value="<?php echo $genre; ?>" <?php if (isset($_GET['genre']) && $_GET['genre'] == $genre) echo 'selected'; ?>><?php echo $genre; ?></option>
        <?php endforeach; ?>
    </select>
    <select name="platform">
        <option value="">All Platforms</option>
        <?php foreach ($platforms as $platform): ?>
            <option value="<?php echo $platform; ?>" <?php if (isset($_GET['platform']) && $_GET['platform'] == $platform) echo 'selected'; ?>><?php echo $platform; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Search">
</form>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Seasons</th>
            <th>Genre</th>
            <th>Platform</th>
            <th>Rating</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($series_data)): ?>
        <?php foreach ($series_data as $serie): ?>
        <tr>
            <td><?php echo $serie['title']; ?></td>
            <td><?php echo $serie['seasons']; ?></td>
            <td><?php echo $serie['genre']; ?></td>
            <td><?php echo $serie['platform']; ?></td>
            <td>
                <form action="home.php" method="post">
                    <input type="hidden" name="series_id" value="<?php echo $serie['series_id']; ?>">
                    <select name="rating">
                        <option value="0" <?php if ($serie['rating'] == 0) echo 'selected'; ?>>No rating</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if ($serie['rating'] == $i) echo 'selected'; ?>><?php echo $i; ?> stars</option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit">Save</button>
                </form>
            </td>
            <td>
                <a href="series_form.php?action=delete&id=<?php echo $serie['series_id']; ?>&user_id=<?php echo $user_id; ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align: center;">No entries found</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<div class="links-container">
    <a href="series_form.php?action=add&user_id=<?php echo $user_id; ?>">Add New Series</a>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>