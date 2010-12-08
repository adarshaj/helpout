<?php

//if(isset($_REQUEST['installed'])&& ($_REQUEST['installed']== 1))
//header('Location: http://apps.facebook.com/reachout-two');

require '../src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '116117598455174',
  'secret' => '277ad601c003df19c31334134f635025',
  'cookie' => true,
));

$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


if($me && isset($_REQUEST['entry']))
{
include_once("../mysql.config.php");

$qry="INSERT into reachout_registrations (user_fb_id,category,location) values ('".$_REQUEST['fid']."','".$_REQUEST['category']."','".$_REQUEST['stateser']."')"; 
mysql_query($qry);
mysql_close();

}

if($me && isset($_REQUEST['search']))
{
include_once("../mysql.config.php");

$qry="SELECT DISTINCT(user_fb_id) from reachout_registrations where category='".$_REQUEST['category']."' and location='".$_REQUEST['location']."'";
$result= mysql_query($qry);
$i=0;
while ($thisrow=mysql_fetch_row($result))  //get one row at a time
  {
  $searchRes[$i]=$thisrow[0];
  $i++;
  }
mysql_close();
}

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Help Out - RHoK</title>
    <style>
      body {
      font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
      text-decoration: none;
      color: #3b5998;
      }
      h1 a:hover {
      text-decoration: underline;
      }
    </style>
  </head>
  <body>
    
    <div id="fb-root"></div>
    
    <script>
      window.fbAsyncInit = function() {
      FB.init({
      appId   : '<?php echo $facebook->getAppId(); ?>',
      session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
      status  : true, // check login status
      cookie  : true, // enable cookies to allow the server to access the session
      xfbml   : true // parse XFBML
      });

      // whenever the user logs in, we refresh the page
      FB.Event.subscribe('auth.login', function() {
      window.location.reload();
      });
      };

      (function() {
      var e = document.createElement('script');
      e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
      e.async = true;
      document.getElementById('fb-root').appendChild(e);
      }());
    </script>
        
     <table style="width:auto;">
      <tr >        
        <td>
          <a href="http://picasaweb.google.com/lh/photo/S4vF6FY3hIiuF_TsLemoog?feat=embedwebsite">
            <img src="http://lh5.ggpht.com/_e5FwhTNAp00/TPsrJG5N99I/AAAAAAAAH8U/sOLUtHNPo_8/s288/help-out-ico.jpg" height="104" width="144" />
          </a>
        </td>
        <td width="50"></td>
        <td align="center">
          <h1> Help Out <br/> RHoK</h1>
        </td>
      </tr>      
    </table> <br/>  

    <?php if ($me): ?>
    
    <?php else: ?>
    <div>
      <fb:login-button></fb:login-button>
    </div> 
    <?php endif ?>


    <?php if ($me): ?>
    <img src="https://graph.facebook.com/<?php echo $uid; ?>/picture">
    <?php echo "Welcome ".$me['name'];?>

    <form >
      <input type="hidden" name="entry" value ="1"></input>
      <input type="hidden" name="fid" value ="<?php echo $me['id'];?>"></input>

      <br></br>
      <select name="category">        
        <option value="" selected="selected"> --- Select Category ---</option>
        <option id="bdonate" >Blood Donation</option>
        <option id="fedu" >Free Education</option>
        <option id="health">Health Check up</option>              
      </select>
      <select name="stateser">
        <option value="" selected="selected"> --- Select Location ---</option>
        <option id="1"  >Bangalore</option>
        <option id="2">Kolkata</option>
        <option id="3">Chennai</option>
        <option id="4">Mumbai</option>
        <option id="5">Delhi</option>
      </select>

      <button type="submit">Enlist as a Helping Hand!</button>
      <br></br>
    </form>
      
    <form name="search" >
      <input type="hidden" name="search" value ="1"></input>
      
      <select name="category">
        <option value="" selected="selected"> --- Select Category ---</option>
        <option id="bdonate">Blood Donation</option>
        <option id="fedu">Free Education</option>
        <option id="health">Health Check up</option>
      </select>
      <select name="location">
        <option value="" selected="selected"> --- Select Location ---</option>
        <option id="1">Bangalore</option>
        <option id="2">Kolkata</option>
        <option id="3">Chennai</option>
        <option id="4">Mumbai</option>
        <option id="5">Delhi</option>
      </select>
      <button type="submit">Search For a Helping Hand!</button>
    </form>

      <br></br>
      <?php if(isset($searchRes))
      {?>
        <table bordercolor ="black" border="1" >
          <tr>
          <?php
      foreach( $searchRes as $ruid)
      {?>
            <td width="100" bordercolor ="black" border="5" align="center" >
          
      <a href="http://www.facebook.com/profile.php?id=<?php echo $ruid;?>"> <img src="https://graph.facebook.com/<?php echo $ruid; ?>/picture" />
     
              </a>
            </td>
      <?php 
      }
      ?>
          </tr>
        </table>
      <?php
      }
      ?>
      
      <?php else: ?>
    <strong>
      <em>You are not Connected.</em>
    </strong>
    <?php endif ?>

  </body>
</html>
