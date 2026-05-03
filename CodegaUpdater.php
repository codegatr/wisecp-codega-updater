<?php
/**
 * CodegaUpdater v3.0.0 — Smart Update Manager
 *
 * ERP'nin guncelleme_github.php sisteminden WiseCP eklenti formatına port edildi.
 *
 * Mimari:
 *   - extends AddonModule (WiseCP standart)
 *   - 5 sekmeli UI: Genel Durum / Dosyalar / Commits / Yedekler / Ayarlar
 *   - Git blob-SHA ile dosya-bazli karsilastirma (Smart Sync)
 *   - Force Sync (tum dosyalari yeniden indirme)
 *   - Tekil dosya guncelleme
 *   - GitHub commit history goruntuleme
 *   - Otomatik yedekleme (max 10) + restore
 *   - GitHub token yonetimi (.gh_token dosyasi)
 *   - WiseCP framework execution_time bypass'i icin set_time_limit(0)
 *
 * @author CODEGA
 */
class CodegaUpdater extends AddonModule {

    public $version = "3.0.0";

    // ── Sabitler ─────────────────────────────────────────────────────
    const GH_BRANCH = 'main';
    const MAX_BK    = 10;

    // Korunan dosya/klasorler — Smart Sync'te de Force Sync'te de atlanir
    const EXCLUDES = [
        // Eklenti kendi dosyalari (kendini guncellemeye calismasin)
        // (theme repo'sundan eklenti yolu zaten farkli, ama emin olmak icin)
        'config.php', '.htaccess', '.gitignore', '.git/',
        'node_modules/', 'vendor/',
        // Tema'ya ozel sakli dosyalar
        '.codega-update.log', '.codega-update.pass',
    ];

    function __construct() {
        $this->_name = __CLASS__;
        parent::__construct();
    }

    public function fields() {
        $settings = $this->config['settings'] ?? [];
        return [
            'github_repo' => [
                'wrap_width'  => 50,
                'name'        => 'GitHub Deposu',
                'description' => 'kullanici/repo formati',
                'type'        => 'text',
                'value'       => $settings['github_repo'] ?? 'codegatr/wisecp-codega-theme',
                'placeholder' => 'codegatr/wisecp-codega-theme',
            ],
            'theme_path' => [
                'wrap_width'  => 50,
                'name'        => 'Tema Yolu',
                'description' => 'WiseCP root\'a gore goreceli',
                'type'        => 'text',
                'value'       => $settings['theme_path'] ?? 'templates/website/Codega',
                'placeholder' => 'templates/website/Codega',
            ],
        ];
    }

    public function save_fields($fields = []) { return $fields; }
    public function activate() { return true; }
    public function deactivate() { return true; }
    public function upgrade() { return true; }

    // ── adminArea: ana sayfa veya AJAX dispatcher ────────────────────
    public function adminArea() {
        // Uzun islem icin
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');
        @ignore_user_abort(true);

        // AJAX endpoint
        $ajax = Filter::init("REQUEST/cdg_ajax", "letters_numbers", "_");
        if ($ajax) {
            return $this->ajaxDispatch($ajax);
        }

        // Ana sayfa render
        $variables = [
            'link'         => $this->area_link,
            'dir_link'     => $this->url,
            'dir_path'     => $this->dir,
            'dir_name'     => $this->_name,
            'name'         => $this->lang['meta']['name'] ?? 'Codega Updater',
            'version'      => $this->config['meta']['version'] ?? $this->version,
            'gh_repo'      => $this->ghRepo(),
            'gh_branch'    => self::GH_BRANCH,
            'theme_path'   => $this->themePath(),
            'theme_root'   => $this->themeRoot(),
            'local_ver'    => $this->localVer(),
            'has_token'    => $this->getTok() !== '',
            'fields'       => $this->fields(),
        ];

        return [
            'page_title'  => 'Codega Smart Update Manager',
            'breadcrumbs' => [
                ['link' => '', 'title' => 'Eklentiler'],
                ['link' => '', 'title' => 'Codega Updater'],
            ],
            'content'     => $this->view('index.php', $variables),
        ];
    }

