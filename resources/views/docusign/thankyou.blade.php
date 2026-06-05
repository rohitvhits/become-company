<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		.thank-you {
		    display: flex;
		    align-items: center;
		    justify-content: center;
		    padding-top: 35vh;
		    flex-direction: column;
		}
		.thank-you  .ty{
			height: 40px;
		    width: 40px;
		    border: 2px solid #4eae49;
		    border-radius: 50%;
		    position: relative;
		    box-shadow: 0 0 0 0px #4eae492e;
		    background: #4eae49;
		   animation: tyanim 1s infinite;
		}
		@keyframes tyanim {
		  0% {box-shadow: 0 0 0 0px #4eae492e;}
		   50% {box-shadow: 0 0 0 10px #4eae492e;}
		  100% {box-shadow: 0 0 0 0px #4eae492e;}
		}
		.thank-you .ty span{
			display: block;
			background: #fff;
			 position: absolute;
			height: 2px;
		}
		.thank-you .ty span:nth-child(1){
		    width: 19px;
		    top: 19.1px;
		    left: 14px;
		    transform: rotate(-46deg);
		}
		.thank-you .ty span:nth-child(2){
		    width: 11px;
		    top: 22px;
		    left: 9px;
		    transform: rotate(-128deg);
		}
	</style>
</head>
<body>
<div class="thank-you">
	<div class="ty"><span></span><span></span></div>
	<h1>Thank you. Document successfully submitted.</h1>
</div>
</body>
</html>