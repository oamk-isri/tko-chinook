<?php
require_once "functions.php";

if (isset($_GET["artist_id"])) {
    $artist_id = $_GET["artist_id"];
} else {
    echo "Artist ID is missing.";
    exit();
}

try {
    $db = openDb();
    $statement = $db->prepare("
        SELECT * FROM artists
        WHERE ArtistId = ?
    ");
    $statement->execute([$artist_id]);
    $artist = $statement->fetch(PDO::FETCH_ASSOC);

    // https://www.mysqltutorial.org/mysql-group_concat/
    $statement = $db->prepare("
        SELECT albums.Title AS album_title, GROUP_CONCAT(tracks.Name SEPARATOR ',') AS track_names
        FROM albums
        INNER JOIN tracks ON albums.AlbumId = tracks.AlbumId
        WHERE albums.ArtistId = ?
        GROUP BY albums.Title
        ORDER BY albums.Title
    ");
    $statement->execute([$artist_id]);
    $albums = [];
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $album_title = $row['album_title'];
        $track_names = $row['track_names'];
        $albums[] = ['title' => $album_title, 'tracks' => explode(',', $track_names)];
    }

    $response = ['artist' => $artist['Name'], 'albums' => $albums];
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    echo "Error retrieving artist information: " . $e->getMessage();
}