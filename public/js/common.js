// prototypes
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g,"");
};

Array.prototype.in_array = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++) {
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
};


