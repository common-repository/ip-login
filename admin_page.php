<? if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
<h1>IP Login Options</h1>
	<div class="card ipl_options">
	<?		if (ipl_get_ip()==false) {
			?><p style="text-align: center;line-height: 30px;padding: 5px;background: rgb(175, 0, 0);color: white;">Your IP can't be determined. You will not use this plugin, sorry...</p><?
			return;
			}
	?>
	<p><strong>Your IP: </strong><? echo(ipl_get_ip());?> <? if (ipl_is_ip_authorized(ipl_get_ip())) echo '<span class="good_ip"> IP Authorized </span>';?> </p><br>
	<form method="POST" id="ipl_remove_ip">
	<input type="hidden" id="nonce" name="nonce" value="<? echo wp_create_nonce('ipl_nonce');?>">
	<input type="hidden" id="delip" name="delip" value="">
	<span class="error"></span>
	<table style="width:190px" id="ip_table">
	   <tr>
		<th>Authorized IPs</th>
		<th></th> 
	  </tr>

	<? if (count($authorized)>0 AND $authorized !=''){
	  foreach ($authorized as $ip):?>
	<? if (strlen($ip)>1){ ?>
	  <tr>
		<td class="ip"><? echo $ip;?></td>
		<td><input type="checkbox" name="<? echo $ip;?>" value="false"></td> 
	  </tr>
	<? } ?>
	<?  endforeach;
	 } else {
	?>
	  <tr>
		<td class="ip">No IP is defined.<br>Use field bellow to add your first IP.</td>
		<td></td> 
	  </tr>	 
	<?	 
	 }
	?>
	</table>
	<br>
	<? if ($authorized !=''){?>
	<input type="submit" value="Delete Selected" class="button button-primary button-large">
	<? } ?>
	</form>

	<br>
	<form method="POST" id="ipl_add_ip">
		<h4>Add IP: <span class="error"></span></h4>
		<input type="text" name="add_ip" id="add_ip">
		<input type="hidden" id="nonce" name="nonce" value="<? echo wp_create_nonce('ipl_nonce');?>">
		<br>
		<input type="submit" value="Add IP" class="button button-primary button-large">
	</form>

	</div>
</div>
<script>
function ValidateIPaddress(ipaddress) {
	if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipaddress)) {
		return (true)
	  } else {
		return (false)
	}
}
// del submit function
jQuery("#ipl_remove_ip").submit(function(event){
	
	var ip='';
    jQuery( "#ip_table td.ip " ).each(function( index ) {
	  if (jQuery( this ).next().children('input').is(':checked')) {
		  
		if (ip=='') {
			ip =jQuery( this ).text();
		} else {
			ip = ip + '|' + jQuery( this ).text();
		}
	  }
	});
	if (ip=='') {
		jQuery('#ipl_remove_ip span.error').text('No IP is checked!');
		return false;
	}
	jQuery ('#ipl_remove_ip #delip').val(ip);

	
});
// add submit function
jQuery("#ipl_add_ip").submit(function(event){
	//event.preventDefault();
	ip=jQuery( "#ipl_add_ip input").val();
	if (ValidateIPaddress(ip)==false) {
		jQuery('#ipl_add_ip span.error').text('IP addres is invalid!');
		return false;
	} else {
		//jQuery('#ipl_add_ip span#error').text('IP is valid');
	}	
});
</script>