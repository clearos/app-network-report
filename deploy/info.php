<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'network_report';
$app['version'] = '2.0.0';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('network_report_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('network_report_app_name');
$app['category'] = lang('base_category_reports');
$app['subcategory'] = lang('base_category_network');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['network_report']['title'] = $app['name'];
$app['controllers']['settings']['title'] = lang('base_settings');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_requires'] = array(
    'app-network-core >= 1:1.4.3',
    'app-reports-core >= 1:1.4.8',
    'app-reports-database-core >= 1:1.4.30',
    'app-tasks-core',
);

$app['core_file_manifest'] = array(
    'app-network-report.cron' => array( 'target' => '/etc/cron.d/app-network-report'),
    'network2db' => array(
        'target' => '/usr/sbin/network2db',
        'mode' => '0755',
    ),
);

$app['delete_dependency'] = array(
    'app-network-report-core'
);
