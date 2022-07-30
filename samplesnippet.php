<?php


include '../imi_configuration/D3sS0L4aToR.php';
include '../imi_configuration/app_indicator.php';

$minutes = "nope";

if(isset($_GET['halo'])) { 
    $token = $_GET['halo'];
    $Sailt = $_GET['Synt'];

    try {
        $chicken = "SELECT * FROM `WS_Reason_Pop` WHERE `T4K_Promise` = '$token' AND `T4K_Email` = '$Sailt' LIMIT 1";
        $prepaChickent = $conn->prepare($chicken);
        $prepaChickent->execute();

        if ($prepaChickent->rowCount() > 0) {
            $result = $prepaChickent->fetch();
            $thisToke = $result['T4K_Promise'];
            $EmmaisWat = $result['T4K_Useed'];
            $WhyU = $result['T4K_Purpo'];
            $FacelessV = $result['T4K_Expires'];

            $date = date('Y/m/d H:i:s');

            $t1 = strtotime($date);
            $t2 = strtotime($FacelessV);
            // echo $t1;
            // echo '<br>';
            // echo $t2;
            $delta_T = ($t2 - $t1);
            $minutes = round(((($delta_T % 604800) % 86400) % 3600) / 60); 
            // echo '<br>';
            // echo $minutes;
           
            if ($minutes >= 0 && $minutes <= 5 ) {
                $rstMess = "";
            }
            else {
                $rstMess = "<div>Password Request Expired</div>";
            }

        } else {
            $rstMess = "<div>Sorry, I cant find you here</div>";
        }
    }
    catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if(isset($_POST['quint_tessa']) && $minutes > 0 && $minutes <= 5) {
    
    $m4sP4s = $_POST['goose_pass'];
    $m5sP4s = $_POST['goose_confirm'];

    try {

        if ($m4sP4s != $m5sP4s) {
            $rstMess = "<div>Password does not match</div>";
        }
        elseif (strlen($m5sP4s)<8) {
            $rstMess = "<div>Password is short</div>";
        }
        else {
            $encpo = hash('sha256', '4ll3g0r14' . $m5sP4s . '3qu1pg3nt5');

            $restapsw = "UPDATE `user_account` SET `u5er_pass` = '$encpo' WHERE `u5er_id` = '$EmmaisWat'";
            $deltok = "DELETE FROM `WS_Reason_Pop` WHERE `T4K_Promise` = '$token'";

            $preprestapsw = $conn->prepare($restapsw);
            $prepdeltok = $conn->prepare($deltok);
            
            $preprestapsw->execute();
            $prepdeltok->execute();
            $rstMess = "<div>Password Changed Successfully!</div>";

            echo '<meta http-equiv="refresh" content="3;url=https://imoodini.com/?summersault=new">';
        }

    }
    catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_GET['halo']) == "") {
    $rstMess = "<div>Invalid Request</div>";
}


?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="no index, nofollow" />

    <title>Password Reset · imoodini</title>
    
    <?php include "../imi_includes/imoodini-inc-css.php" ?>

	</head>
		<body>
        <?php if ($indicator == "") {
            
            echo '<div class="h-separator"></div>';

        } ?>


			<!-- Container -->
			<div class="container">
                <div class="row">
                    <div class="col-md-4 offset-md-4">
                    <div class="quackers text-center p-4">
                        <img class="img-fluid" src="https://imoodini.com/imi-media/imoodini-logo-prime-m.png" style="height: 30px;" alt="">
                        
                        <form class="mt-3 needs-validation" method="post" novalidate>
                        <h6 class="text-muted">Forgot Passsword</h6>
                            <div class="form-group">
                                
                                <input type="password" class="disablecopypaste text-center form-control rounded-0 mt-1"  placeholder="Your Password" maxlength="30" name="goose_pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required autocomplete="new-password">
                                <div class="invalid-feedback">
                                    Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters
                                </div>
                                <input type="password" class="disablecopypaste text-center form-control rounded-0 mt-1" placeholder="Repeat Password" maxlength="30" name="goose_confirm" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required autocomplete="new-password">
                                <div class="invalid-feedback">
                                    Oh you missed something!
                                </div>
                                <?php if(isset($rstMess)){ echo $rstMess;} ?>
                                <input type="hidden" name="quint_tessa" value="cross-eyes">
                                <input type="submit" class="btn btn-block btn-primary mt-2" value="Reset" name="rw_rosetta">
                            </div>
                        </form>
                        <hr>
                        <?php if ($indicator == "") {
            
                            echo '<div class="text-center mb-1"><a class="text-black-50" href="../index.php" ><i class="fi-xwllxl-arrow-simple-wide"></i> Go Back</a></div>';

                        } ?>
                    </div>
                    </div>
                </div>
            </div>
            <!-- / Container -->

            

            <?php include "../imi_includes/imoodini-inc-js.php" ?>
            
		</body>
	</html>