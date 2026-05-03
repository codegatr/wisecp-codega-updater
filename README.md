# Codega Smart Update Manager v3.0.0

WiseCP eklentisi olarak Codega temasinin GitHub'dan akilli sekilde guncellenmesini saglar.

## Ozellikler

- **5 sekmeli UI**: Genel Durum / Dosyalar / Commits / Yedekler / Manuel ZIP / Ayarlar
- **Git Blob SHA karsilastirmasi** - dosya bazli, sha1('blob ' + len + "\0" + content)
- **Smart Sync** - sadece degismis dosyalari indirir
- **Force Sync** - tum dosyalari yeniden indirir
- **Tekil dosya guncelleme** - tek tikla bir dosya senkronizasyonu
- **Otomatik yedekleme** - max 10 yedek, ZIP olarak saklanir
- **Yedeklerden geri donus** (restore)
- **Manuel ZIP yukleme** - GitHub'a erisim olmasa bile
- **Commit history** - son 20 commit goruntuleme
- **GitHub token yonetimi** - .gh_token dosyasi (chmod 0600)

## Kurulum

1. Bu klasoru `coremio/modules/Addons/CodegaUpdater/` yoluna kopyalayin
2. WiseCP admin paneli > Eklentiler > Codega Updater > Aktif yapin
3. Ayarlar sekmesinden GitHub Personal Access Token girin (`repo` kapsami yeterli)
4. Genel Durum sekmesinde Smart Sync veya Force Sync ile guncelleyin

## Mimari

ERP'nin guncelleme_github.php (Smart Update Manager v5) sisteminden WiseCP eklenti
formatina port edildi.

- `extends AddonModule` (WiseCP standart)
- `adminArea()` - ana sayfa
- `ajaxDispatch()` - 11 farkli AJAX action
- `set_time_limit(0)` - WiseCP execution_time bypass

## Sekmeler

### Genel Durum
- Yerel/uzak surum karsilastirmasi
- Dosya istatistikleri (ok/diff/missing)
- Smart Sync ve Force Sync butonlari

### Dosyalar
- Tum dosyalarin durumu (filtreli + arama)
- Tekil dosya indirme

### Commits
- Son 20 commit, SHA + mesaj + yazar + tarih

### Yedekler
- Otomatik alinmis yedekler listesi
- Restore (geri yukleme)
- Silme

### Manuel ZIP
- GitHub'a erisim olmadiginda yedek yontem
- Manifest.json kontrolu, kok ya da alt klasor destegi

### Ayarlar
- GitHub token yonetimi (kaydet + test)
- Yapilandirma ozeti
