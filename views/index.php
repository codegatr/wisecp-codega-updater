<?php if(!defined("CORE_FOLDER")) return false; ?>
<?php
// Mevcut tema sürümü (manifest.json'dan oku)
$theme_path = isset($settings['theme_path']) ? $settings['theme_path'] : 'templates/website/Codega';
$manifest_path = ROOTDIR . '/' . trim($theme_path, '/') . '/manifest.json';
$current_version = '?';
$theme_name = 'Codega';
if(file_exists($manifest_path)) {
    $m = json_decode(file_get_contents($manifest_path), true);
    if($m) {
        $current_version = $m['version'] ?? '?';
        $theme_name = $m['name'] ?? 'Codega';
    }
}

$repo = isset($settings['github_repo']) ? $settings['github_repo'] : 'codegatr/wisecp-codega-theme';
$ajax_link = $link; // /yns/extra/CodegaUpdater
?>

<style>
.cu-shell {
    font-family: 'Plus Jakarta Sans', -apple-system, "Segoe UI", sans-serif;
    color: #0f172a;
    max-width: 1100px;
    margin: 0 auto;
    padding: 12px 0 32px;
}
.cu-shell *, .cu-shell *::before, .cu-shell *::after { box-sizing: border-box; }

.cu-hero {
    background: linear-gradient(135deg, #1A2332 0%, #2E3B4E 100%);
    border-radius: 18px;
    padding: 32px;
    color: #fff;
    margin-bottom: 20px;
    position: relative; overflow: hidden;
}
.cu-hero::before {
    content: ''; position: absolute;
    top: -50%; right: -10%;
    width: 360px; height: 360px;
    background: radial-gradient(circle, rgba(0,229,255,0.20), transparent 70%);
    pointer-events: none;
}
.cu-hero-head { display: flex; align-items: center; gap: 18px; position: relative; z-index: 1; }
.cu-hero-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #00D3E5, #00E5FF);
    border-radius: 14px;
    display: grid; place-items: center;
    color: #1A2332; font-size: 32px;
    box-shadow: 0 12px 32px rgba(0,211,229,0.40);
    flex-shrink: 0;
}
.cu-hero h1 {
    margin: 0 0 4px;
    font-size: 24px; font-weight: 800;
    color: #fff;
}
.cu-hero p { margin: 0; color: rgba(255,255,255,0.75); font-size: 14px; }

.cu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 768px) { .cu-grid { grid-template-columns: 1fr; } }

