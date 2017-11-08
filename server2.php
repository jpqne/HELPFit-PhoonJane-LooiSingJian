<?php
  session_start();

  $db = mysqli_connect('localhost','root','','helpfit');

  function mysqli_query_or_die($query) {
    global $db;
      $result = mysqli_query($db, $query);
      if ($result)
          return $result;
      else {
          $err = mysqli_error($db);
          die("<br>{$query}<br>*** {$err} ***<br>");
      }
  }
  function printSessions(){
    $query = "SELECT * FROM trainingsession ORDER BY trainingsession.training_type, trainingsession.date, trainingsession.time ASC";
    $result = mysqli_query_or_die($query);
    $first_row = true;
    $none_available = true;
    if(isset($_SESSION['type'])){
      echo '<div class="training-container">';
      while ($row = mysqli_fetch_assoc($result)) {
        //checks if member have already joined the training session
        $sessionID = $row['sessionID'];
        $member_id = $_SESSION['user']['user_id'];
        $joined = mysqli_query_or_die("SELECT * FROM membersession WHERE sessionID='$sessionID' AND member_id='$member_id'");
        if ($row['status'] == "available" && mysqli_num_rows($joined) == 0){
          //get the trainer
          $trainer_id = $row['trainer_id'];
          $ses_trn = mysqli_query_or_die("SELECT * FROM user WHERE user_id='$trainer_id'");
          $trainer_user = mysqli_fetch_assoc($ses_trn);
          $ses_trn = mysqli_query_or_die("SELECT * FROM trainer WHERE user_id='$trainer_id'");
          $trainer = mysqli_fetch_assoc($ses_trn);

          if($row['training_type'] == $_SESSION['type'])
          {
            $none_available = false;
            //get the personal or group session attributes
            if ($row['training_type'] == "personal"){
              $ses_p = mysqli_query_or_die("SELECT * FROM personaltraining WHERE sessionID='$sessionID'");
              $personal = mysqli_fetch_assoc($ses_p);
            }else if ($row['training_type'] == "group"){
              $ses_g = mysqli_query_or_die("SELECT * FROM grouptraining WHERE sessionID='$sessionID'");
              $group = mysqli_fetch_assoc($ses_g);
            }

            if ($first_row) {
                $first_row = false;
            }
            $dateDM = DateTime::createFromFormat('Y-m-d', $row['date'])->format('M d');
            $dateY = DateTime::createFromFormat('Y-m-d', $row['date'])->format('Y');
            $time = DateTime::createFromFormat('H:i:s', $row['time'])->format('h:i a');
            //for desktop and tablet
            echo '<div class="panel panel-default">
              <div class="container-fluid">
                <div class="col-xs-2 date-time">
                  <h3>'.$dateDM.'</h3>
                  <h4 class="year">'.$dateY.'</h4>
                  <h4 class="time">'.$time.'</h4>
                </div>
                <div class="col-xs-5 title-class ';
                if(isset($group)){
                  echo 'title-group';
                }
                if (isset($personal)){
                  echo 'title-personal';
                }

            echo '">
                  <p> SESSION ID: #'.$row['sessionID'].'</p>
                  <h2 title="'.$row['title'].'">'.$row['title'].'</h2>';
            if(isset($group)){
              //count how many members have participated in the group training
              $cquery = mysqli_query_or_die("SELECT count(*) AS total FROM membersession WHERE sessionID = '$sessionID'");
              $data = mysqli_fetch_assoc($cquery);
              $no_pax = $data['total'];
              echo '<h3>Type: '.$group['class_type'].'</h3>';
              echo '<p class="par_label">Participants </p>
                    <p class="par">'.$no_pax.'/'.$group['max_pax'].'</p>';
            }
            if (isset($personal)){
              echo '<h3 class="notes">';
              if (empty($personal['notes'])){
                echo 'Notes: N/A';
              }else{
                echo 'Notes: '.$personal['notes'];
              }
              echo '</h3>';
            }
            echo '</div>
                <div class="col-xs-3 trainer-rate">
                  <p>BY TRAINER</p>
                  <h3 title="'.$trainer_user['name'].'">'.$trainer_user['name'].'</h3>
                  <h4 title="'.$trainer['specialty'].'">Specialty: '.$trainer['specialty'].'</h4>
                  <span class="stars" data-rating="3.75" data-num-stars="5" ></span>
                </div>
                <div class="col-xs-2 join-div">
                <label class="radio">
                    <input type="radio" name="join" class="btn join_btn" value="'.$sessionID.'">
                    <div class="btn"><p>
                      <strong>JOIN</strong><br />
                      RM'.$row['fee'].'</p>
                    </div>
                  </label>
                </div>
              </div>
            </div>';

            //for mobile
            echo '<div class="panel mobile-panel">
              <div class="container-fluid">
                <div class="col-xs-6 col-mobile">
                  <p class="sid"> #'.$row['sessionID'].'</p>
                  <p class="title">'.$row['title'].'</p>
                  <p class="notes-type">';
              if(isset($group)){
                echo '<b>Type:</b> '.$group['class_type'].'</p>';
                echo '<p class="mob_par"><b>Participants:</b> '.$no_pax.'/'.$group['max_pax'].'</p>';
                unset($group);
              }
              if (isset($personal)){
                echo '<div class="notes-div">';
                if (empty($personal['notes'])){
                  echo '<b>Notes:</b> N/A';
                }else{
                  echo '<b>Notes:</b> '.$personal['notes'];
                }
                echo '</div></p>';
                unset($personal);
              }
              echo '<p class="fee">RM'.$row['fee'].'</p>
              <p class="date-time">'.$dateDM.','.$dateY.' '.$time.'</p>
              </div>
              <div class="col-xs-6 col-join-mobile">
                <p class="label">Trainer: </p> <br>'.$trainer_user['name'].'</p>
                <p class="label">Specialty: </p> <br>'.$trainer['specialty'].'</p>
                <div class="stars-div"><span class="stars" data-rating="3.75" data-num-stars="5" title="3.75"></span></div>
                <div class="col-xs-12 join-mobile">
                <label class="radio">
                    <input type="radio" name="join" class="btn join_btn_mobile" value="'.$sessionID.'">
                    <div class="btn"><strong>JOIN</strong>
                    </div>
                  </label>
                </div>
              </div>
              </div>
            </div>';
          }
        }
      }// end while loop
      if ($none_available){
        echo '<div style="text-align:center"><h3 style="margin:90px 30px 90px 30px;color:#fff;">
        Sorry! There are no '.$_SESSION['type'].' training sessions currently available.
        </h3></div>';
      }
        echo '</div>';
        unset($_SESSION['type']);
    }//end if training type
  }

  //join session
  if (isset($_POST['join'])){
    $sessionID = $_POST['join'];
    $member_id = $_SESSION['user']['user_id'];
    $query = "INSERT INTO membersession (member_id, sessionID)
				  VALUES('$member_id','$sessionID')";
		mysqli_query($db, $query);
    $ses = mysqli_query_or_die("SELECT * FROM trainingsession WHERE sessionID='$sessionID'");
    $ses = mysqli_fetch_assoc($ses);
    if ($ses['training_type'] == "personal"){
      $query = "UPDATE trainingsession SET status = 'full' WHERE sessionID = '$sessionID'";
      mysqli_query($db, $query);
    }else{
      $ses_g = mysqli_query_or_die("SELECT * FROM grouptraining WHERE sessionID='$sessionID'");
      $group = mysqli_fetch_assoc($ses_g);
      $result = mysqli_query_or_die("SELECT count(*) AS total FROM membersession WHERE sessionID = '$sessionID'");
      $data = mysqli_fetch_assoc($result);
      $no_pax = $data['total'];
      if ($no_pax >= $group['max_pax']){
        $query = "UPDATE trainingsession SET status = 'full' WHERE sessionID = '$sessionID'";
        mysqli_query($db, $query);
      }
    }
    $_SESSION['success_join'] = "You have successfully joined!";
		header('location: view_sessions.php');
    exit();
  }


