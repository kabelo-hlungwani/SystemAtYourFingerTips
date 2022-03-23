<!DOCTYPE html>
<html>
<?php


$connect = mysqli_connect("localhost","root","","sayft");
use PHPMailer\PHPMailer\PHPMailer;   
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
 
  if(!isset($_SESSION)) 
  { 
      session_start(); 
      $id=$_SESSION['user_id'];
      $email=$_SESSION['email'];
      $bookingid=$_SESSION['bid'];
  }





  $query="SELECT * FROM payment
  WHERE payment.user_id ='$id'
  AND payment.booking_id='$bookingid'";

$qry=mysqli_query($connect,$query);
$data=mysqli_fetch_array($qry);

$bal=(double)$data['balAmount'];
$totbal=(double)$data['totAmount'];

$outb=$totbal-$bal;

$query1="SELECT user.name as names,user.surname as surr,booking.booking_id as bid,FORMAT(service.price,2) as total,
FORMAT(service.price *.5,2) as half,booking.payment_status as status
FROM user,service,booking
WHERE user.user_id=booking.user_id
AND booking.service_id=service.service_id
AND user.user_id ='$id'
AND booking.booking_id='$bookingid';";

$qry1=mysqli_query($connect,$query1);
$data1=mysqli_fetch_array($qry1);

