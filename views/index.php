<?php
// Tema'nin mevcut surumu (manifest.json'dan)
$theme_path = isset($settings['theme_path']) ? $settings['theme_path'] : 'templates/website/Codega';
$manifest_path = ROOTDIR . '/' . trim($theme_path, '/') . '/manifest.json';
$current_version = '?';
$theme_name = 'Codega';
if(file_exists($manifest_path)) {
    $m = json_decode(file_get_contents($manifest_path), true);
    if($m) {
        $current_version = isset($m['version']) ? $m['version'] : '?';
        $theme_name = isset($m['name']) ? $m['name'] : 'Codega';
    }
}

$repo = isset($settings['github_repo']) ? $settings['github_repo'] : 'codegatr/wisecp-codega-theme';
?>

<style>
.cu-shell { font-family: -apple-system, "Segoe UI", sans-serif; max-width: 1100px; margin: 0 auto; padding: 12px 0 32px; box-sizing: border-box; }
.cu-shell *, .cu-shell *::before, .cu-shell *::after { box-sizing: border-box; }

.cu-hero {
    background: linear-gradient(135deg, #1A2332 0%, #2E3B4E 100%);
    border-radius: 14px; padding: 26px; color: #fff; margin-bottom: 18px;
    position: relative; overflow: hidden;
}
.cu-hero::before {
    content: ''; position: absolute; top: -50%; right: -10%;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(0,229,255,0.20), transparent 70%);
    pointer-events: none;
}
.cu-hero-head { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; flex-wrap: wrap; }
.cu-hero-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #00D3E5, #00E5FF);
    border-radius: 12px; display: grid; place-items: center;
    color: #1A2332; font-size: 28px; flex-shrink: 0;
    box-shadow: 0 8px 22px rgba(0,211,229,0.40);
}
.cu-hero h1 { margin: 0 0 4px; font-size: 22px; font-weight: 800; color: #fff; }
.cu-hero p { margin: 0; color: rgba(255,255,255,0.75); font-size: 13px; }

.cu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
@media (max-width: 768px) { .cu-grid { grid-template-columns: 1fr; } }

.cu-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 18px; box-shadow: 0 4px 14px rgba(15,23,42,0.04);
}
.cu-card h3 {
    margin: 0 0 12px; font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.5px; color: #0f172a;
}

