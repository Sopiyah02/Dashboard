<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("config/database.php");

if ($conn) {
    echo "DB Connected!";
} else {
    echo "DB Failed!";
}