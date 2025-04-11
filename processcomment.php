<?php
    session_start();

    if(empty($_SESSION['user'])){
        header("Location: /webdev2/project/login");
    }

    require('connect.php');

    $title = "Comment Process";

    function filterInput() {
        if (
            $_POST && 
            !empty($_POST['item_id']) &&
            !empty($_POST['user_id']) && 
            !empty($_POST['comment_text']) &&
            !(trim($_POST['comment_text']) == '')){
            return true;
        }else{
            return false;
        }
    }

    if(filterInput()){

        if(isset($_POST['addcomment'])){
        
            $item_id = filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $content = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "INSERT INTO comments (user_id, item_id, comment_content) 
                        VALUES (:user_id, :item_id, :content)";
            
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query.
            $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $statement->bindValue(':item_id', $item_id, PDO::PARAM_INT);
            $statement->bindValue(':content', $content, PDO::PARAM_STR);

            // Execution on the DB server.
            $statement->execute();
            
            if (isset($_SESSION['previous_page']) && isset($_SESSION['current_page'])) {
                if($_SESSION['current_page'] === "/webdev2/project/item.php"){
                header("Location: " . $_SESSION['previous_page']);
                }
        }
    }
    }

    if(isset($_POST['confirm'])){
        // Sanitizing id data into a number.
        $id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);

        // SQL query
        $query = "DELETE FROM comments WHERE comment_id = :id";

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execution on the DB server.
        $statement->execute();

        // Variable session message added with delete message.

        $_SESSION['message'] = "Item Deleted.";

        // Then it is redirected to index.php.
        header("Location: /webdev2/project/dashboard/comments");

    }

    if(isset($_POST['updateView'])){

        $id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = "UPDATE comments 
            SET status = :status 
            WHERE comment_id = :id";
    
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
            $statement->bindValue(':id', $id, PDO::PARAM_INT);

        if($status === "accepted" || $status === "censored"|| $status === "review"){
            
            $statement->bindValue(':status', 'hidden', PDO::PARAM_STR);
            // Execution on the DB server.
            $statement->execute();

        }elseif($status === "hidden" && ($_POST['censored'] === 'not censored')){

            $statement->bindValue(':status', 'accepted', PDO::PARAM_STR);
            // Execution on the DB server.
            $statement->execute();

        }elseif($status === "hidden" && ($_POST['censored'] === 'censored')){

            $statement->bindValue(':status', 'censored', PDO::PARAM_STR);
            // Execution on the DB server.
            $statement->execute();

        }else{

            header("Location: /webdev2/project/dashboard/comments");
            exit();
        }
            header("Location: /webdev2/project/dashboard/comments");

    }

    if(isset($_POST['review'])){

        $id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
        $reviewComment = filter_input(INPUT_POST, 'review_reason', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $query = "UPDATE comments 
            SET status = :status, review_reason = :review 
            WHERE comment_id = :id";
    
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':status', 'review', PDO::PARAM_STR);
            $statement->bindValue(':review', $reviewComment, PDO::PARAM_STR);
            // Execution on the DB server.
            $statement->execute();
           
            header("Location: /webdev2/project/dashboard/comments");

    }

    if(isset($_POST['disvowel'])){

        $disvowelContent = str_replace(array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'), '', $_POST['content']);

        $id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
        $content = filter_var($disvowelContent, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $query = "UPDATE comments 
            SET disvowel_comment = :content, status = :status 
            WHERE comment_id = :id";
    
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':content', $content, PDO::PARAM_STR);
            $statement->bindValue(':status', 'censored', PDO::PARAM_STR);
            // Execution on the DB server.
            $statement->execute();
           
            header("Location: /webdev2/project/dashboard/comments");

    }
?>