    // ── AJAX Dispatcher ──────────────────────────────────────────────
    private function ajaxDispatch($action) {
        // Output buffer temizle (WiseCP'nin yan etkilerini engelle)
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        $tok = $this->getTok();

        try {
            switch ($action) {
                case 'status':       echo json_encode($this->actStatus($tok)); break;
                case 'sync':         echo json_encode($this->actSync($tok, false)); break;
                case 'force_sync':   echo json_encode($this->actSync($tok, true)); break;
                case 'update_file':  echo json_encode($this->actUpdateFile($tok)); break;
                case 'commits':      echo json_encode($this->actCommits($tok)); break;
                case 'backups':      echo json_encode($this->actBackups()); break;
                case 'restore':      echo json_encode($this->actRestore()); break;
                case 'delete_backup':echo json_encode($this->actDeleteBackup()); break;
                case 'save_token':   echo json_encode($this->actSaveToken()); break;
                case 'test_token':   echo json_encode($this->actTestToken($tok)); break;
                case 'manual_zip':   echo json_encode($this->actManualZip()); break;
                default:             echo json_encode(['ok' => false, 'error' => 'Bilinmeyen islem: ' . $action]);
            }
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── AJAX Actions ─────────────────────────────────────────────────

    private function actStatus($tok) {
        if (!$tok) return ['ok' => false, 'error' => 'GitHub token ayarlanmamis. Ayarlar sekmesinden ekleyin.'];
        $rf = $this->repoTree($tok);
        if (empty($rf)) return ['ok' => false, 'error' => 'Repo agaci okunamadi - token yetkisini kontrol edin'];

        $stats = ['ok' => 0, 'diff' => 0, 'missing' => 0];
        $files = [];
        $root  = $this->themeRoot();

        foreach ($rf as $rec) {
            $lp = $root . '/' . $rec['path'];
            if (!file_exists($lp)) {
                $files[$rec['path']] = ['status' => 'missing', 'size' => $rec['size']];
                $stats['missing']++;
            } else {
                $sha = $this->blobSHA(file_get_contents($lp));
                if ($sha === $rec['sha']) {
                    $files[$rec['path']] = ['status' => 'ok', 'size' => $rec['size']];
                    $stats['ok']++;
                } else {
                    $files[$rec['path']] = ['status' => 'diff', 'size' => $rec['size']];
                    $stats['diff']++;
                }
            }
        }

        return [
            'ok'           => true,
            'local_ver'    => $this->localVer(),
            'remote_ver'   => $this->ghVer($tok),
            'stats'        => $stats,
            'total'        => count($rf),
            'files'        => $files,
            'needs_update' => ($stats['diff'] + $stats['missing']) > 0,
        ];
    }

    private function actSync($tok, $force = false) {
        if (!$tok) return ['ok' => false, 'error' => 'Token yok'];
        $log = []; $errors = []; $updated = 0;
        $root = $this->themeRoot();

        try {
            // 1) Yedek al
            $bk = $this->backup($force ? 'force' : 'sync');
            if ($bk['ok']) $log[] = '📦 Yedek: ' . $bk['name'];

            // 2) Repo agacini al
            $rf = $this->repoTree($tok);
            $log[] = ($force ? '🔥 TUM ' : '📋 ') . count($rf) . ' dosya ' . ($force ? 'yeniden indiriliyor' : 'kontrol ediliyor');

            foreach ($rf as $rec) {
                $lp = $root . '/' . $rec['path'];
                if ($this->isExcluded($rec['path'])) continue;

                // Smart sync: degismemis dosyalari atla
                if (!$force && file_exists($lp) && $this->blobSHA(file_get_contents($lp)) === $rec['sha']) continue;

                $c = $this->ghDownload($rec['path'], $tok);
                if ($c === null) {
                    $errors[] = '❌ ' . $rec['path'] . ' - indirme hatasi';
                    continue;
                }

                $dir = dirname($lp);
                if (!is_dir($dir)) @mkdir($dir, 0755, true);

                if (@file_put_contents($lp, $c) !== false) {
                    $log[] = '✅ ' . $rec['path'];
                    $updated++;
                } else {
                    $errors[] = '❌ ' . $rec['path'] . ' - yazma hatasi';
                }
            }

            $log[] = '';
            $log[] = '🎉 ' . $updated . ' dosya guncellendi';
            if (!empty($errors)) {
                $log[] = '';
                $log[] = '⚠️ ' . count($errors) . ' hata var:';
                foreach ($errors as $e) $log[] = $e;
            }
        } catch (\Throwable $e) {
            $errors[] = '❌ ' . $e->getMessage();
        }

        return [
            'ok'      => empty($errors) || $updated > 0,
            'log'     => $log,
            'errors'  => $errors,
            'updated' => $updated,
            'version' => $this->localVer(),
        ];
    }

    private function actUpdateFile($tok) {
        $file = trim($_POST['file'] ?? '');
        if (!$file || !$tok) return ['ok' => false, 'error' => 'Eksik parametre'];
        if ($this->isExcluded($file)) return ['ok' => false, 'error' => 'Korumali dosya'];

        $c = $this->ghDownload($file, $tok);
        if ($c === null) return ['ok' => false, 'error' => 'Indirme hatasi'];

        $lp = $this->themeRoot() . '/' . $file;
        if (!is_dir(dirname($lp))) @mkdir(dirname($lp), 0755, true);

        $w = @file_put_contents($lp, $c);
        return $w !== false
            ? ['ok' => true, 'bytes' => $w, 'msg' => $file . ' guncellendi (' . number_format($w) . ' byte)']
            : ['ok' => false, 'error' => 'Yazma hatasi'];
    }

    private function actCommits($tok) {
        if (!$tok) return ['ok' => false, 'error' => 'Token yok'];
        $d = $this->ghAPI('/commits?sha=' . self::GH_BRANCH . '&per_page=20', $tok);
        if (!$d) return ['ok' => false, 'error' => 'Commit alinamadi'];

        $out = [];
        foreach ($d as $c) {
            $out[] = [
                'sha'     => substr($c['sha'], 0, 7),
                'message' => $c['commit']['message'] ?? '',
                'author'  => $c['commit']['author']['name'] ?? '',
                'date'    => $c['commit']['author']['date'] ?? '',
                'url'     => $c['html_url'] ?? '',
            ];
        }
        return ['ok' => true, 'commits' => $out];
    }

    private function actBackups() {
        $bdir = $this->backupDir();
        $bks = glob($bdir . '/bk_*.zip') ?: [];
        usort($bks, fn($a, $b) => filemtime($b) <=> filemtime($a));

        return [
            'ok'      => true,
            'backups' => array_map(fn($p) => [
                'name' => basename($p),
                'size' => filesize($p),
                'time' => filemtime($p),
                'date' => date('Y-m-d H:i:s', filemtime($p)),
                'ver'  => preg_match('/_v([\d.]+)\.zip$/', basename($p), $m) ? $m[1] : '?',
            ], $bks),
        ];
    }

    private function actRestore() {
        $name = basename(trim($_POST['backup'] ?? ''));
        if (!$name) return ['ok' => false, 'error' => 'Yedek belirtilmedi'];

        $path = $this->backupDir() . '/' . $name;
        if (!file_exists($path)) return ['ok' => false, 'error' => 'Dosya yok'];

        $log = [];
        $safeBk = $this->backup('pre_restore');
        if ($safeBk['ok']) $log[] = '📦 Guvenlik yedegi: ' . $safeBk['name'];

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) return ['ok' => false, 'error' => 'ZIP acilamadi'];

        $r = 0;
        $root = $this->themeRoot();
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $f = $zip->getNameIndex($i);
            $c = $zip->getFromIndex($i);
            if ($c === false || $this->isExcluded($f)) continue;

            $t = $root . '/' . $f;
            if (!is_dir(dirname($t))) @mkdir(dirname($t), 0755, true);

            if (@file_put_contents($t, $c) !== false) {
                $log[] = '✅ ' . $f;
                $r++;
            } else {
                $log[] = '❌ ' . $f;
            }
        }
        $zip->close();
        $log[] = '';
        $log[] = '🎉 ' . $r . ' dosya geri yuklendi';

        return ['ok' => true, 'log' => $log, 'restored' => $r, 'version' => $this->localVer()];
    }

