# Kuveytpos
### WooCommerce için Kuveyttürk Sanal Pos Ödeme Yöntemi

---
> ⚠️ _Önemli_
> =
> Bu eklentinin kodları ve kullanımı ücretsizdir. Kurulum ve teknik destek istiyorsanız ücretlidir.
---

### Bu Eklenti Ne İşe Yarar?
Wordpress üzerine kurulan WooCommerce e-ticaret sistemi için, Kuveyttürk bankası tarafından sağlanan sanal POS sistemi FreePOS'u kullanan basit ve ücretsiz bir ödeme yöntemi eklemenizi sağlar. Böylece siteniz üzerindeki ödeme sayfasında kredi kartı ile ödeme alabilirsiniz.

### Öncelikle Bilinmesi Gerekenler
Kuveyttürk FreePos sistemi Türk Lirası kabul eder. Bu yüzden WooCommerce'in ayarlarından para birimi olarak TL seçilmesi gerekir. Farklı bir para biriminde çalışan siteniz varsa, ödeme alınırken, toplam tutarın TL cinsine çevrilmesi gereklidir.

### Nasıl Kullanılır?
İndirdiğiniz **kuveytpos** klasörünü web sitenizdeki Wordpress'in plugins klasörüne atın.

Eklentiyi aktif ettikten sonra, WooCommerce ayarlarındaki Ödeme sekmesinde "Kuveyttürk Sanal POS Sistemi" adında yeni bir ödeme yöntemi çıkacak. Bu yöntemi aktif edip yanında "Yönet" butonuna tıklayın.

Açılan sayfada; müşteri numarası, mağaza numarası, bankanın sisteminden oluşturduğunuz API kullanıcı adı ve şifresini girin, sonra ayarları kaydedin. 

Ödeme yöntemini aktifleştirdikten sonra sepet ödeme sayfasında "Kredi kartı ile ödeme" seçeneği çıkacaktır.

### Değişim Günlüğü
v1.2 Free
* Banka dönüş sistemindeki sorunlar giderildi.
* Sipariş alındıktan sonraki banka yönlendirmesinin bazı sunucularda problem çıkarttığını gözlemledim. Böyle bir hata yaşarsanız düzeltmek için banka yönlendirmesi otomatik olarak gerçekleşmiyorsa "Ayarlar" sayfasından "Banka Yönlendirmesini Zorla" seçeceğini aktifleştirebilirsiniz.

v1.1
* İlk versiyondaki banka hata dönüşündeki sorun düzeltildi.
* Hata durumunda dönülmesi istenen sayfayı seçebiliyorsunuz.

v1.0
* İlk versiyon

### Sıkça Sorulan Sorular

##### TL Harici Bir Para Birimi Kullanıyorum, ne yapmalıyım?
Kuveyt Türk FreePOS, sadece Türk Lirası ile ödeme almanızı sağlar. TL harici bir döviz cinsinden satış yapıyorsanız ödeme sayfasında TL cinsinden göstermeniz ve POS sistemine TL cinsinden göndermeniz gerekiyor. Bunun için eklenti dosyalarında değişiklik yapmanız gerekebilir veya ödeme para birimini değiştiren bir eklenti kurup bu eklentiyi ona göre ayarlamanız gerekmektedir. Farklı bir  döviz cinsindeen ödeme alırken bu eklentiyi kullanırsanız, daha az miktarda ödeme alacağınızı unutmayın.

##### Bu eklentiyi kuramadım, yardımcı olur musun?
Bu eklentideki ücretsiz paylaşılan kodlar; 16.05.2020 tarihi itibariyle sıfır bir Wordpress + Woocommerce kurulumunda test edilmiş ve çalışmaktadır. İndirip kendi sitenizde ücretsiz olarak kullanabilirsiniz. Eğer bu eklentiyi çalıştırmakta problem yaşıyorsanız kurulumu için ücretli destek verebilirim. Destek almak için Twitter'dan yada e-posta ile benimle iletişime geçebilirsiniz.

### Resimler
![Kuveytpos](https://raw.githubusercontent.com/muratcesmecioglu/depo/master/Kuveytpos.png)
![Kuveytpos](https://raw.githubusercontent.com/muratcesmecioglu/depo/master/Kuveytturk2.png)
