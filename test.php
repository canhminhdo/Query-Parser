<?php

include "QueryParser.php";

$query = '"Xin chao" (name: "Canh" AND name: "Thanh" OR (precondition: "TESTING" AND name: "Minh")) AND tags: "tag1,tag2"';
$result = QueryParser::parse($query, 'TestResult');
print_r($result);