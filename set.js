function Search(){   //arama yapan js kod bloğu
	var text = document.getElementsByClassName("search-form_input")[0];  //arama kısmını çekiyoruz
	if(text.value.trim() == ""){  //eğer boşsa geri dönüyor
		return;
	}
	var panel2 = document.getElementsByClassName("center767")[0];
	if(panel2.innerHTML.indexOf("Bulunan Kitaplar") == -1)  //bulunan kitaplar yazısı yoksa aşağıya onu basıyoruz
	  panel2.innerHTML = "<h2 class='text-center'>Bulunan Kitaplar</h2>" + panel2.innerHTML;
	var panel = document.getElementsByClassName("box-product")[0];
	panel.style.textAlign = "center";
	panel.innerHTML = "<img src='Spin.svg' height='100' width='100' />";  //yükleniyor svg dosyamız
    panel.focus();
	$.ajax({  //ajax ile books.php dosyasına post olarak kitap ismini yolluyoruz
        url: "books.php",
        type: "post",
        data: {txt:text.value} ,
        success: function (response) {  //eğer hata oluşmassa php echo yaptığımız string dosyası response olarak geliyor bizde bunu html içine basıyoruz ve  kitaplar çıkıyor 
			panel.style = "";
            panel.innerHTML = response;			
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        } });
}
function OpenInNewTabWinBrowser(url) {
  var win = window.open(url, '_blank');
  win.focus();}
