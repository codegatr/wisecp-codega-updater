<?php
/**
 * CodegaUpdater - WiseCP Addon
 *
 * Codega temalarini ve modullerini WiseCP yonetici panelinden
 * tek tikla guncellemenizi saglar. GitHub Release uzerinden
 * versiyon kontrolu, indirme ve uygulama yapar.
 *
 * @author CODEGA <https://codega.com.tr>
 * @license MIT
 */

return [
    'created_at' => 1714720000,
    'meta' => [
        'name'         => 'Codega Updater',
        'version'      => '1.0.0',
        'author'       => 'CODEGA',
        'opening-type' => 'normal',
    ],
    'show_on_adminArea'  => true,
    'show_on_clientArea' => false,   // Sadece admin tarafinda
    'status'             => false,    // Kurulduktan sonra admin etkinlestirir
    'access_ps'          => [],
    'settings'           => [
        // Yapilandirilabilir alanlar (admin tarafindan)
        'github_repo'    => 'codegatr/wisecp-codega-theme',
        'theme_path'     => 'templates/website/Codega',
        'auto_check'     => 1,
        'check_interval' => 86400, // 24 saat
    ],
];
