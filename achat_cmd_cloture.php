<?
include("fonction.php");
if (!d_ok(506)){header("location: accueil.php");exit;}

$_SESSION['en_cour']="achat_cmd_cloture.php";

$j_interne = dbtodata("select id , concat(nom,' ',prenom) from interne; ");


if($save==1)
{

for($i=0;$i < count($id_l);$i++)
	{
	if($chk[$i]<>1){$qte_facture[$i]=0;}
	$vf = new valid_form ;
	$vf->add("qte_facture", "if('".$qte_facture[$i]."'> qte_recue ,qte_recue,'".$qte_facture[$i]."')",0,1);
	$vf->add("etat", "if(".$qte_facture[$i]." + qte_annule >= qte,10,if(qte_recue + qte_annule >= qte,9,7))",0,1);
	$vf->update("achat_ligne","where id = ".$id_l[$i].";","",1);
	$vf->log(__FILE__,__LINE__,DL_1);
	}

for($i=0;$i < count($id_cmd);$i++)
	{
	$clos=nombre_de("select count(*) from achat_ligne where id_cmd = '".$id_cmd[$i]."' and etat not in (3,10,11)")==0;
	$vf = new valid_form ;
	$vf->add("compta_notes", $compta_notes[$i]);
	$vf->add("compta_motif", $compta_motif[$i]);
	$vf->add("m_facture", $m_facture[$i]);
	if($clos)$vf->add("etat", 4);
	
	$vf->update("achat_cmd"," where id = ".$id_cmd[$i]."; ","",1);
	$vf->log(__FILE__,__LINE__,DL_1);
	}
}


	
if ($mode == "9")
{
$r_etat=1;
$r_ref="";
$r_des="";
$r_login="";
$r_cmd="";
$r_aff="";
$r_db1="";
$r_db2="";
$r_dl1="";
$r_dl2="";
$mode="1";
}



if ($mode == "1"){
$_SESSION['achat_cmd_cloture_etat']=$r_etat;
$_SESSION['achat_cmd_cloture_ref']=$r_ref;
$_SESSION['achat_cmd_cloture_des']=$r_des;
$_SESSION['achat_cmd_cloture_login']=$r_login;
$_SESSION['achat_cmd_cloture_cmd']=$r_cmd;
$_SESSION['achat_cmd_cloture_aff']=$r_aff;
$_SESSION['achat_cmd_cloture_ext']=$r_ext;
$_SESSION['achat_cmd_cloture_db1']=$r_db1;
$_SESSION['achat_cmd_cloture_db2']=$r_db2;
$_SESSION['achat_cmd_cloture_dl1']=$r_dl1;
$_SESSION['achat_cmd_cloture_dl2']=$r_dl2;
$p_en=1;
}

