	
	var formatMoney = function(valor) {
		var valorAsNumber = Number(valor);
		return 'R$ ' + valorAsNumber.toMoney(2,',','.');
	};

	Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep) {
		var n = this,
		c = isNaN(decimals) ? 2 : Math.abs(decimals),
		d = decimal_sep || '.', 
		t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		sign = (n < 0) ? '-' : '',
		i = parseInt(n = Math.abs(n).toFixed(c)) + '', 
		j = ((j = i.length) > 3) ? j % 3 : 0; 
		return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 
	};

	var areaToParams = function (area, params) {
		params = params || {};
		$("#" + area).find('input, select').each(function(){
			params[ $(this).attr('name') ] = $(this).val();
		});
	};

	var startsWith = function(data, input) {
		return data.substring(0, input.length) === input;
	}

	var showCheckoutAlert = function( text ){
		swal({
			title: text,
			html: true,
			allowEscapeKey: false,
			showConfirmButton: false,
			customClass: 'checkout-alert'
		});
	};
	
	var showLoading = function(text) {
		showCheckoutAlert(`<img src="${public_url('assets/images/loading.gif')}" style="vertical-align: middle;width: 40px;margin-right: 15px;">&nbsp;&nbsp;Aguarde...`);

	};
	
	var showTransactionCode = function(code) {
		
		var html = ('<span class="success">'+code+'</span>');
		
		showCheckoutAlert(html);
		
	};
	
	var showWaitingPayment = function(paymentName) {
		
		var html = ('<span class="success">'+paymentName+'</span>');
		
		showCheckoutAlert(html);
		
	};
	
	var showPaymentErrors = function(errors) {
		
		if (typeof errors == 'object') {
			
			var errors = errors.error;
			var html = '<ul class="errors">';
			
			for (var i in errors) {
				var error = errors[i];
				html += ('<li>' + error.message + '</li>');
			}
			
			html += ('</ul>');
			
			$.colorbox({
				html: html,
				fixed: true
			});
			
		}
		
	};
	
	var showCardTokenErrors = function(errors) {
		
		if (typeof errors == 'object') {
			
			var html = '<ul class="errors">';
			
			for (i in errors) {
				html += ('<li>' + errors[i] + '</li>');
			}
			
			html += ('</ul>');
			
			$.colorbox({
				html: html,
				fixed: true
			});
			
		}
		
	};
	
	var hideMessages = function() {
		// $.colorbox.close();
		swal.close();
	};