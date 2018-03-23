jQuery( document ).ready(function($){
	$('#devialia_intersticial_container__header-close').click(function(){
		$('.devialia_intersticial').hide();
	});

	//Cookies
	if (Cookies.get('devialia_intersticial_cookie_' + location.hostname) == 1)
		$('#devialia_intersticial').hide();

	$('#devialia_intersticial_container__header-close').click(function(){
		Cookies.set('devialia_intersticial_cookie_' + location.hostname, 1, {expires: 30});
		$('#devialia_intersticial').hide();
	});
});