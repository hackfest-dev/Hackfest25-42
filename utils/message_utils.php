<?php
/**
 * Utility functions for handling messages and notifications
 */

/**
 * Get count of unread announcements for a specific user
 * 
 * @param string $username The username to check unread messages for
 * @param object $conn The database connection
 * @return int Number of unread messages
 */
function getUnreadMessageCount($username, $conn) {
    // Make sure username is safe for SQL query
    $username = mysqli_real_escape_string($conn, $username);
    
    // Count all messages and subtract the ones the user has read
    $sql = "SELECT COUNT(*) as total FROM message 
            WHERE id NOT IN (
                SELECT message_id FROM read_messages WHERE uname = '$username'
            )";
    
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    
    return 0;
}

/**
 * Mark all messages as read for a specific user
 * 
 * @param string $username The username to mark messages read for
 * @param object $conn The database connection
 * @return bool True if successful, false otherwise
 */
function markAllMessagesAsRead($username, $conn) {
    // Make sure username is safe for SQL query
    $username = mysqli_real_escape_string($conn, $username);
    
    // Get all message IDs
    $message_ids = [];
    $sql = "SELECT id FROM message";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $message_ids[] = $row['id'];
        }
    }
    
    // If no messages, return true
    if (empty($message_ids)) {
        return true;
    }
    
    // Insert read records for each message
    $success = true;
    foreach ($message_ids as $message_id) {
        $sql = "INSERT IGNORE INTO read_messages (message_id, uname) VALUES ('$message_id', '$username')";
        if (!mysqli_query($conn, $sql)) {
            $success = false;
            error_log("Error marking message $message_id as read for $username: " . mysqli_error($conn));
        }
    }
    
    return $success;
}
?> 