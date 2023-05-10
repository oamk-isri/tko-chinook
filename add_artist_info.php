<?php
require_once "functions.php";

// the input format is the same as get_artist_info.php output
$data = json_decode(file_get_contents("php://input"), true);

$db = openDb();
$db->beginTransaction();

try {
    // artist
    $statement = $db->prepare("
        INSERT INTO artists (Name)
        VALUES (?)
    ");
    $statement->bindValue(1, $data["artist"]);
    $statement->execute();
    $artist_id = $db->lastInsertId();
    
    // album
    foreach ($data["albums"] as $album) {
        $statement = $db->prepare("
            INSERT INTO albums (Title, ArtistId)
            VALUES (?, ?)
        ");
        $statement->bindValue(1, $album["title"]);
        $statement->bindValue(2, $artist_id);
        $statement->execute();
        $album_id = $db->lastInsertId();
        
        // track
        foreach ($album["tracks"] as $track) {
            $statement = $db->prepare("
                INSERT INTO tracks (Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, UnitPrice)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $statement->bindValue(1, $track);
            $statement->bindValue(2, $album_id);
            $statement->bindValue(3, 1);
            $statement->bindValue(4, 1);
            $statement->bindValue(5, "");
            $statement->bindValue(6, 0);
            $statement->bindValue(7, 0);
            $statement->execute();
        }
    }
    
    $db->commit();
    echo "Artist info added successfully";

} catch (PDOException $e) {
    $db->rollBack();
    echo "Error adding artist info: " . $e->getMessage();
}