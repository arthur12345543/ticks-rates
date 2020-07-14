$(function(){
	var currencies = ["AUD","BRL","GBP","BGN","CAD","CNY","HRK","CZK","DKK","EUR","HKD","HUF","ISK","IDR","INR","ILS","JPY","MYR","MXN","NZD","NOK","PHP","PLN","RON","RUB","SGD","ZAR","KRW","SEK","CHF","THB","TRY","USD"];
	var currencies_names = ["Australian Dollar","Brazilian Real","British Pound Sterline","Bulgarian Lev","Canadian Dollar","Chinese Yuan Renminbi","Croatian Kuna","Czech Koruna","Danish Krone","Euro","Hong Kong Dollar","Hungarian Forint","Icelandic Krona","Indonesian Rupiah","Indian Rupee","Israeli Shekel","Japanese Yen","Malaysian Ringgit","Mexican Peso","New Zealand Dollar","Norwegian Krone","Philippine Peso","Polish Zloty","Romanian Leu","Russian Rouble","Singapore Dollar","South African Rand","South Korean Won","Swedish Krona","Swiss Franc","Thai Baht","Turkish Lira","US Dollar"];
	$('.error, .success').delay(3200).fadeOut('fast');
	SmoothScroll({ stepSize: 60 });
	$('.append-generated-select').append(function() {
	    var elem = $('<select name="curr" class="curr">');
	    for(var i = 0; i < currencies.length; i++){
	         elem.append('<option value="' + currencies[i] + '">' + currencies_names[i] + '</option>');
	    }
	    elem.append('</select>');
	    return elem;
	});
	$('.f-rate button').on('click', function(event){
        event.preventDefault();
        var curr = $(".curr").val();
        var amount = $(".amount-usd").val();
        var csrf = $(".csrf").val();
			var request = $.ajax({
				type: "POST",
				url: "aj.php",
				data: { curr: curr, amount: amount, csrf: csrf }
			});
			request.done(function(msg){
				$(".response").html(msg);
			});
			request.fail(function(jqXHR, textStatus) {
				alert( "Request failed: " + textStatus );
			});
	});
});