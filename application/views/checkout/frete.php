<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body>
	<input type="text" name="cepDestino" value="89.182-000">
	<button type="button" class="calcular">Calcular</button>

	<ul class="retorno-list"></ul>

	<script type="text/javascript">

		$('button.calcular').on('click',function(){
			$.post('<?php echo base_url('checkout/calculafrete'); ?>', {cepDestino: $('input[name="cepDestino"]').val()}, function( response ){
				response = JSON.parse( response );
				let $resplist = '';

				for(let data of response){
					for(let val in data){
						if( val == 'Codigo' ){
							if( data[val] == '40010' ){
								var freteService = 'Sedex';
							}else{
								var freteService = 'PAC';
							}
						}

						if( val == 'ValorSemAdicionais' ){
							var freteValor = data[val];
						}
					}
					$resplist += `<li><b>${freteService}:</b> R$ ${freteValor}</li>`;
				}

				$('.retorno-list').html($resplist);
			});
		});
		
	</script>
</body>
</html>