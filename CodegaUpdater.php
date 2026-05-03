<?php
/**
 * CodegaUpdater - WiseCP Addon Module
 *
 * Codega tema ve eklentilerini admin panelinden tek tikla guncellemenizi saglar.
 *
 * @author CODEGA <https://codega.com.tr>
 * @repo   https://github.com/codegatr/wisecp-codega-updater
 * @version 1.0.0
 */

if(!defined("CORE_FOLDER")) return false;

class CodegaUpdater extends AddonModule
{
    public function __construct()
    {
        $this->_name = __CLASS__;
        parent::__construct();
    }

    /**
     * Yapilandirma alanlari (Eklenti Ayarlari sayfasi)
     */
    public function fields()
    {
        $settings = isset($this->config['settings']) ? $this->config['settings'] : [];
        return [
            'github_repo' => [
                'wrap_width'  => 100,
                'name'        => 'GitHub Deposu',
                'description' => 'Tema kaynaginin GitHub deposu (kullanici/repo formatinda)',
                'type'        => 'text',
                'value'       => isset($settings['github_repo']) ? $settings['github_repo'] : 'codegatr/wisecp-codega-theme',
                'placeholder' => 'codegatr/wisecp-codega-theme',
            ],
            'theme_path' => [
                'wrap_width'  => 100,
                'name'        => 'Tema Yolu',
                'description' => 'WISECP kurulumunda temanin bulundugu rolatif yol',
                'type'        => 'text',
                'value'       => isset($settings['theme_path']) ? $settings['theme_path'] : 'templates/website/Codega',
                'placeholder' => 'templates/website/Codega',
            ],
            'auto_check' => [
                'wrap_width'  => 50,
                'name'        => 'Otomatik Kontrol',
                'description' => 'Belirli araliklarla yeni surum kontrolu yap',
                'type'        => 'checkbox',
                'value'       => isset($settings['auto_check']) ? $settings['auto_check'] : 1,
            ],
            'check_interval' => [
                'wrap_width'  => 50,
                'name'        => 'Kontrol Araligi (saniye)',
                'description' => 'Varsayilan: 86400 (24 saat)',
                'type'        => 'text',
                'value'       => isset($settings['check_interval']) ? $settings['check_interval'] : 86400,
                'placeholder' => '86400',
            ],
        ];
    }

    /**
     * Eklenti aktif edildiginde calisir
     */
    public function activate()
    {
        return ['status' => 'successful', 'message' => 'Codega Updater eklentisi etkinlestirildi.'];
    }

    /**
     * Eklenti devre disi birakildiginda calisir
     */
    public function deactivate()
    {
        return ['status' => 'successful', 'message' => 'Codega Updater eklentisi devre disi birakildi.'];
    }

    /**
     * Admin paneli icerik output'u
     * URL: /yns/extra/CodegaUpdater
     */
    public function adminArea()
    {
        $action = Filter::init("REQUEST/action", "route");
        if(!$action) $action = 'index';

        // AJAX action'lar
        if(in_array($action, ['check', 'apply', 'download'])) {
            return $this->handleAjax($action);
        }

        $variables = [
            'link'      => $this->area_link,
            'dir_link'  => $this->url,
            'dir_path'  => $this->dir,
            'dir_name'  => $this->_name,
            'name'      => $this->lang["meta"]["name"],
            'version'   => $this->config["meta"]["version"],
            'fields'    => $this->fields(),
            'settings'  => isset($this->config['settings']) ? $this->config['settings'] : [],
        ];

        return [
            'page_title'  => 'Codega Tema Guncelleme Merkezi',
            'breadcrumbs' => [
                ['link' => '', 'title' => 'Codega Updater'],
            ],
            'content' => $this->view($action . ".php", $variables)
        ];
    }