    private function actDeleteBackup() {
        $name = basename(trim($_POST['backup'] ?? ''));
        $path = $this->backupDir() . '/' . $name;
        if (file_exists($path)) @unlink($path);
        return ['ok' => true];
    }

    private function actSaveToken() {
        $t = preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($_POST['token'] ?? ''));
        if (strlen($t) < 20) return ['ok' => false, 'error' => 'Token cok kisa (min 20 karakter)'];

        $tokFile = $this->tokenFile();
        $ok = @file_put_contents($tokFile, $t) !== false;
        if ($ok) @chmod($tokFile, 0600);
        return ['ok' => $ok];
    }

    private function actTestToken($tok) {
        if (!$tok) return ['ok' => false, 'error' => 'Token yok'];
        $d = $this->ghAPI('', $tok);
        if ($d && !empty($d['full_name'])) {
            return ['ok' => true, 'repo' => $d['full_name'], 'private' => $d['private'] ?? false, 'desc' => $d['description'] ?? ''];
        }
        return ['ok' => false, 'error' => 'Gecersiz token veya repo erisimi yok'];
    }

    private function actManualZip() {
        if (empty($_FILES['zip']) || $_FILES['zip']['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'ZIP yuklenmedi'];
        }
        $zip_path = $_FILES['zip']['tmp_name'];

        if (filesize($zip_path) > 50 * 1024 * 1024) return ['ok' => false, 'error' => 'ZIP cok buyuk (max 50MB)'];

        $log = [];
        $log[] = '📦 ZIP boyut: ' . number_format(filesize($zip_path)) . ' byte';

        // Yedek al
        $bk = $this->backup('manual');
        if ($bk['ok']) $log[] = '📦 Yedek: ' . $bk['name'];

        $zip = new ZipArchive();
        $r = $zip->open($zip_path);
        if ($r !== true) return ['ok' => false, 'error' => 'ZIP acilamadi (kod ' . $r . ')'];

        // manifest.json bul (kok ya da alt klasorde)
        $manifest = $zip->getFromName('manifest.json');
        $prefix = '';
        if (!$manifest) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (basename($entry) === 'manifest.json') {
                    $manifest = $zip->getFromIndex($i);
                    $dir = dirname($entry);
                    $prefix = ($dir !== '.') ? rtrim($dir, '/') . '/' : '';
                    break;
                }
            }
        }
        if (!$manifest) {
            $zip->close();
            return ['ok' => false, 'error' => 'manifest.json yok'];
        }

        $mdata = @json_decode($manifest, true);
        if (!is_array($mdata) || empty($mdata['version'])) {
            $zip->close();
            return ['ok' => false, 'error' => 'manifest.json gecersiz'];
        }
        $log[] = '✅ Manifest: v' . $mdata['version'] . ' (prefix: ' . ($prefix ?: '/') . ')';

        // Dosyalari kopyala
        $root = $this->themeRoot();
        $copied = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            // prefix'i strip et
            if ($prefix && strpos($entry, $prefix) === 0) {
                $rel = substr($entry, strlen($prefix));
            } else {
                $rel = $entry;
            }
            if (!$rel || substr($rel, -1) === '/') continue; // klasor
            if ($this->isExcluded($rel)) continue;

            $c = $zip->getFromIndex($i);
            if ($c === false) continue;

            $target = $root . '/' . $rel;
            if (!is_dir(dirname($target))) @mkdir(dirname($target), 0755, true);

            if (@file_put_contents($target, $c) !== false) {
                $copied++;
            }
        }
        $zip->close();
        $log[] = '🎉 ' . $copied . ' dosya kopyalandi';

        return ['ok' => true, 'log' => $log, 'updated' => $copied, 'version' => $this->localVer()];
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function ghRepo() {
        return $this->config['settings']['github_repo'] ?? 'codegatr/wisecp-codega-theme';
    }

    private function themePath() {
        return $this->config['settings']['theme_path'] ?? 'templates/website/Codega';
    }

    private function themeRoot() {
        // ROOTDIR PHP 8'de undefined fatal — guvenli yol
        if (defined('ROOTDIR')) {
            $root = ROOTDIR;
        } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $root = $_SERVER['DOCUMENT_ROOT'];
        } else {
            // /coremio/modules/Addons/CodegaUpdater/ -> 4 seviye yukari
            $root = realpath($this->dir . '/../../../../');
        }
        return rtrim($root, '/') . '/' . trim($this->themePath(), '/');
    }

    private function backupDir() {
        $bdir = $this->dir . '/backups';
        if (!is_dir($bdir)) {
            @mkdir($bdir, 0755, true);
            @file_put_contents($bdir . '/.htaccess', "Order deny,allow\nDeny from all\n");
        }
        return $bdir;
    }

    private function tokenFile() {
        return $this->dir . '/.gh_token';
    }

    private function getTok() {
        $f = $this->tokenFile();
        if (file_exists($f)) {
            return trim(preg_replace('/[^a-zA-Z0-9_\-]/', '', file_get_contents($f)));
        }
        return '';
    }

    private function curl($url, $hdrs = [], $to = 30) {
        if (!function_exists('curl_init')) return ['code' => 0, 'body' => '', 'err' => 'cURL yok'];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $to,
            CURLOPT_HTTPHEADER     => $hdrs,
            CURLOPT_USERAGENT      => 'CodegaUpdater/3.0',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        return ['code' => $code, 'body' => $body ?: '', 'err' => $err];
    }

    private function ghAPI($path, $tok) {
        $r = $this->curl('https://api.github.com/repos/' . $this->ghRepo() . $path, [
            'Authorization: token ' . $tok,
            'Accept: application/vnd.github+json',
            'X-GitHub-Api-Version: 2022-11-28',
        ]);
        if ($r['code'] !== 200) return null;
        return json_decode($r['body'], true);
    }

    private function ghDownload($file, $tok) {
        // Once API Contents endpoint
        $d = $this->ghAPI('/contents/' . str_replace('%2F', '/', rawurlencode($file)) . '?ref=' . self::GH_BRANCH, $tok);
        if ($d && !empty($d['content'])) {
            return base64_decode(str_replace(["\n", "\r"], '', $d['content']));
        }
        // Fallback: raw.githubusercontent.com
        $r = $this->curl(
            'https://raw.githubusercontent.com/' . $this->ghRepo() . '/' . self::GH_BRANCH . '/' . $file,
            ['Authorization: token ' . $tok],
            60
        );
        if ($r['code'] === 200 && $r['body']) return $r['body'];
        return null;
    }

    private function isExcluded($path) {
        foreach (self::EXCLUDES as $ex) {
            if ($path === $ex) return true;
            if (str_ends_with($ex, '/') && str_starts_with($path, $ex)) return true;
        }
        return false;
    }

    private function repoTree($tok) {
        $tree = $this->ghAPI('/git/trees/' . self::GH_BRANCH . '?recursive=1', $tok);
        if (!$tree || empty($tree['tree'])) return [];
        $out = [];
        foreach ($tree['tree'] as $i) {
            if ($i['type'] !== 'blob') continue;
            if ($this->isExcluded($i['path'])) continue;
            $out[] = ['path' => $i['path'], 'sha' => $i['sha'], 'size' => $i['size'] ?? 0];
        }
        usort($out, fn($a, $b) => strcmp($a['path'], $b['path']));
        return $out;
    }

    private function blobSHA($content) {
        return sha1('blob ' . strlen($content) . "\0" . $content);
    }

    private function localVer() {
        $manifest = $this->themeRoot() . '/manifest.json';
        if (file_exists($manifest)) {
            $m = json_decode(file_get_contents($manifest), true);
            if (!empty($m['version'])) return $m['version'];
        }
        $version_json = $this->themeRoot() . '/version.json';
        if (file_exists($version_json)) {
            $m = json_decode(file_get_contents($version_json), true);
            if (!empty($m['version'])) return $m['version'];
        }
        return '?';
    }

    private function ghVer($tok) {
        $d = $this->ghAPI('/contents/manifest.json?ref=' . self::GH_BRANCH, $tok);
        if ($d && !empty($d['content'])) {
            $c = base64_decode(str_replace(["\n", "\r"], '', $d['content']));
            $m = json_decode($c, true);
            if (!empty($m['version'])) return $m['version'];
        }
        $d = $this->ghAPI('/contents/version.json?ref=' . self::GH_BRANCH, $tok);
        if ($d && !empty($d['content'])) {
            $c = base64_decode(str_replace(["\n", "\r"], '', $d['content']));
            $m = json_decode($c, true);
            if (!empty($m['version'])) return $m['version'];
        }
        return '?';
    }

    private function backup($label = '') {
        if (!class_exists('ZipArchive')) return ['ok' => false, 'error' => 'ZipArchive yok'];

        $tag  = $label ? '_' . preg_replace('/[^a-z0-9]/i', '', $label) : '';
        $name = 'bk_' . date('Ymd_His') . $tag . '_v' . $this->localVer() . '.zip';
        $path = $this->backupDir() . '/' . $name;

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) !== true) return ['ok' => false, 'error' => 'ZIP acilamadi'];

        $root = $this->themeRoot();
        if (!is_dir($root)) {
            $zip->close();
            return ['ok' => false, 'error' => 'Tema klasoru yok: ' . $root];
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $cnt = 0;
        foreach ($it as $f) {
            if ($f->isDir()) continue;
            $rel = str_replace($root . '/', '', $f->getPathname());
            if ($this->isExcluded($rel)) continue;
            if ($f->getSize() > 10 * 1024 * 1024) continue; // 10MB limit
            $zip->addFile($f->getPathname(), $rel);
            $cnt++;
            if ($cnt > 2000) break; // safety
        }
        $zip->close();

        // Eski yedekleri temizle
        $bks = glob($this->backupDir() . '/bk_*.zip') ?: [];
        usort($bks, fn($a, $b) => filemtime($a) <=> filemtime($b));
        foreach (array_slice($bks, 0, max(0, count($bks) - self::MAX_BK)) as $old) @unlink($old);

        return ['ok' => true, 'name' => $name, 'size' => filesize($path), 'files' => $cnt];
    }
}
