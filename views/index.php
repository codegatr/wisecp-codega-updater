<p>Codega Tema Guncelleme Merkezi</p>
<p>Yuklu Eklenti Surumu: <strong><?php echo $version; ?></strong></p>
<p>Eklenti Adi: <strong><?php echo $name; ?></strong></p>

<hr>

<p><strong>Tema Bilgileri</strong></p>
<p>
    <?php
        $theme_path = isset($settings['theme_path']) ? $settings['theme_path'] : 'templates/website/Codega';
        $manifest_path = ROOTDIR . '/' . trim($theme_path, '/') . '/manifest.json';
        $current_version = '?';
        if(file_exists($manifest_path)) {
            $m = json_decode(file_get_contents($manifest_path), true);
            if($m && isset($m['version'])) $current_version = $m['version'];
        }
        $repo = isset($settings['github_repo']) ? $settings['github_repo'] : 'codegatr/wisecp-codega-theme';
    ?>
    Mevcut Tema Surumu: <strong>v<?php echo $current_version; ?></strong><br>
    GitHub Deposu: <strong><?php echo $repo; ?></strong><br>
    Tema Yolu: <strong><?php echo $theme_path; ?></strong><br>
    Manifest: <strong><?php echo $manifest_path; ?></strong>
</p>

<hr>

<p><strong>Yapilandirma</strong></p>
<p>
    <?php
        if($fields){
            foreach($fields AS $field){
                ?>
                <?php echo $field['name']; ?>: <strong><?php echo isset($field["value"]) ? $field["value"] : ''; ?></strong><br>
                <?php
            }
        }
    ?>
</p>

<hr>

<p>
    <a class="lbtn" href="<?php echo $link; ?>?action=check" target="_blank">Yeni Surum Kontrol Et (Ham JSON)</a>
    &nbsp;
    <a class="lbtn" href="<?php echo $link; ?>?action=apply" target="_blank" onclick="return confirm('Tema guncellemesi uygulanacak. Devam ediyor musunuz?');">Guncellemeyi Uygula</a>
</p>

<p style="font-size:12px;color:#888;margin-top:20px;">
    Not: Yukaridaki linkler yeni sekmede ham JSON cevap doner.
    Daha sonra guzel arayuze gecirilecek.
</p>
