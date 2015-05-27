<?php
include("fonction.php");
if (!d_ok(972)){header("location: espace_doc.php");exit;}

$_SESSION['en_cour']="of_rapprochement2.php?print=$print";

if($parent_id>0){$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori==1)or($_SESSION[$_SESSION['en_cour']]=="")){$_SESSION[$_SESSION['en_cour']]=parent(972);}

if($mode=="9")
	{
	$r_cat="0";
	$r_famille="0";
	$r_fab="0";
	$r_avion="0";
	$r_code="0";
	$r_ref="";
	$r_date="";
	$r_date2="";
	$r_saisie="0";
	$mode="1";
	$r_entite=0;
	}

if($mode=="1")
	{
	$_SESSION['of_rapprochement2_cat']=$r_cat;
	$_SESSION['of_rapprochement2_r_code']=$r_code;
	$_SESSION['of_rapprochement2_famille']=$r_famille;
	$_SESSION['of_rapprochement2_fab']=$r_fab;
	$_SESSION['of_rapprochement2_avion']=$r_avion;
	$_SESSION['of_rapprochement2_ref']=$r_ref;
	$_SESSION['of_rapprochement2_date']=$r_date;
	$_SESSION['of_rapprochement2_date2']=$r_date2;
	$_SESSION['of_rapprochement2_r_saisie']=$r_saisie;
	$_SESSION['of_rapprochement2_r_entite']=$r_entite;

	$p_en=1;
	}

