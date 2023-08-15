<?php
require("request.php");
$response = Request::request("https://namaztimes.kz/ru/api/country", "", []);
file_put_contents("log.txt", json_encode($response, JSON_PRETTY_PRINT));