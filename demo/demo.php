 <?php
//Detect or force "Selfie" mode for mobile devices
//You can remove Android if planning to use Chrome-  supports both methods . or extend user agent checks etc.
if (isset($_GET['forcemobile']) ) {  $_SERVER['HTTP_USER_AGENT']="iPhone";}
if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')  ) {
$useragent="mobile";  } else {  $useragent="desktop"; }
//CONFIGURATION
//Basically quality of selfie required - you need to play and select required quality, 
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone')) { 	$jpegquality=30; $jpegW=300; $jpegH=500; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) { 	 	$jpegquality=80; $jpegW=800; $jpegH=800; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'Android')) {	$jpegquality=70; $jpegW=300; $jpegH=500; }
 ?>
 <html>
 <body>
 <style>
.resImg {
	width:100%;
	max-width:300px;
}
.resImg[src='']{
    display: none;
}
.photoUp[name='photo'] {   display: none; }
.button {
  font-size: 2em;
  text-decoration: none;
  background-color: #EEEEEE;
  color: #333333;
  padding: 2px 6px 2px 6px;
  border-top: 1px solid #CCCCCC;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #CCCCCC;
}
</style>
<script src=http://code.jquery.com/jquery-2.1.4.min.js></script>
 <?
 if ( isset($_REQUEST['action'])     ) {  
	  //Get and crop it
	  if ( isset($_POST["dataimg"]) ) { 
if ( strstr($_POST["dataimg"],"base64",true)=="data:image/jpeg;") { $type="jpeg"; } else { $type="png"; }
if ($type=="png") {   $_POST["dataimg"]=str_replace("data:image/png;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]); }
if ($type=="jpeg") {  $_POST["dataimg"]=str_replace("data:image/jpeg;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]);}
$tmpID=uniqid();
file_put_contents( "/tmp/".md5($tmpID),$_POST["dataimg"]);
$dst_x = 0;   // X-coordinate of destination point. 
$dst_y = 0;   // Y --coordinate of destination point. 
$dst_w = 0;   // X-coordinate of destination point. 
$dst_h = 0;   // Y --coordinate of destination point. 
$array1["x"] = $_POST['x']; // Crop Start X position in original image
$array1["y"]= $_POST['y']; // Crop Srart Y position in original image
$array1["width"]= $_POST['w']; // Thumb width
$array1["height"] = $_POST['h']; // Thumb height
// Create image instances
if ( $type=="png") { $src = imagecreatefrompng("/tmp/".md5($tmpID));}
if ($type=="jpeg") {   $src = imagecreatefromjpeg("/tmp/".md5($tmpID));}
$dest = imagecreatetruecolor(intval($array1["width"])-10, intval($array1["height"])-10 ) or die('Cannot Initialize new GD image stream'); 
imagecopy($dest, $src, 0, 0,$array1["x"]+5, $array1["y"]+5, $array1["width"], $array1["height"]);
imagepng($dest,  "/tmp/cropped-".md5($tmpID).".png");

######################## FINAL RESULT OF THE LIBRARY ############################
$image64=base64_encode(file_get_contents("/tmp/cropped-".md5($tmpID).".png"));
//Some clean up
unlink("/tmp/cropped-".md5($tmpID).".png");
unlink("/tmp/".md5($tmpID));
//$image64 is where you have your final cropped face, send it to API
echo "Here is the final face<hr><img src='data:image/png;base64,".$image64."'><br>
<P>Send this to FACE2 API for registration or verification";
//Call API here
}
 }
  if ( !isset($_REQUEST['action'])     ) {
			if($useragent=="mobile")
{
?> <h3 class="page-header">FACE2 Selfie</h3> 
<? }  else {?>
<h3 class="page-header">FACE2 Webcam</h3> 
<? } ?> <form action="demo.php" class="ajax-form clearfix" method=post id=submit >
			<input type=hidden id=action name=action value="register">
			<?php	
			if($useragent=="mobile")
{
?>  
<div id="area"> <div>  <p><span></span></p>
                        <i></i>  
                        <input name="photo" type="file" accept="image/*;capture=camera"  class="photoUp"/>
                        <label for="demo-username">Enrol your face</label><br><br> <u  style="text-decoration: none" class="button" >Take a selfie</u>
                      
                    </div>
                    <script>
                        $().ready(function() {
                            $('#area u').click(function() {
                                $('input[name=photo]').trigger('click');
                            });
                            $('input[name=photo]').change(function(e) {
                                var file = e.target.files[0];
                                // RESET
                                $('#area p span').css('width', 0 + "%").html('');
                                $('#area img, #area canvas').remove();
                                //$('#area i').html(JSON.stringify(e.target.files[0]).replace(/,/g, ", <br/>"));
                                // CANVAS RESIZING
                                canvasResize(file, {
                                    width: <?=$jpegW?>,
                                    height: <?=$jpegH?>,
                                    crop: false,
                                    quality: <?=$jpegquality?>,
                                    rotate: 0,
                                    callback: function(data, width, height) {
                                        
                                         document.getElementById('image').src=data;
                                      document.getElementById("detectF").style.display='';
                                        // /IMAGE UPLOADING
                                        // =================================================               
                                    }
                                });
                            });
                        });
                    </script>
                    <script src="binaryajax.js"></script>
                    <script src="exif.js"></script>
                    <script src="canvasResize.js"></script>
                </div>
				
 
        </span>
    
   <span class=logo>
	 <div class="picture-container">
      <img src="" id="image" class=resImg>
    </div>
   
 
    <script src="js/jquery.facedetection.js"></script> 
	 <img id=download class=download >
	 <input type=hidden id=dataimg name=dataimg >
<input type=hidden id=x name=x>
<input type=hidden id=y name=y>
<input type=hidden id=w name=w>
<input type=hidden id=h name=h>
<br>
    <a id="try-it" href="#" style="text-decoration: none">
        <button class="button-try btn btn-info" id=detectF style="display:none; text-decoration: none"  > Detect face </button>
    </a>
  <div class=cnvCnt id=cnvCnt style="display:none">
  <h5>Detected face</h5>
  <canvas id=myCanvas style="border:1px solid #000000; width:100%" ></canvas>
  <br>
  <blockquote>continue if the face has been correctly detected, otherwise take another photo and try again</blockquote>
   </div>
	  
    <script>
        $(function () {
            "use strict";
            
            $('#try-it').click(function (e) {
                e.preventDefault();
                $('.face').remove();
                $('#image').faceDetection({
                    complete: function (faces) {
                   
                        
                        for (var i = 0; i < faces.length; i++) {
                            $('<div>', {
                                'class':'face',
                                'css': {
                                    'position': 'absolute',
                                    'left':     faces[i].x * faces[i].scaleX + 'px',
                                    'top':      faces[i].y * faces[i].scaleY + 'px',
                                    'width':    faces[i].width  * faces[i].scaleX + 'px',
                                    'height':   faces[i].height * faces[i].scaleY + 'px'
                                }
                            })
                            .insertAfter(this);
							
		 				
							var outC = document.getElementById("image");
		 var canvas = document.getElementById('myCanvas');
      var context = canvas.getContext('2d');
      var imageObj = new Image();
	   imageObj.src = outC.src;
	 
        // draw cropped image
        var sourceX = faces[i].x ;
        var sourceY = faces[i].y ;
        var sourceWidth = faces[i].width ;
        var sourceHeight = faces[i].height ;
        var destWidth = sourceWidth;
        var destHeight = sourceHeight;
        var destX = canvas.width / 2 - destWidth / 2;
        var destY = canvas.height / 2 - destHeight / 2;
document.getElementById('x').value=sourceX;
document.getElementById('y').value=sourceY;
document.getElementById('w').value=sourceWidth;
document.getElementById('h').value=sourceHeight;
        context.drawImage(imageObj, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight);
       
      
	  
document.getElementById("dataimg").value=document.getElementById("image").src;
document.getElementById("cnvCnt").style.display='';
document.getElementById("submitF").style.display='';
 
                        }
                    },
                    error:function (code, message) {
                        alert('Error: ' + message);
                    }
                });
            });
        });
    </script>
	
	 
<div class="form-group">
					<button type="submit" name="submit" id=submitF style="display:none"  class="btn btn-lg btn-success"> Complete registration </button>
					
					 
				</div>
				
				</form>
<? } else { ?>
			
<input type=hidden id=x name=x>
<input type=hidden id=y name=y>
<input type=hidden id=w name=w>
<input type=hidden id=h name=h>
<input type=hidden id=dataimg name=dataimg>
 <div class="form-group">
			        <label for="signup-email">Enrol your face</label><br>
					<div   class='bg-warning' style="padding: 1em 1em 1em 1em" id="info"><img src=allow.png align=right>
					<h4>Please allow access to your camera! <br>See the warning on the top of this window.</h4>
					
					</div>
			       <canvas id="output" ></canvas> <hr>
		<blockquote>  Make sure your face is inside the blue rectangle to be correctly detected!</blockquote>
		  </div> 
				<div class="form-group">
					<button type="submit" name="submit" onClick="capture()" class="btn btn-lg btn-success"> Complete  </button>
					
				</div>
<? } ?>		 
			</form>
    	
			<? } ?>
	 <?php
			
			if($useragent=="mobile")
{
?><? } else { ?>
	<script src="ccv.js"></script>
		<script src="face.js"></script>
			  <script>
// requestAnimationFrame shim
(function() {
	var i = 0,
		lastTime = 0,
		vendors = ['ms', 'moz', 'webkit', 'o'];
	
	while (i < vendors.length && !window.requestAnimationFrame) {
		window.requestAnimationFrame = window[vendors[i] + 'RequestAnimationFrame'];
		i++;
	}
	
	if (!window.requestAnimationFrame) {
		window.requestAnimationFrame = function(callback, element) {
			var currTime = new Date().getTime(),
				timeToCall = Math.max(0, 1000 / 60 - currTime + lastTime),
				id = setTimeout(function() { callback(currTime + timeToCall); }, timeToCall);
			
			lastTime = currTime + timeToCall;
			return id;
		};
	}
}());
var App = {
	start: function(stream) {
		App.video.addEventListener('canplay', function() {
			App.video.removeEventListener('canplay');
			setTimeout(function() {
				App.video.play();
				App.canvas.style.display = 'inline';
				App.info.style.display = 'none';
				App.canvas.width = App.video.videoWidth;
				App.canvas.height = App.video.videoHeight;
				App.backCanvas.width = App.video.videoWidth / 4;
				App.backCanvas.height = App.video.videoHeight / 4;
				App.backContext = App.backCanvas.getContext('2d');
			
				var w = 100 / 4 * 0.8,
					h = 170 / 4 * 0.8;
			
				App.comp = [{
					x: (App.video.videoWidth / 4 - w) / 2,
					y: (App.video.videoHeight / 4 - h) / 2,
					width: w, 
					height: h,
					
				}];
			
				App.drawToCanvas();
				 
 
			}, 500);
		}, true);
		
		var domURL = window.URL || window.webkitURL;
		App.video.src = domURL ? domURL.createObjectURL(stream) : stream;
	},
	denied: function() {
		App.info.innerHTML = " <h4> <i class='fa  fa-exclamation-triangle'></i> Camera access denied! Please reload and try again</h4>  ";
	},
	error: function(e) {
		if (e) {
			console.error(e);
		}
		App.info.innerHTML = 'Camera access denied. Please make sure you use a browser supporting camera access and/or camera access is allowed.';
	},
	drawToCanvas: function() {
		requestAnimationFrame(App.drawToCanvas);
		
		var video = App.video,
			ctx = App.context,
			backCtx = App.backContext,
			m = 4,
			w = 4,
			i,
			comp;
		
		ctx.drawImage(video, 0, 0, App.canvas.width, App.canvas.height);
		
		backCtx.drawImage(video, 0, 0, App.backCanvas.width, App.backCanvas.height);
		
		comp = ccv.detect_objects(App.ccv = App.ccv || {
			canvas: App.backCanvas,
			cascade: cascade,
			interval: 4,
			min_neighbors: 1
		});
		
		if (comp.length) {
			App.comp = comp;
		}
		
		 
		
		for (i = App.comp.length; i--; ) {
			ctx.drawImage(App.faceframe, (App.comp[i].x - w / 2) * m, (App.comp[i].y - w / 2) * m, (App.comp[i].width + w) * m, (App.comp[i].height + w) * m);
			
			document.getElementById('x').value=(App.comp[i].x - w / 2) * m;
			document.getElementById('y').value=(App.comp[i].y - w / 2) * m;
			document.getElementById('w').value=(App.comp[i].width + w) * m;
			document.getElementById('h').value=(App.comp[i].height + w) * m;
		 
			
		}
	}
};
App.faceframe = new Image();
App.faceframe.src = 'faceframe.png';
App.init = function() {
	App.video = document.createElement('video');
 
 
	
	App.backCanvas = document.createElement('canvas');
	App.canvas = document.querySelector('#output');
	App.canvas.style.display = 'none';
	App.context = App.canvas.getContext('2d');
	App.info = document.querySelector('#info');
	
	navigator.getUserMedia_ = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
	
	try {
		
		var vgaConstraints = {
  video: {
    mandatory: {
      maxWidth: 532,
      maxHeight: 290
    }
  }
};
		navigator.getUserMedia_(vgaConstraints, App.start, App.denied);
	} catch (e) {
		try {
			navigator.getUserMedia_('video', App.start, App.denied);
		} catch (e) {
			App.error(e);
		}
	}
	
	App.video.loop = App.video.muted = true;
 
	App.video.load();
	
 
	
};
App.init();
		function capture() {
		 
		 
		 var canvas = document.getElementById("output");
		var img    = canvas.toDataURL();
//location.href='register.php?image='+img+'&x='+document.getElementById("y").value+'&y='+document.getElementById("y").value+'&w='+document.getElementById("w").value+'&h='+document.getElementById("h").value;
document.getElementById("dataimg").value=img;
document.getElementById("output").style.display='none';
waitingDialog.show();
document.getElementById("submit").submit();
return true;
  
		
		}
		
		 
		
		</script>
		
<?php } ?>
	</body>
	</html>