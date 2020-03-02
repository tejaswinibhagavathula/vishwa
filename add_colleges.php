<?php 
include_once('function/function.php');
include('includes/header.php');
include('includes/sidebar.php');
include('includes/top_navigation.php');
include('includes/connection.php');
error_reporting(1);
$insert=new connection;
$select = new connection;
$update = new connection;
if(isset($_POST['submit'])){
	 $cname = $_POST['cname'];
	 $cemail = $_POST['cemail'];
	 $cphone = $_POST['cmobile'];
	 $cstate = $_POST['state'];
	 $caddress = $_POST['address'];
	 $ccity = $_POST['city'];
	 $cpincode = $_POST['pincode'];
	 $cpassword = $_POST['cmobile'];
	 $status =1;
	 if(isset($_FILES['clogo']['name'])){
		$a=explode('.',$_FILES['clogo']['name']);
		$b=end($a);
		$file=time().'.'.$b;
		$target=unlink_gallry().'/'.$file;
		$status=move_uploaded_file($_FILES['clogo']['tmp_name'],$target);
		$logo=$file;
	}
	$where = ' email="' . $cemail . '"';
	$result = $select->email_exists('colleges', $where);
	$where2 = ' mobile="' . $cphone . '"';
	$result2 = $select->mobile_exists('colleges', $where2);
	$college_detail='';
	if($result){
		$college_detail = '<span class="notsuccess" style="color:red;text-align:center;">Email\'s are already in use!</span>';
    }
	elseif($result2){
		$college_detail = '<span class="notsuccess" style="color:red;text-align:center;">Mobile\'s are already in use!</span>';
	}else{
	  $col=array('cname','email','mobile','cpass','state','city','address','pincode','clogo','status');
	  $val=array($cname,$cemail,$cphone,$cpassword,$cstate,$ccity,$caddress,$cpincode,$logo,$status);
	  $id=$insert->insert('colleges',$col,$val);
	  $college_detail = '<span class="success" style="color:green;text-align:center;">Successfully registered</span>';
	  if($id){
		$data='{ 
			"action":"vishwa_college_register",
			"email":"'.$cemail.'",
			"phone":"'.$cphone.'",
			"pass":"'.$cpassword.'",
			"fname":"'.$cname.'",
			"state":"'.$cstate.'",
			"city":"'.$ccity.'",
			"address":"'.$caddress.'",
			"postal":"'.$cpincode.'",
			"picture":"'.$logo.'",
			"status":"'.$status.'"
		}';
		//echo $data;die;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => curl_url()."api/vishwa",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 1000,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);
		$login = json_decode($response,true);
		$err = curl_error($curl);
		curl_close($curl);	
		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
			if($login['user'][0]['action'] == "success"){
			$uid=$login['user'][0]['uid'];
			if($uid){
				$where=' id="'.$id.'"';
			 	$updateData=array('uid'=>"'".$uid."'");
			  	$sql=$update->update('colleges',$updateData,$where);
				$cname = '';$cemail = '';$cphone = '';$cstate = '';$caddress = '';$ccity = '';$cpincode = '';
			}
		}
		else{
		$college_detail = '<h5 class="alert alert-warning" style="padding-left: 30px;">'.$login['user'][0]['msg'].'</h5>';
		}
	  }
	}
  }
}
?>

<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Add Colleges</h3>
        <?php if($college_detail){ echo $college_detail; }?>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_content">
          <br />
          <form  method="post" name="register" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="first-name">College Name <span style="color:red;">*</span> :</label>
                  <input type="text" id="fname" class="form-control" name="cname" data-parsley-trigger="change"  value="<?php echo $cname;?>" required />
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="mobile-number">Mobile No <span style="color:red;">*</span> :</label>
                  <input type="tel" id="cmobile" class="form-control" name="cmobile" data-parsley-trigger="change" value="<?php echo $cphone;?>" maxlength="10" onkeypress="return isNumberKey(event);" required/>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="email">Email <span style="color:red;">*</span> :</label>
                  <input type="email" id="email" class="form-control" name="cemail" data-parsley-trigger="change" style="text-transform: lowercase;" value="<?php echo $cemail;?>" required/>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="logo">College Logo <span style="color:red;">*</span> :</label>
                  <input type="file" id="clg_logo" class="form-control" name="clogo" data-parsley-trigger="change" style="text-transform: lowercase;" required/>
                </div>
              </div>
             <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="address">Address <span style="color:red;">*</span> :</label>
                  <textarea name="address" id="address" rows="2" class="form-control" style="text-transform: capitalize;" required><?php echo $caddress;?></textarea>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label class="control-label" for="state">State <span style="color:red;">*</span> :</label>
                  <select name="state" class="form-control" data-parsley-trigger="change" id="states" required>
                  <?php if($cstate){
							echo '<option value="'.$cstate.'">'.$cstate.'</option>';
						} else{
							echo '<option value="">Select State</option>';
						}?>
                    <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                    <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                    <option value="Assam">Assam</option>
                    <option value="Bihar">Bihar</option>
                    <option value="Chandigarh">Chandigarh</option>
                    <option value="Chhattisgarh">Chhattisgarh</option>
                    <option value="Dadra and Nagar Haveli">Dadra and Nagar Haveli</option>
                    <option value="Daman and Diu">Daman and Diu</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Goa">Goa</option>
                    <option value="Gujarat">Gujarat</option>
                    <option value="Haryana">Haryana</option>
                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                    <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                    <option value="Jharkhand">Jharkhand</option>
                    <option value="Karnataka">Karnataka</option>
                    <option value="Kerala">Kerala</option>
                    <option value="Lakshadweep">Lakshadweep</option>
                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Manipur">Manipur</option>
                    <option value="Meghalaya">Meghalaya</option>
                    <option value="Mizoram">Mizoram</option>
                    <option value="Nagaland">Nagaland</option>
                    <option value="Orissa">Orissa</option>
                    <option value="Pondicherry">Pondicherry</option>
                    <option value="Punjab">Punjab</option>
                    <option value="Rajasthan">Rajasthan</option>
                    <option value="Sikkim">Sikkim</option>
                    <option value="Tamil Nadu">Tamil Nadu</option>
                    <option value="Tripura">Tripura</option>
                    <option value="Uttaranchal">Uttaranchal</option>
                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                    <option value="West Bengal">West Bengal</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
             <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="clg_name"> City <span style="color:red;">*</span> :</label>
                  <input type="text" id="city_name" class="form-control" name="city" data-parsley-trigger="change" style="text-transform: capitalize;" value="<?php echo $ccity;?>" required/>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="form-group">
                  <label for="clg_name"> Pincode <span style="color:red;">*</span> :</label>
                  <input type="text" id="pincode" class="form-control" name="pincode" data-parsley-trigger="change" style="text-transform: capitalize;" maxlength="6" onkeypress="return isNumberKey(event);" value="<?php echo $cpincode;?>" required/>
                </div>
              </div>
              
            </div>
            
            <div class="ln_solid"></div>
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="submit" class="btn btn-success pull-left" value="Add College" name="submit" id="btnSubmit">
              </div>
            </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function isNumberKey(evt)
	  {
		 var charCode = (evt.which) ? evt.which : event.keyCode
		 if (charCode != 45  && charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
	
		 return true;
	  }
</script>
<?php include('includes/footer.php'); ?>
