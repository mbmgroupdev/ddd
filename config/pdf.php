<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'MBM Group',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),
	'keywords'         => 'PDF, Laravel, Package, Peace', 
	'display_mode'     => 'fullpage',
	'font_path' => base_path('/public/assets/fonts/'),
	'font_data' => [
		'bangla' => [
			'R'  => 'SolaimanLipi.ttf', // regular font
			'B'  => 'SolaimanLipi.ttf', // optional: bold font
			'I'  => 'SolaimanLipi.ttf', // optional: italic font
			'BI' => 'SolaimanLipi.ttf', // optional: bold-italic font
			'useOTL' => 0xFF,   
			'useKashida' => 75, 
		]
	]
];
