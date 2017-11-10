<?php
  include('server.php');
  if (!isset($_SESSION['user'])) {
  	header('location: home.php');
  }

  if ($_SESSION['user']['user_type'] == 'member'){
    header('location: member_main.php');
  }

	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['user']);
		header("location: home.php");
	}

?>
<!DOCTYPE html>
<html>
<head>
  <title>HELPFit | Create Training Session</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Montserrat|Raleway|Cabin" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link rel="stylesheet" href="createsession.css">
  <link rel="stylesheet" href="nav.css">
  <link rel="icon" href="favicon.ico" type="image/x-icon"/>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#home"><img src="icons/helpfitlogosmall.png"></a>
    </div>
    <div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="trainer_main.php">Home</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="createsession.php" class="active-page">Create Sessions</a></li>
          <li><a href="view_history_trainer.php">Manage Sessions</a></li>
          <li><a href="view_reviews.php">View Reviews</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <a href="trainer_main.php?logout='1'"><button class="btn navbar-btn nav-logout"><strong>Log Out</strong></button></a>
				</ul>
      </div>
    </div>
  </div>
</nav>
<div class="container main-container">
  <div class="col-xs-12 col-sm-12 col-sm-offset-0 col-lg-8 col-lg-offset-2">
    <div class="profile-wrap">
      <div class="profile-container">
        <h1><strong>CREATE A SESSION</strong></h1>
        <hr>
        <?php echo display_error(); ?>
        <?php if (isset($_SESSION['success_createsession'])) : ?>
          <div class="alert alert-success alert-dismissible">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>
              <?php
                echo $_SESSION['success_createsession'];
                unset($_SESSION['success_createsession']);
              ?>
            </strong>
            </h1>
          </div>
        <?php endif ?>
        <div class="session-form">
          <form id="session" action="#" method="post" autocomplete="off" >
            <div class="form-group">
              <label for="training_type" class="label">TRAINING TYPE</label>
              <div class="row">
                <div class="col-sm-12 col-lg-6">
                  <label class="radio">
                    <input type="radio" id="radio-personal" name="training_type" value="personal" onclick="showErrorMsg(); showErrorMsgSdm();" required>
                    <div class="choice">Personal Training</div>
                  </label>
                </div>
                <div class="col-sm-12 col-lg-6">
                  <label class="radio">
                    <input type="radio" id="radio-group" name="training_type" value="group"  onclick="showErrorMsg(); " required>
                    <div class="choice">Group Training</div>
                  </label>
                </div>
              </div>
            </div>
            <div id="error-msg-pg">Please select personal or group training.</div>
            <div class="form-group">
              <label for="title" class="label">TITLE</label>
              <input id="title" type="text" name="title" class="form-control" placeholder="Title" required>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-sm-12 col-lg-6">
                <label for="date" class="label">DATE</label>
                <input id="date" type="text" name="date" class="form-control" required>
              </div>
                <div class="col-sm-12 col-lg-6 time-col">
                <label for="time" class="label">TIME</label>
                <input id="time" type="time" name="time" class="form-control" required>
              </div>
              </div>
            </div>
            <div class="form-group">
              <label for="fee" class="label">FEE</label>
              <input id="fee" type="number" name="fee" class="form-control" placeholder="0.00" required>
            </div>
            <div id="group-training">
            <div class="form-group">
                <label for="max_pax" class="label">MAX PARTICIPANTS</label>
              <input id="max_pax" type="number" name="max_pax" class="form-control" placeholder="e.g. 30" />
            </div>
            <div class="form-group">
              <label for="class_type" class="label label-center">CLASS TYPE</label>
              <div class="row">
                <div class="col-sm-12 col-lg-4">
                  <label class="radio">
                    <input type="radio" id="radio-sport" name="class_type" value="sport" onclick="showErrorMsgSdm();" />
                    <div class="choice">Sport</div>
                  </label>
                </div>
                <div class="col-sm-12 col-lg-4">
                  <label class="radio">
                    <input type="radio" id="radio-dance" name="class_type" value="dance" onclick="showErrorMsgSdm();" />
                    <div class="choice">Dance</div>
                  </label>
                </div>
                <div class="col-sm-12 col-lg-4">
                  <label class="radio">
                    <input type="radio" id="radio-mma" name="class_type" value="mma" onclick="showErrorMsgSdm();" />
                    <div class="choice">MMA</div>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div id="error-msg-sdm">Please select sport, dance or MMA type of training.</div>
            <div class="form-group">
              <button type="submit" name="createsession" class="button" style="margin-top:10px;" onclick="showErrorMsg(); showErrorMsgSdm();">CREATE</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<footer class="text-center">
  <a class="up-arrow" href="#top" data-toggle="tooltip" title="TO TOP">
    <span class="glyphicon glyphicon-chevron-up"></span>
  </a><br><br>
  <div class="footer-social-icons">
    <h4 class="_12">Follow us on</h4>
    <ul class="social-icons">
        <li><a href="" class="social-icon"> <i class="fa fa-facebook"></i></a></li>
        <li><a href="" class="social-icon"> <i class="fa fa-twitter"></i></a></li>
        <li><a href="" class="social-icon"> <i class="fa fa-rss"></i></a></li>
        <li><a href="" class="social-icon"> <i class="fa fa-youtube"></i></a></li>
        <li><a href="" class="social-icon"> <i class="fa fa-linkedin"></i></a></li>
        <li><a href="" class="social-icon"> <i class="fa fa-google-plus"></i></a></li>
    </ul>
  </div>
  <div class="f_cont">
    <h4 class="_12">Stay Connected</h4>
    <p><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;KL, Malaysia</p>
    <p><span class="glyphicon glyphicon-phone"></span>&nbsp;&nbsp;Phone: +60 10000000</p>
    <p><span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;E-mail: mail@mail.com</p>
  </div>
  <p style="display:inline;">Privacy Policy</p> |<p style="display:inline;"> Copyright &copy; 2017 HELPFit</p>

</footer>

<script>

// to check if the radio buttons are selected
function showErrorMsg(){
  if(!document.getElementById("radio-group").checked && !document.getElementById("radio-personal").checked) {
    document.getElementById('error-msg-pg').style.display = 'block';
  }else{
    document.getElementById('error-msg-pg').style.display = 'none';
  }
}
function showErrorMsgSdm(){
  if (document.getElementById("radio-group").checked){
    if(!document.getElementById("radio-mma").checked && !document.getElementById("radio-sport").checked
        && !document.getElementById("radio-dance").checked) {
      document.getElementById('error-msg-sdm').style.display = 'block';
    }else{
      document.getElementById('error-msg-sdm').style.display = 'none';
    }
  }
  else{
    document.getElementById('error-msg-sdm').style.display = 'none';
  }
}


$('#radio-group').change(function () {
    if($(this).is(':checked')) {
        $('#radio-sport').prop('required',true);
        $('#max_pax').prop('required',true);
        $('#group-training').css("display","block");
        $('#error-msg-pg').css("display","none");
    }
});

$('#radio-personal').change(function () {
    if($(this).is(':checked')) {
      $('#radio-sport').prop('required',false);
      $('#max_pax').prop('required',false);
      $('#group-training').css("display","none");
      $('#error-msg-pg').css("display","none");
    }
});

$(function() {
    $("#date").datepicker({minDate: 0,
      dateFormat: 'dd/mm/yy',
      defaultDate: '00/00/00'
    });
});
$("#date").attr( 'readOnly' , 'true' );

</script>
</body>
</html>
