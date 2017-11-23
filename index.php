<script>
// Set the date we're counting down to
var countDownDate = new Date("nov 25, 2017 15:00:00").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Output the result in an element with id="demo"
    document.getElementById("counter").innerHTML = days + "<span>n</span> " + hours + "<span>ó</span> "
    + minutes + "<span>p</span> "/* + seconds + "mp"*/;
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("counter").innerHTML = "Üdvözlünk";
    }
}, 1000);
</script>
<?php


echo '
<link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
	<style>
	.polcContentWrapper img{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);}
	@media all and (max-width:1366px) {div#counter{margin-top:50px!important;}}
	@media all and (max-width:768px) {.polcContentWrapper img{top:50%;}div#counter{margin-top:10%!important;}}
	</style>
	<div class="polcContentWrapper">
		<div id="counter" style="text-align: center;font-size: 44px;color: #fb5c43;margin-bottom: 20px;margin-top:15%; font-weight: 700; font-family:Titillium Web;"></div>
		<img src="wp-content/themes/polc/img/plc_november.png">
	</div>';
