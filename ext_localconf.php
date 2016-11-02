<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'VMFDS.' . $_EXTKEY,
	'Requests',
	array(
		'PrayerRequest' => 'show, list, create, new, edit, update, delete, resendConfirmation, answered, listSignup, listSubscribe',
		
	),
	// non-cacheable actions
	array(
		'PrayerRequest' => 'create, update, delete, resendConfirmation, answered, listSubscribe',
		
	)
);
