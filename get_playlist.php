<?php
require_once "functions.php";

if (isset($_GET["playlist_id"])) {
    $playlist_id = $_GET["playlist_id"];
} else {
    echo "Playlist ID is missing.";
    exit();
}

try {
    $db = openDb();
    $statement = $db->prepare("
        SELECT tracks.Name, tracks.Composer
        FROM tracks
        INNER JOIN playlist_track ON tracks.TrackId = playlist_track.TrackId
        INNER JOIN playlists ON playlist_track.PlaylistId = playlists.PlaylistId
        WHERE playlists.PlaylistId = ?
    ");
    $statement->execute([$playlist_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo "<b>name:</b> \"{$row['Name']}\" <b>composer:</b> \"{$row['Composer']}\"<br>";
        }

    } else {
        echo "No tracks found for playlist ID $playlist_id.";
    }

} catch (PDOException $e) {
    echo "Error retrieving tracks: " . $e->getMessage();
}