if(isset($_POST['process']))
{

$pay=$_POST['paymenttype'];
$price=$_POST['price'];

if($price > $totbal)
{
    echo '<script>alert("Your payment is greater than the actual total.");window.location = "paymentU.php";</script>';
    exit;

}else
{
   if($price > $outb)
   {

     echo '<script>alert("Your payment is greater than the actual remaining total.");window.location = "paymentU.php";</script>';
    exit;


   }else
   {
$query1=" UPDATE payment SET 
balAmount=($price + $bal) 
WHERE booking_id='$bookingid'";
$qry1=mysqli_query($connect,$query1); 
    

   }
  
}
   

//$data1=mysqli_fetch_array($qry1);


$query3="SELECT user.user_id as ref,user.name as name,user.surname as surname,user.email as email,user.phone_number as numbers,service.image as photo,service.service_type as type,service.description as descp,service.price as price,booking.booking_date as bdate
FROM user,service,booking
WHERE
user.user_id=booking.user_id
AND service.service_id=booking.service_id
AND user.user_id='$id'
AND booking.booking_id='$bookingid'";



$result3=mysqli_query($connect,$query3);
$rows3=mysqli_fetch_array($result3); 



$query="SELECT user.user_id as ref,user.name as name,user.surname as surname,user.email as email,user.phone_number as numbers,service.image as photo,service.service_type as type,service.description as descp,service.price as price,booking.booking_date as bdate,payment.balAmount as bal,payment.totAmount as tot,payment.qty as qty
FROM user,service,booking,payment
WHERE
user.user_id=service.user_id
AND booking.service_id=service.service_id
AND payment.booking_id=booking.booking_id
AND booking.booking_id='$bookingid'";



$result=mysqli_query($connect,$query);
$rows=mysqli_fetch_array($result);

if($qry1){
mysqli_close($connect);
    
    $out=(double)$rows['tot']-(double)$rows['bal'];
    $net=$rows['tot'];
    $paid=$rows['bal'];
    $qty=$rows['qty'];
    $ref=$rows['name'].''.$rows['ref'];
    $names=$rows['surname'].' '.$rows['name'];
    $type=$rows['type'];
    $infom=$rows['descp'];
    $bdate=$rows['bdate'];
    $num=$rows['numbers'];
    $emails=$rows['email'];
$maina=$data1['names'].' '.$data1['surr'];
$senepe=$rows['photo'];
$getpic='<img style="width: 150px;" src="images/'.$senepe.'">';



        $mess="<table style='font-family: Asap, sans-serif;text-align:left;'>
        <tr><th style='font-size:50px;font-family: Asap, sans-serif;font-weight: Bold;background-color: #f03e33;color: rgb(255,255,255)'>SAYFT</th></tr>
        <tr><th style='font-size:20px;'>Updated booking Service Information&nbsp;</th></tr>
        <tr><td style='font-size:18px;'>Ref &nbsp;: &nbsp;$ref</tr>
        <tr><td style='font-size:18px;'>SP &nbsp;:&nbsp;$names</td></tr>
        <tr><td style='font-size:18px;'>Phone Number &nbsp;: &nbsp;$num</tr>
        <tr><td style='font-size:18px;'>Email &nbsp;:&nbsp;$emails</td></tr>
        <tr><td style='font-size:18px;'>Service &nbsp;:&nbsp;$type</td></tr>
        <tr><td style='font-size:18px;'>Desc &nbsp;:&nbsp;$infom</td></tr>
        
        <tr><td style='font-size:18px;'>Quantity &nbsp;:&nbsp;$qty</td></tr>
        <tr><td style='font-size:18px;'>Payment Type &nbsp;:&nbsp;$pay</td></tr>
        <tr><td style='font-size:18px;'>Total &nbsp;: R&nbsp;$net</td></tr>
        <tr><td style='font-size:18px;'>Paid Amount &nbsp;: R&nbsp;$paid</td></tr>
        <tr><td style='font-size:18px;'>Outstanding Amount &nbsp;: R&nbsp;$out</td></tr><br>
        <tr><td style='font-size:20px;color:red;'>NB: Keep this as a proof.</td></tr>
        </table>";
  

        
        $name =$maina;
        $email =$email;
        $subject ='Updated Booking';
        $body =$mess;
        
        require_once "PHPMailer/PHPMailer.php";
        require_once "PHPMailer/SMTP.php";
        require_once "PHPMailer/Exception.php";

        $mail = new PHPMailer();

        //SMTP Settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "sayftsystem@gmail.com";
        $mail->Password = 'Kabelo@15';
        $mail->Port = 465; //587
        $mail->SMTPSecure = "ssl"; //tls

        //Email Settings
        $mail->isHTML(true);
        $mail->setFrom($email, $name);
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $body;

        if ($mail->send()) {
            $status = "success";
            $response = "Email is sent!";
        } else {
            $status = "failed";
            $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
        }
//SP

$niome=$rows3['surname'].' '.$rows3['name'];
$nums=$rows3['numbers'];

$mess1="<table style='font-family: Asap, sans-serif;text-align:left;'>
<tr><th style='font-size:50px;font-family: Asap, sans-serif;font-weight: Bold;background-color: #f03e33;color: rgb(255,255,255)'>SAYFT</th></tr>
<tr><th style='font-size:20px;'></th></tr>
<br>
<tr><td style='font-size:20px;'>Hello $names !</td></tr>
<br>
<tr><td style='font-size:20px;'>$niome made updates on his bookings.</td></tr>
<br>
<tr><td style='font-size:18px;'>Client Name(s) &nbsp;: &nbsp;$niome</tr>
<tr><td style='font-size:18px;'>Phone Number &nbsp;: &nbsp;$nums</tr>
<tr><td style='font-size:18px;'>Email &nbsp;:&nbsp;$email</td></tr>
<tr><td style='font-size:18px;'>Service &nbsp;:&nbsp;$type</td></tr>
<tr><td style='font-size:18px;'>Desc &nbsp;:&nbsp;$infom</td></tr>

<tr><td style='font-size:18px;'>Quantity &nbsp;:&nbsp;$qty</td></tr>
<tr><td style='font-size:18px;'>Payment Type &nbsp;:&nbsp;$pay</td></tr>
<tr><td style='font-size:18px;'>Total &nbsp;: R&nbsp;$net</td></tr>
<tr><td style='font-size:18px;'>Paid Amount &nbsp;: R&nbsp;$paid</td></tr>
<tr><td style='font-size:18px;'>Outstanding Amount &nbsp;: R&nbsp;$out</td></tr><br>
<br>
<tr><td style='font-size:20px;'>For more information Log in into the system and goto [View Bookings]</td></tr>
<br>
<tr><td style='font-size:20px;'>Regards</td></tr>
<br>
<tr><td style='font-size:20px;'>sayftsystem.</td></tr>
</table>";





$name =$niome;
$email =$email;
$subject ='Updated Booking';
$body =$mess1;

require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$mail = new PHPMailer();

//SMTP Settings
$mail->isSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->Username = "sayftsystem@gmail.com";
$mail->Password = 'Kabelo@15';
$mail->Port = 465; //587
$mail->SMTPSecure = "ssl"; //tls

//Email Settings
$mail->isHTML(true);
$mail->setFrom($email, $name);
$mail->addAddress($emails);
$mail->Subject = $subject;
$mail->Body = $body;

if ($mail->send()) {
    $status = "success";
    $response = "Email is sent!";
} else {
    $status = "failed";
    $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
}

echo '<script>alert("payment processed,Receipt was sent to your email.");window.location = "invoice.php";</script>';

exit;

}
else
{
    echo mysqli_error();

}
}