.cu-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(15,23,42,0.04);
}
.cu-card h3 {
    margin: 0 0 12px;
    font-size: 13px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: #0f172a;
    display: flex; align-items: center; gap: 8px;
}
.cu-card h3 i { color: #2E3B4E; font-size: 16px; }

.cu-info { list-style: none; padding: 0; margin: 0; }
.cu-info li {
    display: flex; justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 13px;
}
.cu-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cu-info li:first-child { padding-top: 0; }
.cu-info-l { color: #64748b; font-weight: 600; }
.cu-info-v { color: #0f172a; font-weight: 700; }
.cu-info-v code {
    background: #f1f5f9; padding: 2px 7px; border-radius: 5px;
    font-family: "JetBrains Mono", Consolas, monospace; font-size: 11px;
}

.cu-version-box {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc, #fff);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 14px;
}
.cu-version-box .label {
    font-size: 11px; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; font-weight: 700; margin-bottom: 6px;
}
.cu-version-box .value {
    font-size: 28px; font-weight: 800; color: #0f172a;
    font-family: "JetBrains Mono", Consolas, monospace;
}

.cu-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    border: 0; cursor: pointer;
    text-decoration: none;
    transition: transform 0.18s, box-shadow 0.18s;
    font-family: inherit;
}
.cu-btn-primary {
    background: linear-gradient(135deg, #1A2332, #485A75);
    color: #fff;
    box-shadow: 0 6px 18px rgba(46,59,78,0.22);
}
.cu-btn-primary:hover { transform: translateY(-1px); color: #fff; text-decoration: none; }
.cu-btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    box-shadow: 0 6px 18px rgba(16,185,129,0.22);
}
.cu-btn-success:hover { transform: translateY(-1px); color: #fff; text-decoration: none; }
.cu-btn[disabled] {
    opacity: 0.6; cursor: not-allowed; transform: none !important;
}

.cu-alert {
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 13px; line-height: 1.55;
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 14px;
}
.cu-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.cu-alert-info { background: #eff6ff; color: #1e3a8a; border: 1px solid #93c5fd; }
.cu-alert-info i { color: #2563eb; }
.cu-alert-success { background: #dcfce7; color: #14532d; border: 1px solid #86efac; }
.cu-alert-success i { color: #16a34a; }
.cu-alert-warn { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
.cu-alert-warn i { color: #d97706; }
.cu-alert-danger { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }
.cu-alert-danger i { color: #dc2626; }

.cu-changelog {
    background: #f8fafc;
    border-radius: 10px;
    padding: 14px 18px;
    list-style: none;
    font-size: 13px;
    line-height: 1.7;
    color: #334155;
    max-height: 280px;
    overflow-y: auto;
}
.cu-changelog li {
    padding: 4px 0;
    padding-left: 18px;
    position: relative;
}
.cu-changelog li::before {
    content: '✓';
    position: absolute; left: 0;
    color: #10b981; font-weight: 700;
}

.cu-loader {
    display: inline-block;
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: cu-spin 0.7s linear infinite;
}
@keyframes cu-spin { to { transform: rotate(360deg); } }

.cu-result-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-top: 16px;
    display: none;
}
.cu-result-box.show { display: block; }
</style>

<div class="cu-shell">

    <!-- Hero -->
    <div class="cu-hero">
        <div class="cu-hero-head">
            <div class="cu-hero-icon">⚡</div>
            <div>
                <h1>Codega Tema Guncelleme Merkezi</h1>
                <p>WiseCP'ye entegre Codega tema yonetim eklentisi v<?php echo htmlspecialchars($version, ENT_QUOTES); ?></p>
            </div>
        </div>
    </div>

    <!-- Tema Bilgileri -->
    <div class="cu-grid">
        <div class="cu-card">
            <h3><i class="fa fa-info-circle"></i> Tema Bilgileri</h3>
            <ul class="cu-info">
                <li><span class="cu-info-l">Tema Adi</span><span class="cu-info-v"><?php echo htmlspecialchars($theme_name, ENT_QUOTES); ?></span></li>
                <li><span class="cu-info-l">Mevcut Surum</span><span class="cu-info-v"><code>v<?php echo htmlspecialchars($current_version, ENT_QUOTES); ?></code></span></li>
                <li><span class="cu-info-l">GitHub Deposu</span><span class="cu-info-v"><code><?php echo htmlspecialchars($repo, ENT_QUOTES); ?></code></span></li>
                <li><span class="cu-info-l">Yol</span><span class="cu-info-v"><code><?php echo htmlspecialchars($theme_path, ENT_QUOTES); ?></code></span></li>
            </ul>
        </div>

        <div class="cu-card">
            <div class="cu-version-box">
                <div class="label">Yuklu Surum</div>
                <div class="value">v<?php echo htmlspecialchars($current_version, ENT_QUOTES); ?></div>
            </div>
            <button type="button" id="cu-check-btn" class="cu-btn cu-btn-primary" style="width:100%;">
                <i class="fa fa-sync"></i> Yeni Surum Kontrol Et
            </button>
        </div>
    </div>

    <!-- Sonuc kutusu -->
    <div id="cu-result" class="cu-result-box"></div>

    <!-- Bilgi -->
    <div class="cu-alert cu-alert-info">
        <i class="fa fa-info-circle"></i>
        <div>
            <strong>Nasil Calisir?</strong><br>
            Bu eklenti, Codega temasinin GitHub deposundaki en son release'i kontrol eder. Yeni surum bulunursa,
            ZIP olarak indirip otomatik olarak <code><?php echo htmlspecialchars($theme_path, ENT_QUOTES); ?></code>
            klasorune uygular. <code>config.php</code> ve <code>theme-config.php</code> dosyalari korunur.
        </div>
    </div>

    <div class="cu-alert cu-alert-warn">
        <i class="fa fa-exclamation-triangle"></i>
        <div>
            <strong>Yedekleme</strong><br>
            Guncelleme oncesi tema klasorunun manuel yedeklenmesi onerilir. Onemli ozel degisiklikler varsa kaybolabilir.
        </div>
    </div>

</div>

<script>
(function(){
    var btnCheck = document.getElementById('cu-check-btn');
    var resultBox = document.getElementById('cu-result');

    function setBusy(btn, busy, busyText) {
        if(busy) {
            btn.dataset.orig = btn.innerHTML;
            btn.innerHTML = '<span class="cu-loader"></span> ' + busyText;
            btn.disabled = true;
        } else {
            btn.innerHTML = btn.dataset.orig || btn.innerHTML;
            btn.disabled = false;
        }
    }

    function call(action, cb) {
        var url = '<?php echo $ajax_link; ?>?action=' + action + '&_t=' + Date.now();
        // postMessage ile cevap aliyoruz (iframe'siz cozum)
        // Bunun yerine fetch kullanip JSON parse edecegiz - daha temiz
        fetch(url, { credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(html){
                // Response'da postMessage scripti var, JSON'i extract edelim
                var match = html.match(/parent\.postMessage\((\{[\s\S]*?\}),\s*"\*"\)/);
                if(match) {
                    try { cb(JSON.parse(match[1])); }
                    catch(e) { cb({status:'error', message: 'JSON parse hatasi: ' + e.message}); }
                } else {
                    cb({status:'error', message:'Beklenmeyen cevap'});
                }
            })
            .catch(function(e){ cb({status:'error', message: e.message || 'Ag hatasi'}); });
    }

    function renderUpdate(data) {
        if(data.status !== 'successful') {
            resultBox.innerHTML = '<div class="cu-alert cu-alert-danger"><i class="fa fa-times-circle"></i><div>' +
                (data.message || 'Hata olustu') + '</div></div>';
            resultBox.classList.add('show');
            return;
        }

        var html = '';
        if(data.has_update) {
            html += '<div class="cu-alert cu-alert-success"><i class="fa fa-check-circle"></i><div>' +
                    '<strong>Yeni surum mevcut: v' + data.remote_version + '</strong></div></div>';
            html += '<div class="cu-grid"><div class="cu-card"><div class="cu-version-box"><div class="label">Mevcut</div><div class="value">v' +
                    data.current_version + '</div></div></div>';
            html += '<div class="cu-card"><div class="cu-version-box" style="background:linear-gradient(135deg,#dcfce7,#fff);border-color:#86efac;"><div class="label">Yeni</div><div class="value" style="color:#10b981;">v' +
                    data.remote_version + '</div></div></div></div>';

            if(data.changelog && data.changelog.length > 0) {
                html += '<div class="cu-card" style="margin-top:14px;"><h3><i class="fa fa-list"></i> Degisiklikler</h3><ul class="cu-changelog">';
                data.changelog.forEach(function(line){
                    html += '<li>' + line.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</li>';
                });
                html += '</ul></div>';
            }

            html += '<div style="text-align:center;margin-top:16px;">' +
                    '<button type="button" id="cu-apply-btn" class="cu-btn cu-btn-success">' +
                    '<i class="fa fa-download"></i> Guncellemeyi Uygula (v' + data.current_version + ' &rarr; v' + data.remote_version + ')' +
                    '</button></div>';
        } else {
            html += '<div class="cu-alert cu-alert-success"><i class="fa fa-check-circle"></i><div>' +
                    '<strong>Tema en guncel surumde!</strong><br>Yuklu surum: v' + data.current_version + '</div></div>';
        }
        resultBox.innerHTML = html;
        resultBox.classList.add('show');

        // Apply butonu varsa baglat
        var applyBtn = document.getElementById('cu-apply-btn');
        if(applyBtn) {
            applyBtn.addEventListener('click', function(){
                if(!confirm('Tema guncellemesi uygulanacak. Devam etmek istiyor musunuz?')) return;
                setBusy(applyBtn, true, 'Guncelleniyor...');
                call('apply', function(r){
                    if(r.status === 'successful') {
                        resultBox.innerHTML = '<div class="cu-alert cu-alert-success">' +
                            '<i class="fa fa-check-circle"></i><div><strong>' + (r.message || 'Guncelleme basarili!') +
                            '</strong><br>Sayfayi yenileyin.</div></div>';
                        setTimeout(function(){ window.location.reload(); }, 2000);
                    } else {
                        resultBox.innerHTML = '<div class="cu-alert cu-alert-danger">' +
                            '<i class="fa fa-times-circle"></i><div><strong>Guncelleme basarisiz</strong><br>' +
                            (r.message || 'Bilinmeyen hata') + '</div></div>';
                        setBusy(applyBtn, false);
                    }
                });
            });
        }
    }

    btnCheck.addEventListener('click', function(){
        setBusy(btnCheck, true, 'Kontrol ediliyor...');
        resultBox.classList.remove('show');
        call('check', function(data){
            setBusy(btnCheck, false);
            renderUpdate(data);
        });
    });
})();
</script>
