<?php
    Class CodegaUpdater extends AddonModule {
        public $version = "1.0";

        function __construct(){
            $this->_name = __CLASS__;
            parent::__construct();
        }

        public function fields(){
            $settings = isset($this->config['settings']) ? $this->config['settings'] : [];
            return [
                'github_repo'  => [
                    'wrap_width'    => 100,
                    'name'          => "GitHub Deposu",
                    'description'   => "Tema kaynağının GitHub deposu (kullanici/repo formatinda)",
                    'type'          => "text",
                    'value'         => isset($settings["github_repo"]) ? $settings["github_repo"] : "codegatr/wisecp-codega-theme",
                    'placeholder'   => "codegatr/wisecp-codega-theme",
                ],
                'theme_path'   => [
                    'wrap_width'    => 100,
                    'name'          => "Tema Yolu",
                    'description'   => "WISECP kurulumunda temanin bulundugu yol",
                    'type'          => "text",
                    'value'         => isset($settings["theme_path"]) ? $settings["theme_path"] : "templates/website/Codega",
                    'placeholder'   => "templates/website/Codega",
                ],
            ];
        }

        public function save_fields($fields = []){
            return $fields;
        }

        public function activate(){
            return true;
        }

        public function deactivate(){
            return true;
        }

        public function upgrade(){
            return true;
        }

        public function adminArea()
        {
            $action = Filter::init("REQUEST/action","route");
            if(!$action) $action = 'index';

            // AJAX endpoint'leri
            if($action === 'check' || $action === 'apply') {
                return $this->ajaxAction($action);
            }

            $variables = [
                'link'              => $this->area_link,
                'dir_link'          => $this->url,
                'dir_path'          => $this->dir,
                'dir_name'          => $this->_name,
                'name'              => $this->lang["meta"]["name"],
                'version'           => $this->config["meta"]["version"],
                'fields'            => $this->fields(),
                'settings'          => isset($this->config['settings']) ? $this->config['settings'] : [],
            ];

            return [
                'page_title'        => 'Codega Tema Guncelleme',
                'breadcrumbs'       => [
                    [
                        'link'      => '',
                        'title'     => 'Codega Updater',
                    ],
                ],
                'content'           => $this->view($action.".php",$variables)
            ];
        }

        /**
         * AJAX endpoint - JSON gomulu HTML donus
         */
        public function ajaxAction($action)
        {
            $settings = isset($this->config['settings']) ? $this->config['settings'] : [];
            $repo = isset($settings['github_repo']) ? $settings['github_repo'] : 'codegatr/wisecp-codega-theme';
            $theme_path = isset($settings['theme_path']) ? $settings['theme_path'] : 'templates/website/Codega';

            $result = ['status' => 'error', 'message' => 'Bilinmeyen islem'];

            try {
                if($action === 'check') {
                    $result = $this->checkUpdate($repo, $theme_path);
                } elseif($action === 'apply') {
                    $result = $this->applyUpdate($repo, $theme_path);
                }
            } catch(\Throwable $e) {
                $result = ['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()];
            }

            return [
                'page_title'    => '',
                'content'       => '<pre id="cdg-response">' . htmlspecialchars(json_encode($result, JSON_UNESCAPED_UNICODE), ENT_QUOTES) . '</pre>'
            ];
        }

        public function checkUpdate($repo, $theme_path)
        {
            $manifest_path = ROOTDIR . '/' . trim($theme_path, '/') . '/manifest.json';
            if(!file_exists($manifest_path)) {
                return ['status' => 'error', 'message' => 'Manifest bulunamadi: ' . $manifest_path];
            }

            $manifest = json_decode(file_get_contents($manifest_path), true);
            $current = isset($manifest['version']) ? $manifest['version'] : '0.0.0';
            $check_url = isset($manifest['checking_version_url'])
                ? $manifest['checking_version_url']
                : "https://raw.githubusercontent.com/{$repo}/main/version.json";

            $body = $this->fetchUrl($check_url);
            if(!$body) {
                return ['status' => 'error', 'message' => 'GitHub version.json okunamadi: ' . $check_url];
            }

            $remote = json_decode($body, true);
            if(!$remote || !isset($remote['version'])) {
                return ['status' => 'error', 'message' => 'GitHub version.json gecersiz format'];
            }

            return [
                'status'            => 'successful',
                'current_version'   => $current,
                'remote_version'    => $remote['version'],
                'has_update'        => version_compare($remote['version'], $current, '>'),
                'changelog'         => isset($remote['changelog']) ? $remote['changelog'] : [],
                'release_date'      => isset($remote['release_date']) ? $remote['release_date'] : '',
            ];
        }

        public function applyUpdate($repo, $theme_path)
        {
            $check = $this->checkUpdate($repo, $theme_path);
            if($check['status'] !== 'successful') return $check;
            if(!$check['has_update']) {
                return ['status' => 'successful', 'message' => 'Tema zaten guncel.'];
            }

            $api = "https://api.github.com/repos/{$repo}/releases/latest";
            $rel_body = $this->fetchUrl($api);
            if(!$rel_body) {
                return ['status' => 'error', 'message' => 'GitHub API erisilemedi'];
            }
            $rel = json_decode($rel_body, true);

            $download_url = '';
            if($rel && isset($rel['assets']) && is_array($rel['assets'])) {
                foreach($rel['assets'] as $a) {
                    if(stripos($a['name'], 'codega') !== false && stripos($a['name'], '.zip') !== false) {
                        $download_url = $a['browser_download_url'];
                        break;
                    }
                }
            }
            if(!$download_url && isset($rel['zipball_url'])) {
                $download_url = $rel['zipball_url'];
            }
            if(!$download_url) {
                return ['status' => 'error', 'message' => 'Indirme URL bulunamadi'];
            }

            $tmp_zip = sys_get_temp_dir() . '/cdg-' . uniqid() . '.zip';
            $zip_data = $this->fetchUrl($download_url);
            if(!$zip_data) {
                return ['status' => 'error', 'message' => 'ZIP indirilemedi'];
            }
            file_put_contents($tmp_zip, $zip_data);

            if(!class_exists('ZipArchive')) {
                @unlink($tmp_zip);
                return ['status' => 'error', 'message' => 'ZipArchive PHP eklentisi gerekli'];
            }
            $tmp_dir = sys_get_temp_dir() . '/cdg-' . uniqid();
            $zip = new \ZipArchive();
            if($zip->open($tmp_zip) !== true) {
                @unlink($tmp_zip);
                return ['status' => 'error', 'message' => 'ZIP acilamadi'];
            }
            @mkdir($tmp_dir, 0755, true);
            $zip->extractTo($tmp_dir);
            $zip->close();
            @unlink($tmp_zip);

            $source = $tmp_dir;
            $entries = array_values(array_diff(scandir($tmp_dir), ['.', '..']));
            if(count($entries) === 1 && is_dir($tmp_dir . '/' . $entries[0])) {
                $source = $tmp_dir . '/' . $entries[0];
                $sub = array_values(array_diff(scandir($source), ['.', '..']));
                if(count($sub) === 1 && is_dir($source . '/' . $sub[0])) {
                    $source = $source . '/' . $sub[0];
                }
            }

            $target = ROOTDIR . '/' . trim($theme_path, '/');
            $copied = $this->copyDir($source, $target, ['config.php', 'theme-config.php']);
            $this->removeDir($tmp_dir);

            return [
                'status'    => 'successful',
                'message'   => "Guncelleme basarili: v{$check['current_version']} -> v{$check['remote_version']} ({$copied} dosya kopyalandi)"
            ];
        }

        public function fetchUrl($url)
        {
            if(!function_exists('curl_init')) return null;
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_USERAGENT       => 'CodegaUpdater/1.0',
                CURLOPT_TIMEOUT         => 60,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_HTTPHEADER      => [
                    'Accept: application/octet-stream, application/vnd.github.v3+json, */*',
                ],
            ]);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($code >= 200 && $code < 400) ? $resp : null;
        }

        public function copyDir($src, $dst, $exclude = [])
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

        public function removeDir($dir)
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
