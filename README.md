# Codega Updater - WiseCP Addon

WiseCP yönetici panelinden Codega temasını tek tıkla güncellemenizi sağlayan native eklenti.

## Özellikler

- ✅ WiseCP admin panelinin "Eklentiler" bölümünden erişim
- ✅ GitHub Release üzerinden versiyon kontrolü
- ✅ Otomatik ZIP indirme ve uygulama
- ✅ `config.php` ve `theme-config.php` korunur
- ✅ Changelog görüntüleme
- ✅ Tek tıkla güncelleme

## Kurulum

1. Bu eklenti klasörünü WiseCP kurulumunda şu yola atın:
   ```
   coremio/modules/Addons/CodegaUpdater/
   ```

2. WiseCP admin paneline girin: **Yönetim → Tools → Eklentiler**
3. "Codega Updater" eklentisini bulun ve **Etkinleştir** butonuna tıklayın
4. **Yapılandır** ile GitHub repo ve tema yolu ayarlarını kontrol edin
5. Eklenti sayfasından "Yeni Sürüm Kontrol Et" ile başlayın

## Ayarlar

- **GitHub Deposu**: `codegatr/wisecp-codega-theme` (varsayılan)
- **Tema Yolu**: `templates/website/Codega` (varsayılan)
- **Otomatik Kontrol**: Aktif
- **Kontrol Aralığı**: 86400 saniye (24 saat)

## Geliştirici

CODEGA - https://codega.com.tr

## Lisans

MIT