    /**
     * AJAX endpoint'leri (check, apply, download)
     */
    protected function handleAjax($action)
    {
        $settings = isset($this->config['settings']) ? $this->config['settings'] : [];
        $repo = $settings['github_repo'] ?? 'codegatr/wisecp-codega-theme';
        $theme_path = $settings['theme_path'] ?? 'templates/website/Codega';

        $result = ['status' => 'error', 'message' => 'Bilinmeyen hata'];

        try {
            if($action === 'check') {
                $result = $this->checkUpdate($repo, $theme_path);
            }
            elseif($action === 'apply') {
                $result = $this->applyUpdate($repo, $theme_path);
            }
            elseif($action === 'download') {
                // Sadece indir, uygulama
                $url = "https://api.github.com/repos/{$repo}/releases/latest";
                $info = $this->fetchJson($url);
                if($info && isset($info['zipball_url'])) {
                    $result = ['status' => 'successful', 'url' => $info['zipball_url']];
                } else {
                    $result = ['status' => 'error', 'message' => 'GitHub release bulunamadi'];
                }
            }
        } catch(\Throwable $e) {
            $result = ['status' => 'error', 'message' => $e->getMessage()];
        }

        // JSON cevap
        return [
            'page_title' => '',
            'content'    => '<script>parent.postMessage(' . json_encode($result) . ', "*");</script>'
        ];
    }

    /**
     * Yeni surum kontrol
     */
    protected function checkUpdate($repo, $theme_path)
    {
        $manifest_path = ROOTDIR . '/' . trim($theme_path, '/') . '/manifest.json';
        if(!file_exists($manifest_path)) {
            return ['status' => 'error', 'message' => 'Tema manifest.json bulunamadi: ' . $manifest_path];
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);
        $current_version = $manifest['version'] ?? '0.0.0';
        $check_url = $manifest['checking_version_url'] ?? "https://raw.githubusercontent.com/{$repo}/main/version.json";

        $remote = $this->fetchJson($check_url);
        if(!$remote || !isset($remote['version'])) {
            return ['status' => 'error', 'message' => 'GitHub version.json okunamadi'];
        }

        $remote_version = $remote['version'];
        $has_update = version_compare($remote_version, $current_version, '>');

        return [
            'status'          => 'successful',
            'current_version' => $current_version,
            'remote_version'  => $remote_version,
            'has_update'      => $has_update,
            'changelog'       => $remote['changelog'] ?? [],
            'release_date'    => $remote['release_date'] ?? '',
            'download_url'    => $remote['file_url'] ?? "https://github.com/{$repo}/archive/refs/heads/main.zip",
        ];
    }