// print session history
  function printHistory(){
    $member_id = $_SESSION['user']['user_id'];
    $result = mysqli_query_or_die("SELECT * FROM membersession JOIN trainingsession ON membersession.sessionID=trainingsession.sessionID WHERE member_id='$member_id' ORDER BY trainingsession.date, trainingsession.time ASC");
    $none_available = true;
    if(isset($_SESSION['type'])){
      echo '<div class="training-container">';
      while ($row = mysqli_fetch_assoc($result)) {
        $sessionID = $row['sessionID'];
        //get the trainer
        $trainer_id = $row['trainer_id'];
        $ses_trn = mysqli_query_or_die("SELECT * FROM user WHERE user_id='$trainer_id'");
        $trainer_user = mysqli_fetch_assoc($ses_trn);
        $ses_trn = mysqli_query_or_die("SELECT * FROM trainer WHERE user_id='$trainer_id'");
        $trainer = mysqli_fetch_assoc($ses_trn);

        //checks if session has expired(completed) or not
        $date = strtotime($row['date']);
        if ($date < strtotime(date("Y-m-d"))){
           $status_type = "completed";
        }else {
          $status_type = "upcoming";
        }

        if($status_type == $_SESSION['type'])
        {
          $none_available = false;
          //get the personal or group session attributes
          if ($row['training_type'] == "personal"){
            $ses_p = mysqli_query_or_die("SELECT * FROM personaltraining WHERE sessionID='$sessionID'");
            $personal = mysqli_fetch_assoc($ses_p);
          }else if ($row['training_type'] == "group"){
            $ses_g = mysqli_query_or_die("SELECT * FROM grouptraining WHERE sessionID='$sessionID'");
            $group = mysqli_fetch_assoc($ses_g);
          }

          $dateDM = DateTime::createFromFormat('Y-m-d', $row['date'])->format('M d');
          $dateY = DateTime::createFromFormat('Y-m-d', $row['date'])->format('Y');
          $time = DateTime::createFromFormat('H:i:s', $row['time'])->format('h:i a');
          //for desktop and tablet
          echo '<div class="panel panel-default">
            <div class="container-fluid">
              <div class="col-xs-2 date-time">
                <h3>'.$dateDM.'</h3>
                <h4 class="year">'.$dateY.'</h4>
                <h4 class="time">'.$time.'</h4>
              </div>
              <div class="col-xs-5 title-class ';
              if(isset($group)){
                echo 'title-group';
              }
              if (isset($personal)){
                echo 'title-personal';
              }

          echo '">
                <p> SESSION ID: #'.$row['sessionID'].'</p>
                <h2 title="'.$row['title'].'">'.$row['title'].'</h2>';
          if(isset($group)){
            //count how many members have participated in the group training
            $cquery = mysqli_query_or_die("SELECT count(*) AS total FROM membersession WHERE sessionID = '$sessionID'");
            $data = mysqli_fetch_assoc($cquery);
            $no_pax = $data['total'];
            echo '<h3>Type: '.$group['class_type'].'</h3>';
            echo '<p class="par_label">Participants </p>
                  <p class="par">'.$no_pax.'/'.$group['max_pax'].'</p>';
          }
          if (isset($personal)){
            echo '<h3 class="notes">';
            if (empty($personal['notes'])){
              echo 'Notes: N/A';
            }else{
              echo 'Notes: '.$personal['notes'];
            }
            echo '</h3>';
          }
          echo '</div>
              <div class="col-xs-3 trainer-rate">
                <p>BY TRAINER</p>
                <h3 title="'.$trainer_user['name'].'">'.$trainer_user['name'].'</h3>
                <h4 title="'.$trainer['specialty'].'">Specialty: '.$trainer['specialty'].'</h4>
                <span class="stars" data-rating="3.75" data-num-stars="5" ></span>
              </div>
              <div class="col-xs-2 join-div">
              <label class="radio">
                  <input type="radio" name="rate" class="btn join_btn" value="'.$sessionID.'">
                  <div class="btn"><p>
                    <strong>RATE<br />
                    TRAINER</strong></p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <aside class="rate">
            <h3>Have a question for me?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis totam distinctio molestiae exercitationem impedit ut libero. Sit accusamus consectetur distinctio nesciunt quae nobis consequatur facere.</p>
            <form>
              <textarea class="comments" name="comments" placeholder="Tell us what you feel!" font-weight="200" id="" cols="30" rows="7"></textarea>
            </form>
          </aside>';

          //for mobile
          echo '<div class="panel mobile-panel">
            <div class="container-fluid">
              <div class="col-xs-6 col-mobile">
                <p class="sid"> #'.$row['sessionID'].'</p>
                <p class="title">'.$row['title'].'</p>
                <p class="notes-type">';
            if(isset($group)){
              echo '<b>Type:</b> '.$group['class_type'].'</p>';
              echo '<p class="mob_par"><b>Participants:</b> '.$no_pax.'/'.$group['max_pax'].'</p>';
              unset($group);
            }
            if (isset($personal)){
              echo '<div class="notes-div">';
              if (empty($personal['notes'])){
                echo '<b>Notes:</b> N/A';
              }else{
                echo '<b>Notes:</b> '.$personal['notes'];
              }
              echo '</div></p>';
              unset($personal);
            }
            echo '<p class="fee">RM'.$row['fee'].'</p>
            <p class="date-time">'.$dateDM.','.$dateY.' '.$time.'</p>
            </div>
            <div class="col-xs-6 col-join-mobile">
              <p class="label">Trainer: </p> <br>'.$trainer_user['name'].'</p>
              <p class="label">Specialty: </p> <br>'.$trainer['specialty'].'</p>
              <div class="stars-div"><span class="stars" data-rating="3.75" data-num-stars="5" title="3.75"></span></div>
              <div class="col-xs-12 join-mobile">
              <label class="radio">
                  <input type="radio" name="rate" class="btn join_btn_mobile" value="'.$sessionID.'">
                  <div class="btn"><strong>RATE</strong>
                  </div>
                </label>
              </div>
            </div>
            </div>
          </div>';
        }
      }// end while loop
      if ($none_available){
        echo '<div style="text-align:center"><h3 style="margin:90px 30px 90px 30px;color:#fff;">
        You do not have any '.$_SESSION['type'].' training sessions.
        </h3></div>';
      }
        echo '</div>';
        unset($_SESSION['type']);
    }//end if type
  }
?>