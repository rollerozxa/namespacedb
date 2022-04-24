<?php
require('lib/common.php');

$twig = twigloader();

$namespaces = $cache->hit('namespaces', function () {
	return fetchArray(query("SELECT * FROM namespaces ORDER BY `namespace` ASC"));
});

if (isset($_GET['raw-json'])) {
	header('Content-Type: application/json');
	echo json_encode($namespaces);
} else {
	echo $twig->render('index.twig', [
		'namespaces' => $namespaces
	]);
}
