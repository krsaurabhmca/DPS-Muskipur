
<script>
function YouTubeGetID (url){
  var ID = '';
  url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
  if(url[2] !== undefined) {
    ID = url[2].split(/[^0-9a-z_\-]/i);
    ID = ID[0];
  }
  else {
    ID = url;
  }
    return ID;
}

//var x = YouTubeGetID('https://youtu.be/ZUZKvMtFbig');
//var x = YouTubeGetID('https://www.youtube.com/watch?v=5I_2cgglGms');

//alert(x);
</script>

<?php

function rnd_str($url)
{
$x  = preg_match_all("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#",$url, $matches); 
  $id  =$matches[0][0];
  return $id;
}

//print_r( rnd_str('https://www.youtube.com/watch?v=5I_2cgglGms'));
echo rnd_str('https://youtu.be/ZUZKvMtFbig');


?>

