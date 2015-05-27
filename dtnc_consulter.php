<?
include("fonction.php");
if (!d_ok(1170)){header("location: accueil.php");exit;}
$_SESSION['en_cour']="dtnc_consulter.php";



if($pere>0)$_SESSION['dtnc_pere']=$pere;

$j_soc=dbtodata("select id,nom from externe_service");

if(($del_id>0)and(d_ok(1172)))
	{
	del_visit(__FILE__,__LINE__,DL_1,"dtnc","where id = '$del_id'");
	}
	
if ($mode == "9")
{
$r_id="";
$r_type="0";
$r_uclient="0";
$r_cause="0";
$r_source="0";
$r_sanction_jallais="0";
$r_sanction_client="0";
$r_statut="0";
$r_ref_client="";
$r_designation="";
$r_ref_produit="";
$r_client="";
$mode="1";
}

if (isset($r_tout))$r_tout=1;

if ($mode == "1")
{
$_SESSION['dtnc_consulter_id']=$r_id;
$_SESSION['dtnc_consulter_type']=$r_type;
$_SESSION['dtnc_consulter_uclient']=$r_uclient;
$_SESSION['dtnc_consulter_cause']=$r_cause;
$_SESSION['dtnc_consulter_source']=$r_source;
$_SESSION['dtnc_consulter_sanction_jallais']=$r_sanction_jallais;
$_SESSION['dtnc_consulter_sanction_client']=$r_sanction_client;
$_SESSION['dtnc_consulter_num_affaire']=$r_num_affaire;
$_SESSION['dtnc_consulter_sanction_statut']=$r_statut;
$_SESSION['dtnc_consulter_ref_client']=$r_ref_client;
$_SESSION['dtnc_consulter_imputation']=$r_imputation;
$_SESSION['dtnc_consulter_designation']=$r_designation;
$_SESSION['dtnc_consulter_ref_produit']=$r_ref_produit;
$_SESSION['dtnc_consulter_red']=$r_red;
$_SESSION['dtnc_consulter_anal']=$r_anal;
$_SESSION['dtnc_consulter_type_imput']=$r_type_imput;
$_SESSION['dtnc_consulter_client']=$r_client;
$_SESSION['dtnc_consulter_tout']=$r_tout;




$p_en=1;
}