if($p_en > 0)$_SESSION["achat_cmd_p_en"]=$p_en;
if($mode > 0){$p_en=1;}else{$p_en=$_SESSION["achat_cmd_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["achat_cmd_p_en"]=$p_en;

$achat_cmd_req="";

$r_etat=$_SESSION['achat_cmd_cloture_etat'];
$r_ref=$_SESSION['achat_cmd_cloture_ref'];
$r_des=$_SESSION['achat_cmd_cloture_des'];
$r_login=$_SESSION['achat_cmd_cloture_login'];
$r_cmd=$_SESSION['achat_cmd_cloture_cmd'];
$r_aff=$_SESSION['achat_cmd_cloture_aff'];
$r_ext=$_SESSION['achat_cmd_cloture_ext'];
$r_db1=$_SESSION['achat_cmd_cloture_db1'];
$r_db2=$_SESSION['achat_cmd_cloture_db2'];
$r_dl1=$_SESSION['achat_cmd_cloture_dl1'];
$r_dl2=$_SESSION['achat_cmd_cloture_dl2'];

if (is_array($r_etat)){$achat_cmd_req .= " and ac.etat in (".tabtosql($r_etat).") ";}else{$achat_cmd_req .= " and ac.etat in (1,2,3,4) ";}
if ($r_ref <> "")$achat_cmd_req .= " and al.ref like '%$r_ref%' ";
if ($r_des <> "")$achat_cmd_req .= " and al.des like '%$r_des%' ";
if ($r_login > 0)$achat_cmd_req .= " and al.id_login = '$r_login' ";
if ($r_cmd > 0)$achat_cmd_req .= " and ac.id in ( $r_cmd ) ";
if ($r_aff > 0)$achat_cmd_req .= " and al.id_affaire = '$r_aff' ";
if ($r_ext > 0)$achat_cmd_req .= " and al.id_service = '$r_ext' ";


if((isdf($r_db1))and(isdf($r_db2))){$achat_cmd_req .= " and (( ac.date >= '".dftoda($r_db1)."')and ( ac.date <= '".dftoda($r_db2)."')) ";}
else if(isdf($r_db1)){$achat_cmd_req .= " and ac.date = '".dftoda($r_db1)."' ";}

if((isdf($r_dl1))and(isdf($r_dl2))){$achat_cmd_req .= " and (( ac.d_liv_prev >= '".dftoda($r_dl1)."')and ( ac.d_liv_prev <= '".dftoda($r_dl2)."')) ";}
else if(isdf($r_dl1)){$achat_cmd_req .= " and ac.d_liv_prev = '".dftoda($r_dl1)."' ";}

$_SESSION['achat_cmd_cloture_req']=$achat_cmd_req;

if($_SESSION['achat_cmd_cloture_trier']==''){$_SESSION['achat_cmd_cloture_trier']='ac.id ';}
if(isset($trier)){$_SESSION['achat_cmd_cloture_trier']=$trier;}

if($_SESSION['achat_cmd_cloture_ordre']==''){$_SESSION['achat_cmd_cloture_ordre']='desc ';}
if(isset($ordre)){$_SESSION['achat_cmd_cloture_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['achat_cmd_cloture_trier'];
$tf->ordre_ec=$_SESSION['achat_cmd_cloture_ordre'];
$tf->page_ec="achat_cmd_cloture.php";

$retour = "achat_cmd.php";

$d_compta = d_ok(506);

$page = new page;
$page->head("Cloture des commandes");
$page->body();
$page->entete("Cloture des commandes");
$page->add_button(1,0);
$page->add_button(2,1,$retour);
$page->add_button(3,0);
$page->add_button(0,2);
$page->add_button(4,1,"validation()","Enregistrer");
$page->add_button(0,2);
$page->add_button(27,1,"achat_cmd_cloture.php?print=1","Version Excel");
$page->fin_entete();
$page->datescript();

if($print <> 1)
{

?>


<script LANGUAGE="JavaScript">
fois=0;
function validation()
{
if(fois==0){fois++;document.formulaire.submit();}
}
</script>



<style type="text/css" media="all">
	/* Ce style CSS ne dois pas être enlevé, sinon les divs ne se cacherons pas ... */
	.cachediv {
		display: none;
	}
</style>
<!-- Script créé par KevBrok ;-) -->
<script type="text/javascript">
	/*
	* Montre / Cache un div
	*/
	function DivStatus( divID )
		{
		Pdiv = document.getElementById( divID );
		Pdiv.className = ( Pdiv.className == 'cachediv' ) ? '' : 'cachediv';
			
		}
</script>




<form style="position:relative;z-index:1;" method="post" name="formulaire1" action="achat_cmd_cloture.php?mode=1"  target="_self">

<table  class=forumline cellSpacing=1 cellPadding=0 width="100%" border=0 align=center>
<tr >
<td class= "m3" height=30 >
N° CDE :
<input id="button2"  type=text name="r_cmd" size=10 maxlength=15 value="<? echo $r_cmd;?>"  >
&nbsp;

Ref :
<input id="button2"  type=text name="r_ref" size=10 maxlength=15 value="<? echo $r_ref;?>"  >
&nbsp;

Des. :
<input id="button2"  type=text name="r_des" size=10 maxlength=15 value="<? echo $r_des;?>"  >
&nbsp;


Demandeur :
<?
echo liste_db("select id , nom from achat_demandeur",$r_login,"r_login",'','<option value=""></option>' );
?>
&nbsp;&nbsp;


<?
echo liste_ms($j_achat_cmd_etat,$r_etat,"r_etat", "Etat");
?>
&nbsp;&nbsp;

Affaire :
<input id="button2"  type=text name="r_aff" size=6 maxlength=10 value="<? echo $r_aff;?>"  >
&nbsp; &nbsp;
</td>
<td class= "m3" rowspan=2>
&nbsp; &nbsp; &nbsp; &nbsp;<input id=button3 type=submit value="Filtrer"> &nbsp;|&nbsp; <input id=button3 type=button value="Clear" onclick="document.location.href='achat_cmd_cloture.php?mode=9';">

</td>
</tr>
<tr>
<td class= "m3" height=30>
Fournisseur :
<?
echo liste_db("select e.id , e.nom from externe_service as e left join achat_cmd as ac on e.id = ac.id_service where ac.id_service is not null group by e.id order by e.nom",$r_ext,"r_ext" ,'style="width:150;"','<option value=""></option>');
?>
&nbsp; &nbsp; &nbsp;

Date de création :&nbsp; &nbsp;
<input   onclick="return showCalendar('sel1', '%d/%m/%Y');"  id=sel1  type=text name="r_db1" size=10 maxlength=10 value="<? echo $r_db1;?>"  ><? echo calendar('sel1');?>
&nbsp; &nbsp; au &nbsp; &nbsp;
<input  onclick="return showCalendar('sel2', '%d/%m/%Y');"  id=sel2   type=text name="r_db2" size=10 maxlength=10 value="<? echo $r_db2;?>"  ><? echo calendar('sel2');?>
&nbsp; &nbsp; &nbsp; &nbsp;

Date de livraison prévue :&nbsp; &nbsp;
<input  onclick="return showCalendar('sel3', '%d/%m/%Y');"  id=sel3  type=text name="r_dl1" size=10 maxlength=10 value="<? echo $r_dl1;?>"  ><? echo calendar('sel3');?>
&nbsp; &nbsp; au &nbsp; &nbsp;
<input  onclick="return showCalendar('sel4', '%d/%m/%Y');"  id=sel4   type=text name="r_dl2" size=10 maxlength=10 value="<? echo $r_dl2;?>"  ><? echo calendar('sel4');?>
&nbsp;

</td>
</tr>

</table>
</form>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="js/overlib.js"></script>
<script language="javascript">
function sel_all_cb(className)
{
	var cb = document.getElementsByClassName(className);
	for(i in cb)
	{
		cb[i].click();
	}
}
</script>

<?php
}
?>

<form name="formulaire"  method="post" action="achat_cmd_cloture.php"   target="_self">
<input type=hidden name="save" value="1">

<?

$req2="select count(distinct ac.id) ,count(*), sum(al.m_ht) , sum(al.m_ttc) from achat_cmd as ac left join achat_ligne as al on al.id_cmd = ac.id where 1 ".$_SESSION['achat_cmd_cloture_req']."  ";
$res2=my_query($req2);
$ligne2=mysql_fetch_array($res2);

?>
<table class=forumline cellSpacing=1 cellPadding=1 width="100%" border=0 marginwidth=0 marginheight=0 align=center >
<tr>
<?php
if($print <> 1){
?>
<td class= "m3" width=2% >
	<a href="javascript:sel_tt();" >BAP</a><br/>
	<input type="checkbox" onchange="sel_all_cb('sel_all_bap');" />
</td>
<td class= "m3" width=2% ><? $tf->aff("ac.etat","Etat");?></td>
<td class= "m3" width=2% height=23>PDF</td>
<?php } ?>

<td class= "m3" width=4% ><? $tf->aff("ac.id","N° CDE");?></td>
<td class= "m3" width=1% >NB</td>
<td class= "m3" width=4% ><? $tf->aff("ac.date","Date"); ?></td>
<td class= "m3" width=20% ><? $tf->aff("e.societe","Fournisseur");?></td>
<td class= "m3" width=5% ><? $tf->aff("ac.m_ht","Mt HT");echo "<br>".$ligne2[2] ;?></td>
<td class= "m3" width=5% >Mt Réception</td>
<td class= "m3" width=6% ><? $tf->aff("ac.m_facture","MT facturé");?></td>
<td class= "m3" width=10% ><? $tf->aff("ac.compta_motif","Motif");?></td>
<td class= "m3" width=30% ><? $tf->aff("ac.compta_notes","Notes");?></td>
<td class= "m3" width=2% ><? $tf->aff("ac.compta_liaison","Liée");?></td>
<td class= "m3" width=5% ><? $tf->aff("ac.type","Type");?></td>
</tr>

<?
$ic=0;
$il=0;
$req="select ac.* , e.nom as societe 
from achat_cmd as ac 
left join externe_service as e on ac.id_service = e.id 
left join achat_ligne as al on al.id_cmd = ac.id 
where 1 ".$_SESSION['achat_cmd_cloture_req']." group by ac.id order by ".$_SESSION['achat_cmd_cloture_trier']." ".$_SESSION['achat_cmd_cloture_ordre']." , ac.id desc ";
//var_dump($req);
$res=my_query($req);
$ligne_page=$lpp -4  ;                //ligne par page
$p_pf=20;  			     //page par feuille

$nb_ligne=mysql_num_rows($res);
if ($nb_ligne==0){echo "<br>Aucun enregistrement trouvé<br>";exit;}
$prem_ligne=(($p_en - 1) * $ligne_page);
mysql_data_seek($res,0);
mysql_data_seek($res,$prem_ligne);


while ($ligne=mysql_fetch_array($res))
{
	$cid="class= \"cel1\"";
	if($ligne["cloture_login"] > 0){$clot_par="title = 'Cloturée le ".datodf($ligne["cloture_date"])." par ".$j_interne[$ligne["cloture_login"]]."'";}else{$clot_par="";}
	$etat=$ligne["etat"]-1;
	if ($etat == 2){$etat = 1;}
	else if ($etat == 1){$etat = 2;}
	$bap='';
	if(($ligne["etat"]< 2)and($ligne["etat"]> 3)){$bap = ' disabled ';}
	if($ligne["etat"]==4){$bap .= ' checked ';}
	
	$req2="select al.* , a.type , acat.id_compte , if(al.remise > 0,(al.qte_recue * p_u)-(((al.qte_recue * p_u) * remise) / 100),(al.qte_recue * p_u)) as mt_ht_r 
	from achat_ligne al
	left join affaire a on al.id_affaire = a.id
	left join article_cat acat on al.cat = acat.id
	where al.id_cmd = '".$ligne["id"]."' 
	order by al.id_cmd asc , al.etat desc ,  al.d_besoin asc ";
	$res2=my_query($req2);
	$nb_ligne2=mysql_num_rows($res2);
	$sel_all .= "document.getElementById('cmd".$ligne["id"]."').className='cachediv';\n";
	$dsel_all .= "document.getElementById('cmd".$ligne["id"]."').className='';";
	if($ligne["compta_liaison"]>0){$liee=1;$title="Commande enfant de ".nombre_de("select cmd_bis from achat_ligne where id_cmd = ".$ligne["id"]." and cmd_bis > 0");}
	else
		{
		$res3=my_query("SELECT * from achat_ligne where cmd_bis = ".$ligne["id"]." ");
		$nb_ligne3=mysql_num_rows($res3);
		if($nb_ligne3>0)
			{
			$liee=1;
			$title="Commande liée : ";
			while($row3 = mysql_fetch_array($res3))
				{
				$title.= " ".$row3["id_cmd"];
				}
			}
			else
			{
			$liee=0;
			$title="";
			}
			

		}
	?>

	<tr >
	<?php
	if($print <> 1)
	{
	?>
	<td align=center <? echo $cid;?>  ><input type=hidden name="id_cmd[<? echo $ic;?>]" value="<? echo $ligne["id"];?>"><input type=checkbox id="dup_<? echo $ligne["id"];?>" class="sel_all_bap" name="bap[<? echo $ic;?>]" value="1" <? echo $bap;?> onclick="sel('dup_<? echo $ligne["id"];?>','dup<? echo $ligne["id"];?>',<? echo $nb_ligne2;?>);"></td>
	<td align=center <? echo $cid;?>  ><img src="images/statut<? echo $etat;?>.gif" title="<? echo $j_achat_cmd_etat[$ligne["etat"]] ?>"></td>
	<td align=center <? echo $cid;?>  ><a class=b href="./data/achat_cmd/cmd_<? echo format_0($ligne["id"],7);?>.pdf" target="_blank"><img border=0 src="images/pdf.gif"></a></td>
	<?php } ?>
	
	<td align=center <? echo $cid;?>  ><a class=b href="javascript:DivStatus( '<? echo 'cmd'.$ligne["id"];?>' );" ><? echo format_0($ligne["id"],7);?></a></td>
	<td align=center <? echo $cid;?>  ><? echo $nb_ligne2;?></td>
	<td align=center <? echo $cid;?>  ><? echo datodf($ligne['date']);?></td>
	<td align=center <? echo $cid;?>  ><? echo $ligne["societe"];?></td>
	<td align=center class="cel2"  ><? echo $ligne["m_ht"];?></td>
	<td align=center <? echo $cid;?>  ><? echo $ligne["m_reception"];?></td>
	<td align=center <? echo $cid;?>  ><input size=10 type=text name="m_facture[]" value="<? echo $ligne["m_facture"];?>" ></td>
	<td align=center <? echo $cid;?>  ><? echo liste_d2($j_achat_compta_motif,$ligne["compta_motif"],"compta_motif[]");?></td>
	<td align=center <? echo $cid;?>  ><input size=50 type=text name="compta_notes[]" value="<? echo $ligne["compta_notes"];?>" ></td>
	<td align=center <? echo $cid;?>  ><? if($liee==1){echo '<img title="'.$title.'" src="images/lien.gif">';}else{echo "&nbsp;";}?></td>
	<td align=center <? echo $cid;?>  ><? echo $j_achat_cmd_type[$ligne["type"]];?></td>
	</tr>

	<tr id="cmd<? echo $ligne["id"];?>" <?php if($print <> 1){ ?> class='cachediv' <?php } ?> >
	<td colspan=14 >
	<table cellSpacing=1 cellPadding=1 width="100%" border=0 marginwidth=0 marginheight=0 align=center >

		<TR class="cel4">
			<?php 
			if($print <> 1)
			{
			?>
			<TD width="2%"></TD>
			<TD width="2%"></TD>
			<?php
			}
			?>
			<TD width="6%" align=center>Affaire</TD>
			<TD width="6%" align=center>Type</TD>
			<TD width="6%" align=center>Date reception</TD>
			<TD width="6%" align=center>Compte comptable</TD>
			<TD width="7%" align=center>Ref</TD>
			<TD width="28%" align=center>Désignation</TD>
			<TD width="6%" align=center>Unitée</TD>
			<TD width="6%" align=center>Qté Facturée</TD>
			<TD width="6%" align=center>Qté recue</TD>
			<TD width="6%" align=center>Qté commandée</TD>
			<TD width="6%" align=center>P.U</TD>
			<TD width="6%" align=center>Mt HT sur reception</TD>
			<TD width="6%" align=center>Mt HT sur commande</TD>
			<TD width="6%" align=center>Mt TTC sur commande</TD>
			<?php 
			if($print <> 1)
				{
				?>
				<TD width="2%" align=center></TD>
				<TD width="2%" align=center>Etat</TD>
				<?php
				}
			?>
		</TR>

	<?


	$l=0;
	while ($ligne2=mysql_fetch_array($res2))
		{

		$cid="class= \"cel2\"";
		$info = '';
		$info_img = 'grp.png';
		$info .= '<b>&nbsp; Demandeur</b> : '.$j_interne[$ligne2["id_login"]];
		if($ligne2["id_login"]<>$ligne["id_destinataire"])$info .= '<br>&nbsp; <b>Destinataire</b> : '.$j_interne[$ligne2["id_destinataire"]];
		
		if(($ligne2["id_affaire"]>0)and(($j_net_n==1)or($j_net_n==6)or($j_net_n==7)or($j_net_n==10)))
			{
			$dep = nombre_de("select departement from affaire where id = ".$ligne2["id_affaire"]);
			$info .= '<br>&nbsp; <b>Département</b> : '.$j_departement[$dep];
			}
		
		
		if($ligne2["observation"]<>''){$info .= '<br>&nbsp; <b>Observation</b> : '.$ligne2["observation"];$info_img = 'cell_layout.png';}
		if($ligne2["commentaire"]<>''){$info .= '<br>&nbsp; <b>Commentaire</b> : '.$ligne2["commentaire"];$info_img = 'cell_layout.png';}
		if($ligne2["budget"]>0){$info .= '<br>&nbsp; <b>Budget</b> : '.$ligne2["budget"];$info_img = 'cell_layout.png';}
		if($ligne2["modif"]>0){$info_img = 'history.png';}
		if($ligne2["chiffrage"]==1){$info .= '<br>&nbsp; <b>Chiffrage en cours.</b> ';$info_img = 'cle.png';}
		if($ligne2["etat"]==1){$info .= '<br>&nbsp; <b>En attente de validation de </b>'.$j_interne[$ligne2["id_validation"]];$info_img = 'cle.png';}
		if(($ligne2["etat"]==11)or($ligne2["etat"]==3))
			{
			$info .= '<br>&nbsp; <b>Annulé par </b>'.$j_interne[$ligne2["id_validation"]];
			if($ligne2["motif"]<>'')$info .= '<br>&nbsp; <b>Motif </b>:  '.$ligne2["motif"];
			$info_img = 'trash.gif';
			}
		if($ligne2["etat"]==10){$ch="checked";}else{$ch="";}
		if(($ligne2["etat"]>=7)and($ligne2["etat"]<11)){$v='1';}else{$v='0';}
		if($ligne2["qte_facture"] > 0){$qte_facture=$ligne2["qte_facture"];$ch="checked";}else{$qte_facture=$ligne2["qte_recue"];}
		
		$c='<input id="dup'.$ligne["id"].'_'.$l.'" '.$cid.' '.$ch.' type="checkbox" name="chk['.$il.']" value="'.$v.'" '.$bap.' ><input type=hidden name="id_l['.$il.']" value="'.$ligne2["id"].'">';
		if($nb_ligne2==($l + 1)){$item="images/menu/Item_Last.gif";}else{$item="images/menu/Item.gif";}
		if($ligne2["cmd_bis"]>0){$info .= '<br>&nbsp; <b>Commande liée : </b> '.$ligne2["cmd_bis"];$info_img = 'lien.gif';}

		?>

		<tr>
		<?php 
		if($print <> 1)
			{
			?>
			<td class= "cel1" ><img src="<? echo $item;?>"></td>
			<td align=center <? echo $cid;?>  ><? echo $c;?></td>
			<?php
			}
		?>
		<td align=center <? echo $cid;?> title="Affaire" ><? echo $ligne2["id_affaire"];?></td>
		<td align=center <? echo $cid;?> title="<? echo $j_affaire_type[$ligne2["type"]];?>" ><? echo $j_affaire_type_abr[$ligne2["type"]];?></td>
		<td align=center <? echo $cid;?> title="Date reception" ><? echo $ligne2["d_liv_reel"];?></td>
		<td align=center <? echo $cid;?> title="Compte comptable" ><? echo $ligne2["id_compte"];?></td>
		<td align=center <? echo $cid;?> title="Ref" ><? echo $ligne2["ref"];?></td>
		<td align=center <? echo $cid;?> title="Désignation" ><div style="overflow:hidden;width:100%;height:100%;"><? echo $ligne2["des"];?></div></td>
		<td align=center <? echo $cid;?> title="Unitée" ><? echo $j_unite[$ligne2["unite"]];?></td>
		<td align=center <? echo $cid;?> title="Qté Facturée" ><input name="qte_facture[<? echo $il;?>]" type=text size=5 value="<? echo ($qte_facture*1);?>"></td>
		<td align=center class="cel1" title="Qté recue" ><? echo ($ligne2["qte_recue"]*1);?></td>
		<td align=center <? echo $cid;?> title="Qté commandée" ><? echo ($ligne2["qte"]*1);?></td>
		<td align=center <? echo $cid;?> title="P.U" ><? echo $ligne2["p_u"];?></td>
		<td align=center class="cel1" title="Mt HT sur reception" ><? echo round($ligne2["mt_ht_r"],2);?></td>
		<td align=center <? echo $cid;?> title="Mt HT sur commande" ><? echo $ligne2["m_ht"];?></td>
		<td align=center <? echo $cid;?> title="Mt TTC sur commande" ><? echo $ligne2["m_ttc"];?></td>
		<?php 
		if($print <> 1)
			{
			?>
			<td align=center <? echo $cid;?>  ><img style="cursor:pointer;" onmouseover="return overlib('<? echo addslashes($info);?>', CAPTION,'&nbsp;<img src=images/info.png > Information' );" onmouseout="return nd();" src="images/<? echo $info_img;?>" ></td>
			<td align=center <? echo $cid;?>  ><img style="cursor:pointer;" title="<? echo $j_achat_etat[$ligne2["etat"]];?>" src="images/progress<? echo $ligne2["etat"];?>0.gif"></td>
			<?php
			}
		?>
		</tr>
		<?
		$il++;
		$l++;

		}
	?>
	</table>
	</td>
	</tr>
	<?
	$ic++;
	if ($ic == $ligne_page){break;}
}
echo '</table></form>';

echo bar("achat_cmd_cloture.php",'',$p_en,$nb_ligne,$ligne_page);

?>

<script LANGUAGE="JavaScript">


function sel(id_p,id,nb)
{
var select = document.getElementById(id_p).checked;
if(select)
	{
	for(var i = 0; i < nb ; i++)document.getElementById(id + '_' + i).checked=true;
	}
	else
	{
	for(var i = 0; i < nb ; i++)document.getElementById(id + '_' + i).checked=false;
	}
}


var selt = 1;

function sel_tt()
{
if(selt == 0)
	{
	selt=1;
	sel_all();
	}
	else
	{
	selt=0;
	dsel_all();
	}
}

function sel_all()
{
<?
echo $sel_all;
?>
}
function dsel_all()
{
<?
echo $dsel_all;?>
}
</script>

</center>
<?
echo pied_page();
?>
