function jq(elem) {
	return document.getElementById(elem);
}

function validateForm() {
	var period = jq('period').value;
	var commission = jq('commission').value;
	if(period === '' || period === undefined) {
		alert("Please select period of report!");
		return;
	}

	if(commission === '' || commission === undefined) {
		alert("Please enter commission!");
		return;
	}

	if(isInt(commission) || isFloat(commission)) {
		jq('ltvform').submit();
		return true;
	} else {
		alert("Please enter proper value for commission");
		return false;				
	}

}

function isInt(n){
	return n != "" && !isNaN(n) && Math.round(n) == n;
}

function isFloat(n){
	return n != "" && !isNaN(n) && Math.round(n) != n;
}