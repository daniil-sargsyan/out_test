<?php
require("request.php");
$response = Request::request("https://restcountries.com/v3.1/all", "", []);
file_put_contents("log.txt", json_encode($response, JSON_PRETTY_PRINT));