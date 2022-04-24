<?php

/**
 * Twig loader, initializes Twig with standard configurations and extensions.
 *
 * @param string $subfolder Subdirectory to use in the templates/ directory.
 * @return \Twig\Environment Twig object.
 */
function twigloader($subfolder = '') {
	global $tplCache, $tplNoCache, $log, $userdata;

	$doCache = ($tplNoCache ? false : $tplCache);

	$loader = new \Twig\Loader\FilesystemLoader('templates/' . $subfolder);

	$twig = new \Twig\Environment($loader, [
		'cache' => $doCache,
	]);

	$twig->addExtension(new NamespaceDBExtension());

	return $twig;
}

class NamespaceDBExtension extends \Twig\Extension\AbstractExtension {
	public function getFunctions() {
		global $profiler;
		return [
			new \Twig\TwigFunction('profiler_stats', function () use ($profiler) {
				$profiler->getStats();
			})
		];
	}
}
