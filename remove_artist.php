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
    $db->beginTransaction();

    // playlist_track
    $statement = $db->prepare("
        DELETE FROM playlist_track
        WHERE TrackId IN (
            SELECT TrackId
            FROM tracks
            WHERE AlbumId IN (
                SELECT AlbumId
                FROM albums
                WHERE ArtistId = ?
            )
        )
    ");
    $statement->execute([$artist_id]);

    // invoice_items
    $statement = $db->prepare("
        DELETE FROM invoice_items
        WHERE TrackId IN (
            SELECT TrackId
            FROM tracks
            WHERE AlbumId IN (
                SELECT AlbumId
                FROM albums
                WHERE ArtistId = ?
            )
        )
    ");
    $statement->execute([$artist_id]);

    // tracks
    $statement = $db->prepare("
        DELETE FROM tracks
        WHERE AlbumId IN (
            SELECT AlbumId
            FROM albums
            WHERE ArtistId = ?
        )
    ");
    $statement->execute([$artist_id]);

    // albums
    $statement = $db->prepare("
        DELETE FROM albums
        WHERE ArtistId = ?
    ");
    $statement->execute([$artist_id]);

    // artists
    $statement = $db->prepare("
        DELETE FROM artists
        WHERE ArtistId = ?
    ");
    $statement->execute([$artist_id]);

    $db->commit();
    echo "Artist and related data deleted successfully.";

} catch (PDOException $e) {
    $db->rollBack();
    echo "Error deleting artist: " . $e->getMessage();
}