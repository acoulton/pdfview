<?php
// Configuration for koharness - builds a standalone skeleton Kohana app for running specs
return array(
	'modules' => array(
		'pdfview'  => __DIR__,
		'unittest' => __DIR__.'/vendor/kohana/unittest'
	),
);
