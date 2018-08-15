<?php

$config = array(
	'bb-theme' => array(
		'name' => 'Beaver Builder Theme',
	),
	'bb-plugin' => array(
		'name' => 'Beaver Builder Plugin',
	),
	'bb-themer' => array(
		'name' => 'Beaver Themer',
	),
);

$nav = array(
	'bb-plugin' => array(
		'name' => 'Beaver Builder',
		'link' => 'https://hooks.wpbeaverbuilder.com/bb-plugin/',
	),
	'bb-themer' => array(
		'name' => 'Themer',
		'link' => 'https://hooks.wpbeaverbuilder.com/bb-themer/',
	),
	'bb-theme' => array(
		'name' => 'Theme',
		'link' => 'https://hooks.wpbeaverbuilder.com/bb-theme',
	),
	'kb' => array(
		'name' => 'Knowledge Base',
		'link' => 'https://kb.wpbeaverbuilder.com/',
	),
	'hook' => array(
		'name' => 'Hooks JSON',
		'link' => $_SERVER['REQUEST_URI'] . '?json',
	),
);