    /**
     * Guncellemeyi uygula (indir + ac + replace)
     */
    protected function applyUpdate($repo, $theme_path)
    {
        $check = $this->checkUpdate($repo, $theme_path);
        if($check['status'] !== 'successful') return $check;
        if(!$check['has_update']) {
            return ['status' => 'successful', 'message' => 'Tema zaten en guncel surumde.'];
        }

        // GitHub'dan release'in zipball'unu indir
        $api_url = "https://api.github.com/repos/{$repo}/releases/latest";
        $rel = $this->fetchJson($api_url);

        // Asset varsa kullan (manifest.json'daki Codega-vX.Y.Z.zip)
        $download_url = '';
        if($rel && isset($rel['assets']) && is_array($rel['assets'])) {
            foreach($rel['assets'] as $a) {
                if(stripos($a['name'] ?? '', 'codega') !== false && stripos($a['name'], '.zip') !== false) {
                    $download_url = $a['browser_download_url'];
                    break;
                }
            }
        }
        if(!$download_url) $download_url = $rel['zipball_url'] ?? '';
        if(!$download_url) {
            return ['status' => 'error', 'message' => 'Indirme URL\'i bulunamadi'];
        }

        // ZIP indir
        $tmp_zip = sys_get_temp_dir() . '/codega-update-' . uniqid() . '.zip';
        $zip_data = $this->fetchUrl($download_url);
        if(!$zip_data) {
            return ['status' => 'error', 'message' => 'ZIP indirilemedi'];
        }
        file_put_contents($tmp_zip, $zip_data);

        // ZIP'i ac
        $tmp_dir = sys_get_temp_dir() . '/codega-update-' . uniqid();
        if(!class_exists('ZipArchive')) {
            return ['status' => 'error', 'message' => 'ZipArchive PHP eklentisi yuklu degil'];
        }
        $zip = new \ZipArchive();
        if($zip->open($tmp_zip) !== true) {
            return ['status' => 'error', 'message' => 'ZIP acilamadi'];
        }
        @mkdir($tmp_dir, 0755, true);
        $zip->extractTo($tmp_dir);
        $zip->close();
        @unlink($tmp_zip);

        // Tema dosyalarinin oldugu klasoru bul
        $entries = array_values(array_diff(scandir($tmp_dir), ['.', '..']));
        $source_dir = $tmp_dir;
        if(count($entries) === 1 && is_dir($tmp_dir . '/' . $entries[0])) {
            $source_dir = $tmp_dir . '/' . $entries[0];
            // Bu da bir wrapper olabilir (Codega/...)
            $sub = array_values(array_diff(scandir($source_dir), ['.', '..']));
            if(count($sub) === 1 && is_dir($source_dir . '/' . $sub[0]) &&
               in_array($sub[0], ['Codega', 'theme'])) {
                $source_dir = $source_dir . '/' . $sub[0];
            }
        }

        // Hedef tema dizinine kopyala (config.php hariç)
        $target = ROOTDIR . '/' . trim($theme_path, '/');
        $copied = $this->copyDir($source_dir, $target, ['config.php', 'theme-config.php']);

        // Geçici klasörü temizle
        $this->removeDir($tmp_dir);

        return [
            'status'  => 'successful',
            'message' => "Tema {$check['current_version']} -> {$check['remote_version']} guncellendi. {$copied} dosya kopyalandi.",
        ];
    }

    /**
     * URL'den JSON cek
     */
    protected function fetchJson($url)
    {
        $body = $this->fetchUrl($url);
        if(!$body) return null;
        $data = json_decode($body, true);
        return $data;
    }

    /**
     * URL'den ham veri cek (cURL)
     */
    protected function fetchUrl($url)
    {
        if(!function_exists('curl_init')) return null;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'CodegaUpdater/1.0',
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/octet-stream, application/vnd.github.v3+json, */*',
            ],
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code >= 200 && $code < 400) ? $resp : null;
    }

    /**
     * Recursive klasor kopyalama (excluded files atlanir)
     */
    protected function copyDir($src, $dst, $exclude = [])
    {
        $count = 0;
        if(!is_dir($src)) return 0;
        if(!is_dir($dst)) @mkdir($dst, 0755, true);
        $items = array_diff(scandir($src), ['.', '..']);
        foreach($items as $item) {
            if(in_array($item, $exclude)) continue;
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if(is_dir($s)) {
                $count += $this->copyDir($s, $d, $exclude);
            } else {
                if(@copy($s, $d)) $count++;
            }
        }
        return $count;
    }

    /**
     * Recursive klasor silme
     */
    protected function removeDir($dir)
    {
        if(!is_dir($dir)) return;
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach($items as $item) {
            $p = $dir . '/' . $item;
            if(is_dir($p)) $this->removeDir($p);
            else @unlink($p);
        }
        @rmdir($dir);
    }
}

/**
 * Hook: Admin sidebar menusune "Codega Updater" linkini ekle
 * (Eklenti zaten admin panelinde Eklentiler altinda gorunur,
 *  bu hook ek bir kisayol saglar)
 */
Hook::add("AdminAreaSidebarMenuTools", 1, function($params = []) {
    return [
        'name'   => 'Codega Tema',
        'icon'   => 'fa fa-paint-brush',
        'url'    => 'extra/CodegaUpdater',
        'access' => true,
    ];
});
