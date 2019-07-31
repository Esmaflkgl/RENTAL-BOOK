<?php
class Kitap{  //Kitapları rahat tutmak için Kitap adlı bir class oluşturduk
	function ekle($n,$yaz,$yay,$i,$f,$l,$t,$ef){
		$this->name = $n;
		$this->yazar = $yaz;
		$this->yayıncı = $yay;
		$this->img = $i;
		$this->fiyat = $f;
		$this->detaylink = $l;
		$this->tur = $t;
		$this->eskifiyat = $ef;
	}		
}
//Kitap isimlerinden silinmesi gereken özel karakterler
$ozel = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c","-"," ",":","(",")","!","&",";","'");
include('simple_html_dom.php');//Veri çekmek için kullandığımız açık kaynaklı api php dosyası
$kitapismi = $_POST["txt"];//Ajax ile gönderilen POST kitap ismimiz
$kylist = getKitapYurduİtems($kitapismi,1);//Kitapyurdu Bilgileri çekiyoruz!
$idfxlist = getİdefixİtems($kitapismi,1);//İdefix Bilgileri çekiyoruz!
$drlist = getDandRİtems($kitapismi,1);//D&R Bilgileri çekiyoruz!
$kitapisimleri = array();
$ilk = array();
$son = array();
foreach($kylist as $kitap){	//İlk karşılaştırma kitapyurdundaki kitapları idefixtekilerle karşılaştırıyoruz
	$yazar = strtolower(str_replace($ozel,"", html_entity_decode($kitap->yazar))); //Yazar ve Kitap isimlerindeki özel karakterleri temizliyoruz!
	$name =  strtolower(str_replace($ozel,"", html_entity_decode($kitap->name))); //Yazar ve Kitap isimlerindeki özel karakterleri temizliyoruz!
	foreach($idfxlist as $kitap2){ //İdefixteki kitaplar
		$yazar2 = strtolower(str_replace($ozel,"", html_entity_decode($kitap2->yazar))); //Yazar ve Kitap isimlerindeki özel karakterleri temizliyoruz!
		$name2 = strtolower(str_replace($ozel,"", html_entity_decode($kitap2->name))); //Yazar ve Kitap isimlerindeki özel karakterleri temizliyoruz!
		if(strcasecmp($yazar,$yazar2) == 0){  //Yazar isimlerini karşılaştırıyoruz 0 demek tam eşleşme sağlanmış demektir!
          $ekle = false;			
           $cmp = strcasecmp($name,$name2);	//Kitap isimlerine bakıyoruz	   
		   if($cmp > 0){   //eğer fazladan kelime varsa bazılarında 'dir' gibi ekler olabiliyor onları temizleyip tekrardan bakıyoruz eğer eşleşme sağlandıysa ekle true oluyor!
			   $index = strlen($name) - $cmp;			   
			   $name = substr($name,0,$index);
			   if(strcasecmp($name,$name2) == 0)
				   $ekle = true;
		   }else if($cmp < 0 ){
			   
		   }else{
			   $ekle = true;
		   }
	
		   if($ekle){	//ekle true gelirse &bir dinamik dizisine kitapları ekliyoruz
             //echo $kitap->fiyat ."  " . $kitap2->fiyat;	 
              $birfiyat = floatval(str_replace(",",".",$kitap->fiyat));
              $ikifiyat = floatval(str_replace(",",".",$kitap2->fiyat));			  
			   if($birfiyat >= $ikifiyat){	//fiyat karşılaştırması
			   //echo "İdefix";
				   $kitapisimleri[] = $name;
				   $ilk[] = $kitap2;
			   }else{
				   //echo "kitayurdu";
				   $kitapisimleri[] = $name;
				   $ilk[] = $kitap;
			   }
			   //echo "</br>";
		   }
			 
		}
	}
}
foreach($ilk as $kitap){  //yukardaki ile aynı mantık ilk bulunan kitaparla d&r kitaplarını karşılaştırıyoruz bu sefer!	
	$yazar = strtolower(str_replace($ozel,"", html_entity_decode($kitap->yazar)));
	$name =  strtolower(str_replace($ozel,"", html_entity_decode($kitap->name)));
	foreach($drlist as $kitap2){
		$yazar2 = strtolower(str_replace($ozel,"", html_entity_decode($kitap2->yazar)));
		$name2 = strtolower(str_replace($ozel,"", html_entity_decode($kitap2->name)));
		if(strcasecmp($yazar,$yazar2) == 0){
          $ekle = false;			
           $cmp = strcasecmp($name,$name2);		   
		   if($cmp > 0){
			   $index = strlen($name) - $cmp;			   
			   $name = substr($name,0,$index);
			   if(strcasecmp($name,$name2) == 0)
				   $ekle = true;
		   }else if($cmp < 0 ){
			   
		   }else{
			   $ekle = true;
		   }
		  /* if(array_search($name,$kitapisimleri)){
			   $ekle = true;
		   }else{
			   $ekle = false;
		   }*/
		   if($ekle){	//Bir fiyat kaşlılaştırmasında son dizisine atıyoruz yani en son çıkan kitaplar oluyor
             //echo $kitap->fiyat ."  " . $kitap2->fiyat;	 
              $birfiyat = floatval(str_replace(",",".",$kitap->fiyat));
              $ikifiyat = floatval(str_replace(",",".",$kitap2->fiyat));			  
			   if($birfiyat > $ikifiyat){	
			   //echo "dandr";
				   $kitapisimleri[] = $name;
				   $son[] = $kitap2;
			   }else{
				   //echo "idefix";
				   $kitapisimleri[] = $name;
				   $son[] = $kitap;
			   }
			   //echo "</br>";
		   }
		}
	}
}
$all = "";  //HTML kitap tasarımını tutmak için string oluşturduk!
if(count($son) != 0){  //eğer kitap varsa
foreach($son as $kitap){  //son dizisindeki kitap bilgilerini çekip aşağıda $all strinine ekliyoruz
	$yazar = $kitap->yazar;
	$name = $kitap->name;
	$eskifiyat = $kitap->eskifiyat;
	$fiyat = $kitap->fiyat;
	$img = $kitap->img;
	$link = $kitap->detaylink;
	$tur = $kitap->tur;
	$yayıncı = $kitap->yayıncı;
	$fiyatecho  = "";
	$turimg = "";
	if($tur == "ky"){
		$turimg = "<span class='sticker_top'><img alt='' src='a_data/kitapyurdu.png'></span>";
	}else if($tur == "dr"){
		$turimg = "<span class='sticker_top'><img alt='' src='a_data/dandr.png'></span>";
	}else if($tur == "ix"){
		$turimg = "<span class='sticker_top'><img alt='' src='a_data/idefix.png'></span>";
	}
	if($eskifiyat != -1){
		$fiyatecho = "<div class='price'>" . 
		"<span class='price-old'>$eskifiyat</span>" .
														   "<span class='price-new'>$fiyat</span>"
											 														        ."</div>";													
														}else{
			$fiyatecho =												"<div class='price'>" . 
														   "<span class='price-new'>$fiyat</span></div>";
														}													
												
	$all = $all . "<li class='' style='width:250px;'><div>
				
				<div class='image2'>
					<div style='background-color:white;padding-top:15px;'><img src='$img' alt=''></div>
					" . $turimg .
					"
              	</div>			
					
				<div class='inner'>				
					<div class='' style='min-height: 86px;height:auto;'>						
						<div class='name' style='font-weight:bold;color:gray;'>$name</div>						
						<div class='description'>Yazar:$yazar</div>
						<div class='description' style='margin-top:-15px;'>Yayıncı:$yayıncı</div>											
					</div>					
					
									
				<div class='cart-button'>
				" .								
							$fiyatecho
	
						. "<div class='cart'><a href='$link' title='' data-id='28;' class='button addToCart tooltip-1' data-original-title='Add to cart'><i class='fa fa-info'></i><span></span></a></div>
						
						<span class='clear'></span>
				</div>
				
				<div class='clear'>
				</div>
				</div>
				<div class='clear'></div>
			</div></li>";
}
}else{   //eğer kitap yoksa boş gider
	echo "<div class='' style='text-align:center;font-size:20px;'><strong>Kitap Yok</strong></div>";
}
echo $all;
function getDandRİtems($query,$page){  //D&R kitaplarını çekiyoruz
	$query = str_replace(" ","%20",$query);
	$list = array();
	$dr = "http://www.dr.com.tr/search?q=".$query."&cat=0%2C10001&parentId=10001";
    $html = file_get_html($dr);
if($html->find("div[id='container']") != null){  //eğer kitap varsa o panel boş değilse
	$container = $html->find("div[id='container']",0);
	foreach($container->find("div[class='list-cell']") as $item){  //kitap bilgilerini o panelden çekip aşağıya kitap classına doldurup $list dinamik listesine ekliyoruz
		$img = $item->find("img",0)->src;
	$name = $item->find("h3",0)->plaintext;
	$yazar = $item->find("a[class='who']",0)->plaintext;
	$yayıncı = "";
	if($item->find("a[class='mb10']") == null){
		$yayıncı = $item->find("span[class='mb10']",0)->plaintext;
	}else{
		$yayıncı = $item->find("a[class='who']",0)->plaintext;
	}
	$fiyatlar = $item->find("div[class='prices'] span");
	$eskifiyat = $yenifiyat = $indirim = -1;
    if(count($fiyatlar) == 3){
		$eskifiyat = $fiyatlar[0]->plaintext;
		$yenifiyat = $fiyatlar[1]->plaintext;
		$indirim = $fiyatlar[2]->plaintext;
	}	else{
		$yenifiyat = $fiyatlar[0]->plaintext;
	}
	$yenifiyat = str_replace("TL","",$yenifiyat);
	$eskifiyat = str_replace("TL","",$eskifiyat);
	$detaylink = "http://www.dr.com.tr" . $item->find("a[class='btn white']",0)->href;	
	$kitap = new Kitap;
	$kitap->ekle($name,$yazar,$yayıncı,$img,$yenifiyat,$detaylink,"dr",$eskifiyat);
	$list[] = $kitap;
}
}
 return $list;
	
}
function getKitapYurduİtems($query,$page){  //Kitap yurdu kitaplarını çekiyoruz
	$query = str_replace(" ","%20",$query);
	$list = array();
	$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://www.kitapyurdu.com/index.php?route=product/search&sort=purchased&order=DESC&filter_name=".$query."&filter_product_type=1&limit=100&fuzzy=0"); // target
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  //Kitapyurdunu curl ile indiriyoruz çünkü yukardaki api ile html indirmemize izin vermiyor
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$result = curl_exec($ch);
curl_close($ch);
$html = str_get_html($result);  //html indirdik string olarak tutuyoruz
if($html->find("div[id='product-table']") != null){  //kitap varsa panel içi boş değilse
$table = $html->find("div[id='product-table']")[0];
foreach($table->find("div[itemtype='http://schema.org/Book']") as $item){   //kitapları almaya başlıyoruz ve aşağıdaki bilgileri o panelden çekiyoruz
	if($item->find("div[class='price']",0) != null){
	$img = $item->find("img",0)->src;
	$spans = $item->find("span[itemprop='name']");
	$name = $spans[0]->plaintext;
	$yayıncı = $spans[1]->plaintext;
	$yazar = $item->find("div[class='author']",0)-> plaintext;
	$yazar = str_replace("Yazar : ","",$yazar);
	$yazar = str_replace(" , ","",$yazar);
	$yazar = str_replace(" , , "," , ",$yazar);
	$detaylink = $item->find("a[itemprop='url']",0)->href;
	$fiyat = -1;
	$eskifiyat = -1;
	if($item->find("div[class='price-new']",0) != null){
		$fiyat = $item->find("div[class='price-new'] span[class='value']",0)->plaintext;
		$eskifiyat = $item->find("div[class='price-old'] span[class='value']",0)->plaintext;
	}else{
		$fiyat = $item->find("span[class='price-old'] span[class='value']",0)->plaintext;
	}
	$kitap = new Kitap;
	$kitap->ekle($name,$yazar,$yayıncı,$img,$fiyat,$detaylink,"ky",$eskifiyat);
	$list[] = $kitap;
   }
  }
 }
 return $list;
}
function getİdefixİtems($query,$page){  //İdefix kitaplarını çekiyoruz d&r ile aynı kod bloğuna sahip çünkü tasarımları aynı
	$query = str_replace(" ","%20",$query);
	$list = array();
	$idefix = "http://www.idefix.com/search?q=".$query."&cat=0%2C11693&parentId=11693#/page=1/sort=relevance,desc/categoryid=0,11693/parentId=11693/lg=undefined/price=-1,-1/ldir=h";
$html = file_get_html($idefix);
if($html->find("div[id='container']") != null){
$container = $html->find("div[id='container']");
$container = $container[0];
foreach($container->find("div[class='list-cell']") as $item){
	$img = $item->find("img",0)->src;	
	$name = $item->find("h3",0)->plaintext;
	$yazareyayın = $item->find("a[class='who']");
	$yazar = $yazareyayın[0]->plaintext;
	$yayıncı = $yazareyayın[1]->plaintext;
	$fiyatlar = $item->find("div[class='prices'] span");
	$eskifiyat = $yenifiyat = $indirim = -1;
    if(count($fiyatlar) == 3){
		$eskifiyat = $fiyatlar[0]->plaintext;
		$yenifiyat = $fiyatlar[1]->plaintext;
		$indirim = $fiyatlar[2]->plaintext;
	}	else{
		$yenifiyat = $fiyatlar[0]->plaintext;
	}	
	$yenifiyat = str_replace("TL","",$yenifiyat);
	$eskifiyat = str_replace("TL","",$eskifiyat);
	$detaylink = "http://www.idefix.com/" . $item->find("a[class='btn white-border']",0)->href;
	$kitap = new Kitap;
	$kitap->ekle($name,$yazar,$yayıncı,$img,$yenifiyat,$detaylink,"ix",$eskifiyat);
	$list[] = $kitap;
}
}
 return $list;
} ?>