if($p_en>0)$_SESSION["of_rapprochement2_p_en"]=$p_en;
if($mode>0) {$p_en=1;} else {$p_en=$_SESSION["of_rapprochement2_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["of_rapprochement2_p_en"]=$p_en;

$of_rapprochement2_req="";

$r_cat=$_SESSION['of_rapprochement2_cat'];
$r_code=$_SESSION['of_rapprochement2_r_code'];
$r_famille=$_SESSION['of_rapprochement2_famille'];
$r_fab=$_SESSION['of_rapprochement2_fab'];
$r_avion=$_SESSION['of_rapprochement2_avion'];
$r_ref=$_SESSION['of_rapprochement2_ref'];
$r_mois=$_SESSION['of_rapprochement2_mois'];
$r_annee=$_SESSION['of_rapprochement2_annee'];
$r_date=$_SESSION['of_rapprochement2_date'];
$r_date2=$_SESSION['of_rapprochement2_date2'];
$r_saisie=$_SESSION['of_rapprochement2_r_saisie'];
$r_entite=$_SESSION['of_rapprochement2_r_entite'];

if(is_array($r_entite))$of_rapprochement2_req.=" AND of.id_entite in (".tabtosql($r_entite).") ";
if($r_cat>0)$of_rapprochement2_req.=" AND of.id_cat='$r_cat' ";
if($r_famille>0)$of_rapprochement2_req.=" AND of.id_famille='$r_famille' ";
//if($r_fab>0)$of_rapprochement2_req.=" AND of.id_fab ='$r_fab' ";
if(is_array($r_fab))$of_rapprochement2_req.=" AND of.id_fab in (".tabtosql($r_fab).") ";
//if($r_avion>0)$of_rapprochement2_req.=" AND of.id_avion ='$r_avion' ";
if(is_array($r_avion))$of_rapprochement2_req.=" AND of.id_avion in (".tabtosql($r_avion).") ";
if($r_ref<>"")$of_rapprochement2_req.=" AND of.ref like '$r_ref%' ";

if((isdf($r_date))and(isdf($r_date2))) {$of_rapprochement2_req.=" AND pi.d_fin_sap_facture between '".dftoda($r_date)."' AND '".dftoda($r_date2)."' ";}
else if(isdf($r_date)) {$of_rapprochement2_req.=" AND pi.d_fin_sap_facture='".dftoda($r_date)."' ";}

$_SESSION['of_rapprochement2_req']=$of_rapprochement2_req;

if($_SESSION['of_rapprochement2_trier']=='') {$_SESSION['of_rapprochement2_trier']='pi.id_gt ';}
if(isset($trier)) {$_SESSION['of_rapprochement2_trier']=$trier;}

if($_SESSION['of_rapprochement2_ordre']=='') {$_SESSION['of_rapprochement2_ordre']='ASC ';}
if(isset($ordre)) {$_SESSION['of_rapprochement2_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['of_rapprochement2_trier'];
$tf->ordre_ec=$_SESSION['of_rapprochement2_ordre'];
$tf->page_ec=$_SESSION['en_cour'];

if($print>0)
	{
	echo '<HTML><HEAD>'.$j_meta.$j_style.'</HEAD><BODY class="vide"><CENTER><h2>Rapprochement par facturation<BR>Date Edition : '.date('d/m/Y').'</h2></CENTER>';
	$j_even_dispo_color[0]='FFFFFF';
	}
else
	{
	$page=new page;
	$page->head("Rapprochement pour facturation PS");
	$page->body();
	$page->entete("Rapprochement pour facturation PS");
	$page->add_button(1,0);
	$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
	$page->add_button(3,0);
	$page->add_button(0,2);
	$page->add_button(0,2);
	$page->add_button(27,1,"of_rapprochement2.php?print=1","Imprimer");
	$page->fin_entete();
	$page->datescript();
	?>
	<FORM style="position:relative;z-index:1;" method="post" name="f1" action="of_rapprochement2.php?mode=1"  target="principal">
		<TABLE  class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
			<TR>
				<TD class="m3">
					Par Réference :
					<INPUT type="text" name="r_ref" size="20" maxlength="20" value="<?php echo $r_ref;?>"/>&nbsp;
					Catégorie :
					<?php 
					echo liste_db("SELECT id,nom FROM gamme_cat GROUP BY id",$r_cat,"r_cat",'onchange="f1.submit();"','<OPTION value="0"></OPTION>');
					echo "&nbsp; &nbsp; &nbsp;Famille:";
					echo liste_db("SELECT id,nom FROM gamme_famille GROUP BY id",$r_famille,"r_famille",'onchange="f1.submit();"','<OPTION value="0"></OPTION>');
					echo "&nbsp; &nbsp; &nbsp;";
					echo liste_ms("SELECT id,nom FROM piece_avion GROUP BY id",$r_avion,"r_avion","Par avion");
					echo "&nbsp; &nbsp; &nbsp;";
					echo liste_ms("SELECT id,nom FROM gamme_fab GROUP BY id",$r_fab,"r_fab","Par fabrication");
					echo "&nbsp; &nbsp;";
					echo liste_ms("SELECT id,nom FROM entite ORDER BY nom ASC ",$r_entite,"r_entite","UAP Gamme");
					?>
					&nbsp; &nbsp; &nbsp;
				</TD>
			</TR>
			<TR>	
				<TD class="m3">
					&nbsp;Date :<INPUT onclick="return showCalendar('sel3','%d/%m/%Y');"  id="sel3" class="button2" size="11" type="text"  name="r_date" value="<?php echo $r_date;?>"/>
					<?php echo calendar('sel3');?>&nbsp;à&nbsp;
					<INPUT onclick="return showCalendar('sel4','%d/%m/%Y');"  id="sel4" class="button2" size="11" type="text"  name="r_date2" value="<?php echo $r_date2;?>"/>
					<?php echo calendar('sel4');
					echo "&nbsp; &nbsp; &nbsp; &nbsp;";
					echo liste_ms("SELECT id,concat(code,' - ',designation) FROM gt WHERE gt_piece_serie=1 ORDER BY code ASC",$r_code, "r_code", "Code Gt");
					?>&nbsp; &nbsp;
					<INPUT type="submit" id="button3" value="Go"/>&nbsp; &nbsp;
					<INPUT id="button3" type="button" value="Clear" onclick="document.location.href='of_rapprochement2.php?mode=9';"/>
				</TD>
			</TR>
		</TABLE>
	</FORM>
	<?php
	}
function entete()
	{
	global $tf,$print,$class1,$class2,$class3;
	?>
	<style>
		.m2
			{
			border-width:1;
			border-left:0;
			border-right:0;
			border-top:0;
			border-bottom:1;
			padding-right:3;if (is_array($r_code))$of_rapprochement2_req .= "AND pi.id_gt in (".tabtosql($r_code).") ";
			border-style:solid;
			font-family: Arial;
			font-size: 13;
			color:#000000;
			padding-right:5;
			}
		.m4
			{
			background-color: #8996ae;
			color: #ffffff;
			font-weight:bolder;
			font-size:12;
			text-align: center;
			padding-right:5;
			}
		.m5
			{	
			color: #000000;
			font-size:12;
			text-align: center;
			padding-right:7;
			font-weight:bold;
			}	
	</style>
	<?php
	if($print>0)
		{
		$forumline="cellSpacing=0";
		$class1="m2";
		$class2="m2";
		$class3="m5";
		}
	else
		{
		$class1="cel1";
		$forumline='class="forumline" cellSpacing=1';
		$class2="cel2";
		$class3="m3";
		}
	?>
	<TABLE <?php echo $forumline;?> width="100%" align="center" cellSpacing="0" cellPadding="2" border="0">
		<TR class="m3">
			<TD rowspan=2 width=2%><?php $tf->aff("of.numero_client","Ordre");?></TD>
			<TD rowspan=2 width=2%><?php $tf->aff("of.poste","Poste");?></TD>
			<TD rowspan=2 width=2%><?php $tf->aff("of.of_client","OF client");?></TD>
			<TD rowspan=2 width=2%><?php $tf->aff("pi.id_of","OF");?></TD>
			<TD rowspan=2 width=4%><?php $tf->aff("gt_code","gt");?></TD>
			<TD rowspan=2 width=10%><?php $tf->aff("of.ref","Référence");?></TD>
			<TD rowspan=2 width=10%><?php $tf->aff("of.designation","Désignation");?></TD>
			<TD rowspan=2 width=8%><?php $tf->aff("gc.nom","Cat");?></TD>
			<TD rowspan=2 width=8%><?php $tf->aff("gf.nom","Famille");?></TD>
			<TD rowspan=2 width=8%><?php $tf->aff("gfb.nom","Fab");?></TD>
			<TD rowspan=2 width=8%><?php $tf->aff("of.d_fin_sap","Date SAP");?></TD>
			<TD rowspan=2 width=2%><?php $tf->aff("pi.quantite","Qté");?></TD>
			<TD colspan=2>Prix de vente</TD>
			<TD colspan=7>Carnet de commande</TD>
		</TR>
		<TR class="m3">
			<TD width=4%><?php $tf->aff("prix_vente","Prix/u");?></TD>
			<TD width=4%><?php $tf->aff("total","Total");?></TD>
			
			<TD width=4%><?php $tf->aff("ap.qte","Qté");?></TD>
			<TD width=4%><?php $tf->aff("ap.p_u","PU");?></TD>
			<TD width=4%><?php $tf->aff("ap.mt","MT");?></TD>
			<TD width=4%><?php $tf->aff("ap.id_affaire","Affaire");?></TD>
			<TD width=4%><?php $tf->aff("ap.des","Poste");?></TD>
			<TD width=4%><?php $tf->aff("f.numero","N° Facture");?></TD>
			<TD width=4%><?php $tf->aff("ap.d_facture","Date Facture");?></TD>
		</TR>
	<?php
	}
if($r_date=='')
	{
	echo "Vous devez ajouter au moins 1 date!";
	exit;
	}
$turn=0;
entete();
if($print<>1) echo'<tbody style="width:100%;height:540;overflow-Y:auto;">';
$req2="SELECT pi.numero,pi.id_of,pi.id,pi.gt_code,pi.statut,pi.quantite,pi.tps_reel,g.prix_vente,of.numero_client,of.of_client,of.ref,of.poste,of.designation,gf.nom,gc.nom as cat,
gfb.nom as fab,of.quantite_ok,of.d_fin_sap,ap.qte,ap.p_u,ap.mt,ap.des,ap.id_affaire,ap.d_facture,f.numero
FROM phase_i as pi
LEFT JOIN of ON pi.id_of=of.id
LEFT JOIN gamme_famille as gf ON gf.id=of.id_famille
LEFT JOIN gamme_cat as gc ON gc.id=of.id_cat
LEFT JOIN gamme g ON of.id_gamme=g.id
LEFT JOIN affaire_prev ap ON (of.of_client=ap.n_cde_c AND ap.n_cde_c<>'' AND of.of_client<>'*')
	or (of.numero_client=ap.n_cde_c AND (of.poste)+0=ap.des AND of.poste<>'' AND of.numero_client<>'' AND of.numero_client<>'*' AND of.poste<>'*')
LEFT JOIN facture f ON ap.id_facture=f.id
LEFT JOIN gamme_fab gfb ON of.id_fab=gfb.id
WHERE pi.facturation=1 $of_rapprochement2_req AND pi.is_facturation=1 AND of.id_affaire_type=1 AND of.etat=2 AND pi.statut=2
ORDER BY pi.id_gt ASC,".$_SESSION['of_rapprochement2_trier']." ".$_SESSION['of_rapprochement2_ordre']."  ";
//echo $req2;
//pi.tps_devis_ori,
//pi.prix_vente_ori,
//pi.tps_obj_ori,
//((pi.tx_gt*pi.tps_reel)+ pi.mt_st) as total2,
//pi.tx_gt,
$res2=my_query($req2);
while($ligne2=mysql_fetch_array($res2))
	{
	//$pv=div($ligne2["prix_vente_ori"] * $ligne2["quantite_ok"],$ligne2["quantite"]);
	$tab[$ligne2["gt_code"]]["totqte"]+=$ligne2["quantite"];
	$tab[$ligne2["gt_code"]]["total"]+=($ligne2["prix_vente"]);
	$tab[$ligne2["gt_code"]]["somme"]+=($ligne2["prix_vente"]*$ligne2["quantite_ok"]);
	$tab[$ligne2["gt_code"]]["total_tps"]+=$ligne2["tps_reel"];
	
	$tab[$ligne2["gt_code"]]["ap_totqte"]+=$ligne2["qte"];
	$tab[$ligne2["gt_code"]]["ap_total"]+=($ligne2["mt"]);
	//	$tab[$ligne2["gt_code"]]["total_somme"]+=$ligne2["total2"];
	//	$tab[$ligne2["gt_code"]]["total_ecart"]+=($ligne2["prix_vente"]*$ligne2["quantite_ok"]) - $ligne2["total2"];
	$tab[$ligne2["gt_code"]]["html"].='
	<TR class="m2" align="center">
		<TD class='.$class2.'>'.$ligne2["numero_client"].'&nbsp</TD>
		<TD class='.$class2.'>'.$ligne2["poste"].'&nbsp</TD>
		<TD class='.$class2.'>'.$ligne2["of_client"].'&nbsp</TD>
		<TD class='.$class1.'>'.$ligne2["id_of"].'</TD>
		<TD class='.$class1.'>'.$ligne2["gt_code"].'</TD>
		<TD class='.$class2.' nowrap>'.$ligne2["ref"].'</TD>
		<TD class='.$class1.' nowrap><DIV style="overflow:hidden;width:95%;height:100%;">'.$ligne2["designation"].'</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["cat"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["nom"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["fab"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.datodf($ligne2["d_fin_sap"]).'&nbsp</TD>
		<TD class='.$class2.' nowrap>'.$ligne2["quantite_ok"].'</TD>
		<TD class='.$class1.' nowrap>'.nformat($ligne2["prix_vente"],0,1).'</TD>
		<TD class='.$class3.' nowrap>'.nformat($ligne2["prix_vente"]*$ligne2["quantite_ok"],0,1).'</TD>
		
		<TD class='.$class2.' nowrap>'.$ligne2["qte"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["p_u"].'&nbsp</TD>
		<TD class='.$class3.' nowrap>'.$ligne2["mt"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["id_affaire"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["des"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.$ligne2["numero"].'&nbsp</TD>
		<TD class='.$class1.' nowrap>'.datodf($ligne2["d_facture"]).'&nbsp</TD>
	</TR>';
	}
if(is_array($tab)) foreach($tab as $ligne)
	{
	echo $ligne["html"];
	echo '<TR align="center">
		<TD colspan=4 height="30" class='.$class3.'></TD>
		<TD class='.$class3.'>'.$ligne["gt_code"].'</TD>
		<TD colspan=6 class='.$class3.'></TD>
		<TD class='.$class3.' nowrap>'.nformat($ligne["totqte"],0,1,0).'</TD>
		<TD class='.$class3.' nowrap>'.nformat($ligne["total"],0,1).'</TD>
		<TD class='.$class3.' nowrap>'.nformat($ligne["somme"],0,1).'</TD>
		<TD colspan=7 class='.$class3.'></TD>
	</TR>';
	$t_totqte+=$ligne["totqte"];
	$t_total+=$ligne["total"];
	$t_somme+=$ligne["somme"];
	$t_total_tps+=$ligne["total_tps"];
	$t_ap_qte+=$ligne["ap_totqte"];
	$t_ap_tot+=$ligne["ap_total"];
	//$t_total_somme+=$ligne["total_somme"];
	//$t_total_ecart+=$ligne["total_ecart"];
	}
echo '<TR align="center">
	<TD colspan=11 height="30" class='.$class3.'>TOTAL</TD>
	<TD class='.$class3.' nowrap>'.nformat($t_totqte,0,1,0).'</TD>
	<TD class='.$class3.' nowrap>'.nformat($t_total,0,1).'</TD>
	<TD class='.$class3.' nowrap>'.nformat($t_somme,0,1).'</TD>
	<TD class='.$class3.' nowrap>'.nformat($t_ap_qte,0,1).'</TD>
	<TD class='.$class3.' nowrap></TD>
	<TD class='.$class3.' nowrap>'.nformat($t_ap_tot,0,1).'</TD>
	<TD colspan=4 class='.$class3.'></TD>
</TR>';
if($print<>1) echo'</tbody>';
echo "</TABLE>";
echo pied_page();
?>
