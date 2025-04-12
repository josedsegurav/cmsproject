<?php 

function unsetRedirectSessions(){
    unset($_SESSION['categoryBrowse']);
    unset($_SESSION['dashboardTab']);
    unset($_SESSION['createUser']);
    unset($_SESSION['loginRquestItem']);
    unset($_SESSION['createItem']);
    unset($_SESSION['editUser']);
    unset($_SESSION['editItem']);
}

?>