.cu-info { list-style: none; padding: 0; margin: 0; }
.cu-info li {
    display: flex; justify-content: space-between; padding: 7px 0;
    border-bottom: 1px dashed #e2e8f0; font-size: 13px;
}
.cu-info li:last-child { border-bottom: 0; padding-bottom: 0; }
.cu-info li:first-child { padding-top: 0; }
.cu-info-l { color: #64748b; font-weight: 600; }
.cu-info-v { color: #0f172a; font-weight: 700; }
.cu-info-v code {
    background: #f1f5f9; padding: 2px 7px; border-radius: 5px;
    font-family: monospace; font-size: 11px;
}

.cu-version-box {
    text-align: center; padding: 18px;
    background: linear-gradient(135deg, #f8fafc, #fff);
    border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 12px;
}
.cu-version-box .label {
    font-size: 11px; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; font-weight: 700; margin-bottom: 4px;
}
.cu-version-box .value {
    font-size: 26px; font-weight: 800; color: #0f172a;
    font-family: monospace;
}

.cu-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 22px; border-radius: 10px;
    font-size: 13px; font-weight: 700; border: 0; cursor: pointer;
    text-decoration: none; transition: transform 0.18s, box-shadow 0.18s;
    font-family: inherit;
}
.cu-btn-primary {
    background: linear-gradient(135deg, #1A2332, #485A75);
    color: #fff; box-shadow: 0 6px 16px rgba(46,59,78,0.22);
}
.cu-btn-primary:hover { transform: translateY(-1px); color: #fff; text-decoration: none; }
.cu-btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff; box-shadow: 0 6px 16px rgba(16,185,129,0.22);
}
.cu-btn-success:hover { transform: translateY(-1px); color: #fff; text-decoration: none; }
.cu-btn[disabled] { opacity: 0.6; cursor: not-allowed; transform: none !important; }

.cu-alert {
    padding: 13px 16px; border-radius: 10px; font-size: 13px; line-height: 1.55;
    margin-bottom: 12px;
}
.cu-alert-info { background: #eff6ff; color: #1e3a8a; border: 1px solid #93c5fd; }
.cu-alert-success { background: #dcfce7; color: #14532d; border: 1px solid #86efac; }
.cu-alert-warn { background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }
.cu-alert-danger { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }

.cu-changelog {
    background: #f8fafc; border-radius: 10px; padding: 12px 16px;
    list-style: none; font-size: 13px; line-height: 1.7; color: #334155;
    max-height: 260px; overflow-y: auto; margin: 0;
}
.cu-changelog li { padding: 3px 0 3px 18px; position: relative; }
.cu-changelog li::before { content: '✓'; position: absolute; left: 0; color: #10b981; font-weight: 700; }

.cu-loader {
    display: inline-block; width: 13px; height: 13px;
    border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff;
    border-radius: 50%; animation: cu-spin 0.7s linear infinite;
}
@keyframes cu-spin { to { transform: rotate(360deg); } }

.cu-result-box {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 18px; margin-top: 14px; display: none;
}
.cu-result-box.show { display: block; }
</style>

<div class="cu-shell">

    <div class="cu-hero">
        <div class="cu-hero-head">
            <div class="cu-hero-icon">⚡</div>
            <div>
                <h1>Codega Tema Guncelleme Merkezi</h1>
                <p>WiseCP'ye entegre Codega tema yonetim eklentisi v<?php echo $version; ?></p>
            </div>
        </div>
    </div>

    <div class="cu-grid">
        <div class="cu-card">
            <h3>Tema Bilgileri</h3>
            <ul class="cu-info">
                <li><span class="cu-info-l">Tema Adi</span><span class="cu-info-v"><?php echo $theme_name; ?></span></li>
                <li><span class="cu-info-l">Mevcut Surum</span><span class="cu-info-v"><code>v<?php echo $current_version; ?></code></span></li>
                <li><span class="cu-info-l">GitHub Deposu</span><span class="cu-info-v"><code><?php echo $repo; ?></code></span></li>
                <li><span class="cu-info-l">Tema Yolu</span><span class="cu-info-v"><code><?php echo $theme_path; ?></code></span></li>
            </ul>
        </div>

        <div class="cu-card">
            <div class="cu-version-box">
                <div class="label">Yuklu Surum</div>
                <div class="value">v<?php echo $current_version; ?></div>
            </div>
            <button type="button" id="cu-check-btn" class="cu-btn cu-btn-primary" style="width:100%;">
                Yeni Surum Kontrol Et
            </button>
        </div>
    </div>

    <div id="cu-result" class="cu-result-box"></div>

    <div class="cu-alert cu-alert-info">
        <strong>Nasil Calisir?</strong> Bu eklenti Codega temasinin GitHub deposundaki en son release'i kontrol eder. Yeni surum bulunursa ZIP olarak indirip otomatik olarak <code><?php echo $theme_path; ?></code> klasorune uygular. <code>config.php</code> ve <code>theme-config.php</code> korunur.
    </div>

    <div class="cu-alert cu-alert-warn">
        <strong>Yedekleme:</strong> Guncelleme oncesi tema klasorunun manuel yedeklenmesi onerilir.
    </div>

</div>

<script>
(function(){
    var btnCheck = document.getElementById('cu-check-btn');
    var resultBox = document.getElementById('cu-result');
    if(!btnCheck) return;

    var ajaxBase = '<?php echo $link; ?>';

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
        // WiseCP route format: /yns/tools/addons/CodegaUpdater?action=check
        var url = ajaxBase + '?action=' + action + '&_t=' + Date.now();
        fetch(url, { credentials: 'same-origin' })
            .then(function(r){ return r.text(); })
            .then(function(html){
                var match = html.match(/<pre id="cdg-response"[^>]*>([^<]+)<\/pre>/);
                if(match) {
                    try {
                        var jsonStr = match[1].replace(/&quot;/g,'"').replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&#039;/g,"'");
                        cb(JSON.parse(jsonStr));
                    } catch(e) {
                        cb({status:'error', message: 'JSON parse: ' + e.message});
                    }
                } else {
                    cb({status:'error', message:'Beklenmeyen cevap. URL: ' + url});
                }
            })
            .catch(function(e){ cb({status:'error', message: 'Ag hatasi: ' + (e.message || e)}); });
    }

    function renderUpdate(data) {
        if(data.status !== 'successful') {
            resultBox.innerHTML = '<div class="cu-alert cu-alert-danger"><strong>Hata:</strong> ' +
                (data.message || 'Bilinmeyen hata') + '</div>';
            resultBox.classList.add('show');
            return;
        }

        var html = '';
        if(data.has_update) {
            html += '<div class="cu-alert cu-alert-success">' +
                    '<strong>Yeni surum mevcut: v' + data.remote_version + '</strong></div>';
            html += '<div class="cu-grid"><div class="cu-card"><div class="cu-version-box"><div class="label">Mevcut</div><div class="value">v' +
                    data.current_version + '</div></div></div>';
            html += '<div class="cu-card"><div class="cu-version-box" style="background:linear-gradient(135deg,#dcfce7,#fff);border-color:#86efac;"><div class="label">Yeni</div><div class="value" style="color:#10b981;">v' +
                    data.remote_version + '</div></div></div></div>';

            if(data.changelog && data.changelog.length > 0) {
                html += '<div class="cu-card" style="margin-top:14px;"><h3>Degisiklikler</h3><ul class="cu-changelog">';
                data.changelog.forEach(function(line){
                    html += '<li>' + line.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</li>';
                });
                html += '</ul></div>';
            }

            html += '<div style="text-align:center;margin-top:16px;">' +
                    '<button type="button" id="cu-apply-btn" class="cu-btn cu-btn-success">' +
                    'Guncellemeyi Uygula (v' + data.current_version + ' &rarr; v' + data.remote_version + ')' +
                    '</button></div>';
        } else {
            html += '<div class="cu-alert cu-alert-success">' +
                    '<strong>Tema en guncel surumde!</strong><br>Yuklu: v' + data.current_version + '</div>';
        }
        resultBox.innerHTML = html;
        resultBox.classList.add('show');

        var applyBtn = document.getElementById('cu-apply-btn');
        if(applyBtn) {
            applyBtn.addEventListener('click', function(){
                if(!confirm('Tema guncellemesi uygulanacak. Devam etmek istiyor musunuz?')) return;
                setBusy(applyBtn, true, 'Guncelleniyor...');
                call('apply', function(r){
                    if(r.status === 'successful') {
                        resultBox.innerHTML = '<div class="cu-alert cu-alert-success">' +
                            '<strong>' + (r.message || 'Guncelleme basarili!') + '</strong><br>Sayfa yeniden yuklenecek.</div>';
                        setTimeout(function(){ window.location.reload(); }, 2000);
                    } else {
                        resultBox.innerHTML = '<div class="cu-alert cu-alert-danger">' +
                            '<strong>Guncelleme basarisiz:</strong><br>' + (r.message || 'Bilinmeyen hata') + '</div>';
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