if($p_en > 0)$_SESSION["dtnc_consulter_p_en"]=$p_en;
if($mode > 0){$p_en=1;}else{$p_en=$_SESSION["dtnc_consulter_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["dtnc_consulter_p_en"]=$p_en;

$dtnc_consulter_req="";

$r_id=$_SESSION['dtnc_consulter_id'];
$r_type=$_SESSION['dtnc_consulter_type'];
$r_uclient=$_SESSION['dtnc_consulter_uclient'];
$r_cause=$_SESSION['dtnc_consulter_cause'];
$r_source=$_SESSION['dtnc_consulter_source'];
$r_sanction_jallais=$_SESSION['dtnc_consulter_sanction_jallais'];
$r_sanction_client=$_SESSION['dtnc_consulter_sanction_client'];
$r_num_affaire=$_SESSION['dtnc_consulter_num_affaire'];
$r_statut=$_SESSION['dtnc_consulter_sanction_statut'];
$r_ref_client=$_SESSION['dtnc_consulter_ref_client'];
$r_imputation=$_SESSION['dtnc_consulter_imputation'];
$r_designation=$_SESSION['dtnc_consulter_designation'];
$r_ref_produit=$_SESSION['dtnc_consulter_ref_produit'];
$r_red=$_SESSION['dtnc_consulter_red'];
$r_anal=$_SESSION['dtnc_consulter_anal'];
$r_type_imput=$_SESSION['dtnc_consulter_type_imput'];
$r_client=$_SESSION['dtnc_consulter_client'];
$r_tout=$_SESSION['dtnc_consulter_tout'];



if ($r_id > 0)$dtnc_consulter_req .= " and id = $r_id ";
if (is_array($r_type))$dtnc_consulter_req .= " and type in (".tabtosql($r_type).") ";
if (is_array($r_uclient))$dtnc_consulter_req .= " and unite_client in (".tabtosql($r_uclient).") ";
if (is_array($r_cause))$dtnc_consulter_req .= " and id_cause in (".tabtosql($r_cause).") ";
if (is_array($r_source))$dtnc_consulter_req .= " and source in (".tabtosql($r_source).") ";
if (is_array($r_sanction_jallais))$dtnc_consulter_req .= " and sanction_jallais in (".tabtosql($r_sanction_jallais).") ";
if (is_array($r_sanction_client))$dtnc_consulter_req .= " and sanction_client in (".tabtosql($r_sanction_client).") ";
if ($r_num_affaire <> "")$dtnc_consulter_req .= " and id_affaire = $r_num_affaire ";
if (is_array($r_statut))$dtnc_consulter_req .= " and statut in (".tabtosql($r_statut).") ";
if ($r_ref_client > 0)$dtnc_consulter_req .= " and numero_client = $r_ref_client ";
if (is_array($r_imputation))$dtnc_consulter_req .= " and id_imputation in (".tabtosql($r_imputation).") ";
//if ($r_statut <> "")$dtnc_consulter_req .= " and designation like '%$r_designation%' ";
if ($r_designation <> "")$dtnc_consulter_req .= sql_find('designation',$r_designation);
if ($r_ref_produit <> "")$dtnc_consulter_req .= " and ref_produit like '$r_ref_produit%' ";
if (is_array($r_red))$dtnc_consulter_req .= " and redacteur in (".tabtosql($r_red).") ";
if (is_array($r_anal))$dtnc_consulter_req .= " and id_analyste in (".tabtosql($r_anal).") ";
if ($r_type_imput=="r_imputation_interne") $dtnc_consulter_req .= "and id_imputation <> 0";
if ($r_type_imput=="r_imputation_externe") $dtnc_consulter_req .= "and id_imputation_externe  > 0";

if ($r_four > 0)$dtnc_consulter_req .= " and i.fournisseur = '$r_four' ";
if ($r_client <> '')$dtnc_consulter_req .= " and client like '%$r_client%' ";


if($_SESSION['dtnc_consulter_trier']==''){$_SESSION['dtnc_consulter_trier']='id ';}
if(isset($trier)){$_SESSION['dtnc_consulter_trier']=$trier;}

if($_SESSION['dtnc_consulter_ordre']==''){$_SESSION['dtnc_consulter_ordre']='desc ';}
if(isset($ordre)){$_SESSION['dtnc_consulter_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['dtnc_consulter_trier'];
$tf->ordre_ec=$_SESSION['dtnc_consulter_ordre'];
$tf->page_ec=$_SESSION['en_cour'];

$page = new page;
$page->head("Consulter une DTNC");
$page->body();
$page->entete("Consulter une DTNC");
$page->add_button(1,0);
$page->add_button(2,1,parent(1170));
$page->add_button(3,0);
$page->add_button(0,2);
if(d_ok(1174))$page->add_button(52,1,"dtnc_indicateur_affaire.php?parent_ori=1","Bilans DTNC Affaire");
if(d_ok(1176))$page->add_button(17,1,"dtnc_indicateur_piece_serie.php?parent_ori=1","Bilans DTNC Pièce série");
if(d_ok(1178))$page->add_button(46,1,"dtnc_indicateur_reception.php?parent_ori=1","Bilans DTNC Réception");
$page->fin_entete();
$page->datescript();
?>
<script LANGUAGE="JavaScript">

function Lien() {
	i = document.getElementById("ajout_dtnc").selectedIndex;
	if (i == 0) return;
	url = document.getElementById("ajout_dtnc").options[i].value;
	document.location.href = 'dtnc_ajouter.php?new=1&dtnc_type='+url;
}

</script>


<center>
<form style='position:relative;z-index:1;' method="post" name="formulaire1" action="dtnc_consulter.php?mode=1"  target="principal">

<table  class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
<tr >
<td class= "m3"  >

ID :
<input id="button2"  type=text name="r_id" size=6  value="<? echo $r_id;?>"  onchange="document.formulaire1.submit();" >
&nbsp; &nbsp;

Imputation : 
<select name="r_type_imput">
			<option value="">
			<option value="r_imputation_interne" <? if($r_type_imput=="r_imputation_interne") echo "selected"; ?> >Interne</option>
			<option value="r_imputation_externe" <? if($r_type_imput=="r_imputation_externe") echo "selected"; ?> >Externe</option>
	
</select>
&nbsp; &nbsp;

<? 
echo liste_ms($j_dtnc_type,$r_type,"r_type" ,"Par secteur");
?>
&nbsp; &nbsp;


<? 
echo liste_ms("select id , nom from dtnc_unite_client group by id",$r_uclient,"r_uclient" ,"Unité client");
?>
&nbsp; &nbsp;


<? 
echo liste_ms($j_source,$r_source,"r_source" ,"Source");
?>
&nbsp; &nbsp; 


<? 
echo liste_ms($j_sanction_jallais,$r_sanction_jallais,"r_sanction_jallais" ,"Sanction Interne");
?>
&nbsp; &nbsp;


<? 
echo liste_ms($j_sanction_client,$r_sanction_client,"r_sanction_client" ,"Sanction client");
?>
&nbsp; &nbsp;
Affaire :
<input id="button2"  type=text name="r_num_affaire" size=5 maxlength=24 value="<? echo $r_num_affaire;?>"  onchange="document.formulaire1.submit();" >

&nbsp; &nbsp;

<? 
echo liste_ms("select i.id,concat(i.nom,' ',i.prenom) from dtnc left join interne i on dtnc.redacteur = i.id group by i.id order by i.nom asc",$r_red,"r_red" ,"Rédacteur");
?>

&nbsp; &nbsp;

<? 
echo liste_ms("select i.id,concat(i.nom,' ',i.prenom) from dtnc left join interne i on dtnc.id_analyste = i.id group by i.id order by i.nom asc",$r_anal,"r_anal" ,"Opérateur");
?>



</td>
</tr>
<tr>
<td class=m3>

<? 

echo liste_ms("select id , nom from dtnc_cause where type = 1 order by nom asc",$r_cause,"r_cause" ,"Cause PS");
?>

&nbsp; &nbsp;

<? 
echo liste_ms($j_statut_dtnc,$r_statut,"r_statut" ,"Statut");
?>
&nbsp; &nbsp;
Référence client :
<input id="button2"  type=text name="r_ref_client" size=10 maxlength=11 value="<? echo $r_ref_client;?>"  onchange="document.formulaire1.submit();" >
&nbsp;
<? 

echo liste_ms("select gt.id,code from gt, dtnc d where d.id_imputation = gt.id and gt_piece_serie=1 and d.client = ''  order by id asc",$r_imputation,"r_imputation" ,"Imputation");
?>

&nbsp; &nbsp;
Désignation :
<input id="button2"  type=text name="r_designation" size=20  value="<? echo $r_designation;?>"  onchange="document.formulaire1.submit();" >
&nbsp;

&nbsp; &nbsp;
Client :
<input id="button2"  type=text name="r_client" size=10 value="<? echo $r_client;?>"  onchange="document.formulaire1.submit();" >
&nbsp;

&nbsp; &nbsp;
Réf produit :
<input id="button2"  type=text name="r_ref_produit" size=20  value="<? echo $r_ref_produit;?>"  onchange="document.formulaire1.submit();" >
&nbsp;

<input type=submit id=button3 value="Go">
&nbsp; &nbsp;<input id=button3 type=button value="Clear" onclick="document.location.href='dtnc_consulter.php?mode=9';">
&nbsp; &nbsp;| &nbsp; &nbsp; 

Ajouter une DTNC
<? echo liste_d2($j_dtnc_type,"","ajout_dtnc",'id="ajout_dtnc" onChange="Lien();"'); ?>

<label><input class=m3 type=checkbox name="r_tout" <? if ($r_tout == 1)echo 'checked';?> > Sur 1 page</label> &nbsp; &nbsp;


</td>
</tr>
</table><br>

</form>

<table class=forumline cellSpacing=1 cellPadding=2 width="100%" align=left border=0>
<tr >
<td class= "m3" width=10% ><? $tf->aff("id","DTNC");?></td>
<td class= "m3" width=5% ><? $tf->aff("type","Secteur");?></td>
<td class= "m3" width=20% ><? $tf->aff("designation","Désignation");?></td>
<td class= "m3" width=10% ><? $tf->aff("ref_produit","Référence produit");?></td>
<td class= "m3" width=5% ><? $tf->aff("numero_client","Référence Client");?></td>
<td class= "m3" width=5% ><? $tf->aff("id_of","OF interne");?></td>
<td class= "m3" width=5% ><? $tf->aff("quantite","Qte concernée");?></td>
<td class= "m3" width=5% ><? $tf->aff("sanction_client","Sanction Client");?></td>
<td class= "m3" width=5% ><? $tf->aff("date","Date");?></td>
<td class= "m3" width=5% ><? $tf->aff("statut","Statut");?></td>
<td class= "m3" width=5% ><? $tf->aff("id_fiche_action","N° FA");?></td>
<td class= "m3" width=10% ><? $tf->aff("id_imputation_externe","Imputation");?></td>

</tr>

<?
$req="select id,id_of,quantite,type,sanction_client,date,statut,designation, numero_client,ref_produit,id_fiche_action, client ,id_imputation_externe
from  dtnc  where 1 $dtnc_consulter_req order by ".$_SESSION['dtnc_consulter_trier']." ".$_SESSION['dtnc_consulter_ordre'];

//echo $req;
$ic=0;
$res=my_query($req);
$ligne_page=$lpp -10;         //ligne par page
$p_pf=20;  			     //page par feuille

$nb_ligne=mysql_num_rows($res);
if ($nb_ligne==0){echo "<br>Aucun enregistrement trouvé<br>";exit;}
$prem_ligne=(($p_en - 1) * $ligne_page);
mysql_data_seek($res,0);
mysql_data_seek($res,$prem_ligne);

while ($ligne=mysql_fetch_array($res))
	{
	if (($ic % 2)==0){$cid="class= \"cel2\"";}else {$cid="class= \"cel1\"";}
	?>
	<tr align=center>
		<td <? echo $cid;?>  ><a class="b" href="dtnc_ajouter.php?util=<? echo $ligne["id"]?>"><? echo format_0($ligne["id"],6);?></a></td>
		<td <? echo $cid;?>  ><? echo $j_dtnc_type[$ligne["type"]];?></td>
		<td <? echo $cid;?>  ><? echo $ligne["designation"];?></td>
		<td <? echo $cid;?>  ><? echo $ligne["ref_produit"];?></td>
		<td <? echo $cid;?>  ><? echo $ligne["numero_client"];?></td>
		<td <? echo $cid;?>  ><? echo $ligne["id_of"];?></td>
		<td <? echo $cid;?>  ><? echo $ligne["quantite"];?></td>
		<td <? echo $cid;?>  ><? echo $j_sanction_client[$ligne["sanction_client"]];?></td>
		<td <? echo $cid;?>  ><? echo datodf($ligne["date"]);?></td>
		<td <? echo $cid;?>  ><? echo $j_statut_dtnc[$ligne["statut"]];?></td>
		<td <? echo $cid;?>  ><? if($ligne["id_fiche_action"]>0) echo '<a class="b" href="fiche_action_ajouter.php?util='.$ligne["id_fiche_action"].'&parent='.$_SESSION['en_cour'].'">'.$ligne["id_fiche_action"].'</a>';?></td>
		<td <? echo $cid;?>  ><? echo $j_soc[$ligne["id_imputation_externe"]];?></td>
	</tr>
	<?
	$ic++;
		if (($r_tout == 0 )and($ic == $ligne_page)){break;}
	}
?>
</table>
<?
if ($r_tout == 0 )echo bar("dtnc_consulter.php",'',$p_en,$nb_ligne,$ligne_page);
echo pied_page();
?>
