
jQuery(document).ready(function() {
	jQuery('#lastname').keyup(function(){
	    this.value = this.value.toUpperCase();
	});
});

function copy() {
  let textarea = document.getElementById("textarea");
  textarea.select();
  document.execCommand("copy");
}