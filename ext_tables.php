<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'VMFDS.' . $_EXTKEY,
	'Requests',
	'VMFDS'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'VMFDS');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_vmfdsprayerrequests_domain_model_prayerrequest', 'EXT:vmfds_prayer_requests/Resources/Private/Language/locallang_csh_tx_vmfdsprayerrequests_domain_model_prayerrequest.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_vmfdsprayerrequests_domain_model_prayerrequest');
