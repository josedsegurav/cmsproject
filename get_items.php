<?php
// Include database connection
require 'connect.php'; // Adjust this to match your database connection file

// Set headers to allow cross-origin requests (if needed) and specify JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Be more specific in production

try {
    // Your database query
    $query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, 
              c.category_name, u.name, u.lastname, COUNT(m.comment_id) AS comments_count 
              FROM serverside.items i 
              JOIN serverside.categories c ON c.category_id = i.category_id
              JOIN serverside.users u ON i.user_id = u.user_id
              LEFT JOIN serverside.comments m ON i.item_id = m.item_id
              GROUP BY i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, 
              c.category_name, u.name, u.lastname
              ORDER BY i.date_created DESC";

    // Prepare and execute the query
    $statement = $db->prepare($query);
    $statement->execute();

    // Fetch all results
    $items = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // Optional: Filter or transform data if needed
    // For example, you might want to limit the amount of data sent to the AI
    // $filteredItems = array_map(function($item) {
    //     // Remove potentially sensitive information
    //     unset($item['user_id']);
    //     // Format dates nicely
    //     $item['date_created'] = date('Y-m-d', strtotime($item['date_created']));
    //     return $item;
    // }, $items);
    
    // Output JSON
    echo json_encode($items);
    
} catch (PDOException $e) {
    // Return error message
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>