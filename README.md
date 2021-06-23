# Wp-Woo-commerce-Altin-kuru-hesaplama-eklentisi
Eklenti - woocommence  altın kur hesabi 








Api https://truncgil.com.tr/ json dan kurları online olarak çekilmekte sonra işlenmektedir. Siz yinede kurları kontrol etmizde fayda vardır.

Kurulum Rehberi : Wordpress yükledikten sonra "woocommerce > Altın kuru Düzenleme" Diyorsunuz.
sonrasında kaydet kur güncelle diyorsunuz. 
Alta size listeleme yapıyor.
Ürünler sayfasından gram ve kanat bilgilerini giriyorsunuz ve gerisini eklenti hallediyor. 


Eğer alım satım kullanıyorsanız:

 656. ekleyin :
```php <!--alış Satış Farkı Alış kapalı ise kaldırmanız daha mantıklı--><!--
			<p class="form-field">
				<label for="spread"><?php  esc_html_e( 'Alış Satış Yüzdesi', 'woocommence-altin-fiyati' )?></label>
				<input type="text" class="short" id="spread" name="spread" value="<?php echo $spread; ?>"  />
			</p>--> 
```
Güncelleme gerektiğinde gelecektir. 

İletişim için : habib@epist.io