?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>SAYFT</title><link rel="icon" href="assets/logo1.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/material-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ABeeZee">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abhaya+Libre">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abril+Fatface">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aclonica">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Adamina">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Advent+Pro">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aguafina+Script">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Akronim">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aladin">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aldrich">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alef">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+Sans+SC">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alegreya+SC">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aleo">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alex+Brush">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alice">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alike">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Allerta">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Allerta+Stencil">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Allura">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Almendra">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Almendra+Display">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amaranth">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amatic+SC">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amethysta">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amiko">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amiri">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Amita">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Anaheim">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Andada">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Andika">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Angkor">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Annie+Use+Your+Telescope">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Anonymous+Pro">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Antic">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Antic+Didone">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Antic+Slab">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Anton">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arapey">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arbutus">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arbutus+Slab">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Architects+Daughter">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Archivo">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Archivo+Black">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Archivo+Narrow">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aref+Ruqaa">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arima+Madurai">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arizonia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Armata">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arsenal">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Artifika">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arvo">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arya">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Asap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Asar">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Asset">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Assistant">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Astloch">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Asul">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Athiti">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Atomic+Age">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aubrey">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Autour+One">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Average">
    <link rel="stylesheet" href="assets/css/Contact-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/dh-highlight-left-right.css">
    <link rel="stylesheet" href="assets/css/Footer-Basic.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/login-full-page-bs4.css">
    <link rel="stylesheet" href="assets/css/Google-Style-Login.css">
    <link rel="stylesheet" href="assets/css/Pretty-Registration-Form.css">
    <link rel="stylesheet" href="assets/css/Pretty-Login-Form.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean.css">
    <link rel="stylesheet" href="assets/css/Registration-Form-with-Photo.css">
    <link rel="stylesheet" href="assets/css/Search-Field-With-Icon.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/Swipe-Slider-9.css">
</head>
<script>

    
function validateForm() 
{
var qerror=document.getElementById("qerror");
var payerror=document.getElementById("payerror");



if(document.forms["form"]["paymenttype"].value==""&&
 document.forms["form"]["price"].value==""
 )
{


payerror.innerHTML="<span style='color:red;''>"+" please select payment type. *</span>"
qerror.innerHTML="<span style='color:red;''>"+" please fill the price field. *</span>"



return false;



}else
{


    var pay=document.forms["form"]["paymenttype"].value;

if(pay==""){

    payerror.innerHTML="<span style='color:red;''>"+" Please select payment type.*</span>"
    return false;

}
else
{

    payerror.innerHTML="";

}



var qty=document.forms["form"]["price"].value;

if(qty==""){

    qerror.innerHTML="<span style='color:red;''>"+" price field should not be empty. *</span>"
    return false;

}
else
{

    qerror.innerHTML="";

}




}
}
</script>
<body>
    <div>
        <nav class="navbar navbar-light navbar-expand-md fixed-top shadow navigation-clean" style="background-color: rgb(237,14,0);color: rgb(255,255,255);opacity: 0.80;">
            <div class="container"><a class="navbar-brand" href="" style="font-family: Asap, sans-serif;font-weight: normal;">SAYFT&nbsp;<i class="fa fa-calendar"></i>&nbsp;</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
                <div
                    class="collapse navbar-collapse" id="navcol-1" style="font-size: 17px;font-family: 'Advent Pro', sans-serif;">
                    <ul class="nav navbar-nav ml-auto" style="color: rgb(255,255,255);">
                        <li class="nav-item" role="presentation"><a class="nav-link" href="" style="color: rgb(255,255,255);font-weight: normal;font-family: Asap, sans-serif;">Back</a></li>
                        <li class="nav-item" role="presentation"></li>
                    </ul>
            </div>
    </div>
    </nav>
    </div>
    <div class="row login-form">
        <div class="col-md-4 offset-md-4" style="padding-top: 131px;">
            <h2 class="text-center shadow-sm form-heading" style="font-family: Asap, sans-serif;font-weight: normal;color: rgb(240,62,51);">Pay outstanding amount&nbsp;<i class="fa fa-money"></i></h2>
            <form class="text-white border-white shadow custom-form" name="form" onsubmit="return validateForm();"  method="post">
                <div class="col">
                    <h1 style="font-size: 16px;font-family: Asap, sans-serif;color: rgb(240,62,51);font-style: italic;">Provide Details</h1>
                    <hr style="background-color: #f03e33;">
                </div>
                <div class="form-group"><input class="form-control"  name="price" id="num1" type="number" min="0.00" max="100000.00" step="0.01" placeholder="Price"><span id="qerror"></div>
                <div class="form-group"><select class="form-control" value="<?php echo $data['payment_type']?>" name="paymenttype" id="paymenttype" style="font-family: Asap, sans-serif;">
                <optgroup label="Payment Type">
                    <option value="" selected="">Select Payment Type</option>
                <option value="EFT">EFT</option>
              
            </optgroup>
        </select><span id="payerror"></span></div>
                
                    
    
        <div class="col">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><button class="btn btn-primary border-white" type="submit" name="process" value="process" style="background-color: rgb(240,62,51);color:white;">Process&nbsp;<i class="fa fa-spinner"></i></button></td>
                            <td><a class="btn btn-primary border-white" href="bookings.php" type="button" style="background-color: rgb(240,62,51);color:white;"><i class="fa fa-chevron-left"></i></a></td>
                        </tr>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
        </form>

    </div>
    </div>
  
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/login-full-page-bs4.js"></script>
    <script src="assets/js/login-full-page-bs4-1.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script src="assets/js/Swipe-Slider-9.js"></script>
   
</body> 


</html>
