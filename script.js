$(document).ready(function(){
	$("#captcha_img").attr("src","libs/captcha.php?"+Math.random());

	//$(this).pngFix();//png IE6 Fix
	
	if(document.getElementById('birthday')){
		$('#birthday').datepick({dateFormat:'yyyy-mm-dd',defaultDate:new Date(1980,1-1,1)});//生日格式化
	}
	$('.date').each(function(){ // date class 套用 datepick
		$(this).datepick({dateFormat:'yyyy-mm-dd'});
	});
	
	if(document.getElementById('city') && document.getElementById('area')){ //如果有 city 跟 area 選項套用連動
		var options={
			preselectFirst:'',
			preselectSecond:1,
			emptyOption:true,
			emptyValue:'請選擇',
			emptyKey:''
		};
		$.getJSON('libs/city_aray_json.php',function(selectOptions){
			$('#city').doubleSelect('area',selectOptions,options);
		});
	}
	
	$('.required').each(function(){
		if($(this).attr('class')!='validate[required]'){
			$(this).removeClass();
			if($(this).attr('type')!='radio'){
				$(this).addClass('validate[required]');
			}
			else{
				$(this).addClass('validate[required] radio');
			}
		}
	});
	
	$('.number').each(function(){
		if($(this).attr('class')!='validate[required,custom[onlyNumberSp]]'){
			$(this).removeClass();
			$(this).addClass('validate[required,custom[onlyNumberSp]]');
		}
	});
	
	$('.onlynumber').each(function(){
		if($(this).attr('class')!='validate[custom[onlyNumberSp]]'){
			$(this).removeClass();
			$(this).addClass('validate[custom[onlyNumberSp]]');
		}
	});
	
	$('.email').each(function(){
		if($(this).attr('class')!='validate[required,custom[email]]'){
			$(this).removeClass();
			$(this).addClass('validate[required,custom[email]]');
		}
	});
	
	$('.url').each(function(){
		if($(this).attr('class')!='validate[required,custom[url]]'){
			$(this).removeClass();
			$(this).addClass('validate[required,custom[url]]');
		}
	});
	
	/* $('input').each(function(){
		if($(this).attr('type')=='checkbox'){
			if($(this).attr('class').match('min')){
				var min_check=$.trim($(this).attr('class').substring($(this).attr('class').indexOf('[')+1,$(this).attr('class').indexOf(']')));
				$(this).removeClass();
				$(this).addClass('validate[minCheckbox['+min_check+']]');
			}
		}
	}); */
	
	if(document.getElementById('form')){ //如果有表單 id = form 套用 validationEngine
		$('#form').validationEngine();
	}
	
});