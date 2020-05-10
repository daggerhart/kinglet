<?php

// Define paths when instantiating the Finder object.
$finder = new \Kinglet\FileSystem\Finder([
	get_stylesheet_directory(),
]);

// Find files using the files() method.
// Search with an array of patterns to match and/or not-match.
$files = $finder->files(['*.php'], ['func*', 'index*', '404*']);

/** @var \SplFileInfo $fileinfo */
foreach ($files as $fileinfo) {
	echo $fileinfo->getRealPath() . '<br>';
}

// Or set paths with the in() method.
$dirs = $finder->in(ABSPATH . 'wp-content/uploads')->recurse()->dirs();

/** @var \SplFileInfo $fileinfo */
foreach ($dirs as $fileinfo) {
	echo $fileinfo->getRealPath() . '<br>';
}
