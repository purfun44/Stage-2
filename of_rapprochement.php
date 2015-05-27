<?php
include("fonction.php");
include("fonction_affaire.php");
if (!d_ok(972)){header("location: espace_doc.php");exit;}

$_SESSION['en_cour']="of_rapprochement.php?print=$print";

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
	$r_numero_client="";
	$r_poste="";
	$r_of_client="";
	$r_id_of="";
	$r_date="";
	$r_date2="";
	$r_saisie="0";
	$mode="1";
	$r_entite=0;
	$r_etat=4;
	}
if ((!isset($r_etat))and(!isset($_SESSION['of_rapprochement_r_etat'])))$_SESSION['of_rapprochement_r_etat']=4;
if($mode=="1")
	{
	$_SESSION['of_rapprochement_cat']=$r_cat;
	$_SESSION['of_rapprochement_r_code']=$r_code;
	$_SESSION['of_rapprochement_famille']=$r_famille;
	$_SESSION['of_rapprochement_fab']=$r_fab;
	$_SESSION['of_rapprochement_avion']=$r_avion;
	$_SESSION['of_rapprochement_ref']=$r_ref;
	$_SESSION['of_rapprochement_numero_client']=$r_numero_client;
	$_SESSION['of_rapprochement_poste']=$r_poste;
	$_SESSION['of_rapprochement_of_client']=$r_of_client;
	$_SESSION['of_rapprochement_id_of']=$r_id_of;
	$_SESSION['of_rapprochement_date']=$r_date;
	$_SESSION['of_rapprochement_date2']=$r_date2;
	$_SESSION['of_rapprochement_r_saisie']=$r_saisie;
	$_SESSION['of_rapprochement_r_entite']=$r_entite;
	$_SESSION['of_rapprochement_r_etat']=$r_etat;
	$p_en=1;
	}

if($p_en>0)$_SESSION["of_rapprochement_p_en"]=$p_en;
if($mode>0) {$p_en=1;} else {$p_en=$_SESSION["of_rapprochement_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["of_rapprochement_p_en"]=$p_en;

$of_rapprochement_req="";

$r_cat=$_SESSION['of_rapprochement_cat'];
$r_code=$_SESSION['of_rapprochement_r_code'];
$r_famille=$_SESSION['of_rapprochement_famille'];
$r_fab=$_SESSION['of_rapprochement_fab'];
$r_avion=$_SESSION['of_rapprochement_avion'];
$r_ref=$_SESSION['of_rapprochement_ref'];
$r_numero_client=$_SESSION['of_rapprochement_numero_client'];
$r_poste=$_SESSION['of_rapprochement_poste'];
$r_of_client=$_SESSION['of_rapprochement_of_client'];
$r_id_of=$_SESSION['of_rapprochement_id_of'];
$r_date=$_SESSION['of_rapprochement_date'];
$r_date2=$_SESSION['of_rapprochement_date2'];
$r_saisie=$_SESSION['of_rapprochement_r_saisie'];
$r_entite=$_SESSION['of_rapprochement_r_entite'];
$r_etat=$_SESSION['of_rapprochement_r_etat'];

if($r_etat==0)$of_rapprochement_req.=" AND of.is_rapprochement=0 ";//Non rapproché
if($r_etat==1)$of_rapprochement_req.=" AND of.is_rapprochement>0 ";//Rapproché
if($r_etat==2)$of_rapprochement_req.=" AND ((of.is_rapprochement<> of.quantite_ok )or(ap.id is NULL)or(ap.id_facture=0)) ";//Non facturé
if($r_etat==3)$of_rapprochement_req.=" AND of.is_rapprochement>0 AND ap.id_facture!=0 ";//Facturé
if(is_array($r_entite))$of_rapprochement_req.=" AND of.id_entite in (".tabtosql($r_entite).") ";
if($r_cat>0)$of_rapprochement_req.=" AND of.id_cat='".$r_cat."' ";
if($r_famille>0)$of_rapprochement_req.=" AND of.id_famille='".$r_famille."' ";
//if($r_fab>0)$of_rapprochement_req.=" AND of.id_fab ='$r_fab' ";
if(is_array($r_fab))$of_rapprochement_req.=" AND of.id_fab in (".tabtosql($r_fab).") ";
//if($r_avion>0)$of_rapprochement_req.=" AND of.id_avion ='$r_avion' ";
if(is_array($r_avion))$of_rapprochement_req.=" AND of.id_avion in (".tabtosql($r_avion).") ";
if($r_ref<>"")$of_rapprochement_req.=" AND of.ref like '".$r_ref."%' ";
if($r_numero_client<>"")$of_rapprochement_req.=" AND of.numero_client like '".$r_numero_client."%' ";
if($r_poste<>"")$of_rapprochement_req.=" AND of.poste in (".$r_poste.") ";
if($r_of_client<>"")$of_rapprochement_req.=" AND of.of_client in (".$r_of_client.") ";
if($r_id_of<>"")$of_rapprochement_req.=" AND pi.id_of in (".$r_id_of.") ";
if (is_array($r_code))$of_rapprochement_req .=" AND pi.id_gt in (".tabtosql($r_code).") ";

if((isdf($r_date))and(isdf($r_date2))) {$of_rapprochement_req.=" AND pi.d_fin_sap_facture between '".dftoda($r_date)."' AND '".dftoda($r_date2)."' ";}
else if(isdf($r_date)) {$of_rapprochement_req.=" AND pi.d_fin_sap_facture='".dftoda($r_date)."' ";}

$_SESSION['of_rapprochement_req']=$of_rapprochement_req;

if($_SESSION['of_rapprochement_trier']=='') {$_SESSION['of_rapprochement_trier']='pi.id_gt ';}
if(isset($trier)) {$_SESSION['of_rapprochement_trier']=$trier;}

if($_SESSION['of_rapprochement_ordre']=='') {$_SESSION['of_rapprochement_ordre']='ASC ';}
if(isset($ordre)) {$_SESSION['of_rapprochement_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['of_rapprochement_trier'];
$tf->ordre_ec=$_SESSION['of_rapprochement_ordre'];
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
	$page->add_button(27,1,"of_rapprochement.php?print=1","Imprimer");
	$page->add_button(0,2);
	$page->add_button(75,1,"of_rapprochement.php?maj=1","Mise à jour");
	$page->fin_entete();
	$page->datescript();
	
	if($maj==1)
		{
		rapprochement_ps();
		echo "<CENTER>Mise à jour effectuée</CENTER><BR><BR>";
		$maj=0;
		}
	?>
	<FORM style="position:relative;z-index:1;" method="post" name="f1" action="of_rapprochement.php?mode=1" target="principal">
		<TABLE class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
			<TR>
				<TD class="m3">
					Réference :
					<INPUT type="text" name="r_ref" size="20" maxlength="20" value="<?php echo $r_ref;?>"/>&nbsp;&nbsp;&nbsp;
					Catégorie :
					<?php
					echo liste_db("SELECT id,nom FROM gamme_cat GROUP BY id",$r_cat,"r_cat",'onchange="f1.submit();"','<OPTION value="0"></OPTION>');
					echo "&nbsp;&nbsp;&nbsp;Famille : ";
					echo liste_db("SELECT id,nom FROM gamme_famille GROUP BY id",$r_famille,"r_famille",'onchange="f1.submit();"','<OPTION value="0"></OPTION>');
					echo "&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,nom FROM piece_avion GROUP BY id",$r_avion,"r_avion","Par avion");
					echo "&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,nom FROM gamme_fab GROUP BY id",$r_fab,"r_fab","Par fabrication");
					echo "&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,nom FROM entite ORDER BY nom ASC ",$r_entite,"r_entite","UAP Gamme");
					?><BR>
					Cde Client :
					<INPUT type="text" name="r_numero_client" size="10" maxlength="20" value="<?php echo $r_numero_client;?>"/>&nbsp;&nbsp;&nbsp;
					Poste :
					<INPUT type="text" name="r_poste" size="10" maxlength="20" value="<?php echo $r_poste;?>"/>&nbsp;&nbsp;&nbsp;
					OF Client :
					<INPUT type="text" name="r_of_client" size="10" maxlength="20" value="<?php echo $r_of_client;?>"/>&nbsp;&nbsp;&nbsp;
					OF :
					<INPUT type="text" name="r_id_of" size="10" maxlength="20" value="<?php echo $r_id_of;?>"/>&nbsp;&nbsp;&nbsp;
					Date fin SAP :
					<INPUT onclick="return showCalendar('sel3','%d/%m/%Y');" id="sel3" class="button2" size="11" type="text" name="r_date" value="<?php echo $r_date;?>"/>
					<?php echo calendar('sel3');?>&nbsp;à&nbsp;
					<INPUT onclick="return showCalendar('sel4','%d/%m/%Y');" id="sel4" class="button2" size="11" type="text" name="r_date2" value="<?php echo $r_date2;?>"/>
					<?php echo calendar('sel4')."&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,concat(code,' - ',designation) FROM gt WHERE gt_piece_serie=1 ORDER BY code ASC",$r_code,"r_code","Code Gt");
					echo "&nbsp;&nbsp;&nbsp;&nbsp;Etat :";
					?>
					<SELECT id="button" name="r_etat" size="1" onchange="document.formulaire1.submit();">
						<OPTION value="0" <?php if($r_etat==0){echo "selected";}?>>Non rapproché</OPTION>
						<OPTION value="1" <?php if($r_etat==1){echo "selected";}?>>Rapproché</OPTION>
						<OPTION value="2" <?php if($r_etat==2){echo "selected";}?>>Non facturé</OPTION>
						<OPTION value="3" <?php if($r_etat==3){echo "selected";}?>>Facturé</OPTION>
						<OPTION value="4" <?php if($r_etat==4){echo "selected";}?>>Tout</OPTION>
					</SELECT>
				</TD>
				<TD class="m3">
					<INPUT type="submit" id="button3" value="Go"/>&nbsp;&nbsp;
					<INPUT id="button3" type="button" value="Clear" onclick="document.location.href='of_rapprochement.php?mode=9';"/>
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
			padding-right:3;
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
	
		$class1="cel1";
		$forumline='class="forumline" cellSpacing=1';
		$class2="cel2";
		$class3="m3";
		
	?>
	<TABLE <?php echo $forumline;?> width="100%" align="center" cellSpacing="0" cellPadding="2" border="0">
		<TR class="m3">
			<TD rowspan="2" width="2%"><?php $tf->aff("of.numero_client","Cde Client");?></TD>
			<TD rowspan="2" width="2%"><?php $tf->aff("of.poste","Poste");?></TD>
			<TD rowspan="2" width="2%"><?php $tf->aff("of.of_client","OF Client");?></TD>
			<TD rowspan="2" width="2%"><?php $tf->aff("pi.id_of","OF");?></TD>
			<TD rowspan="2" width="4%"><?php $tf->aff("gt_code","GT");?></TD>
			<TD rowspan="2" width="15%"><?php $tf->aff("of.ref","Référence");?></TD>
			<TD rowspan="2" width="15%"><?php $tf->aff("of.designation","Désignation");?></TD>
			<TD rowspan="2" width="5%"><?php $tf->aff("gc.nom","Cat");?></TD>
			<TD rowspan="2" width="8%"><?php $tf->aff("gf.nom","Famille");?></TD>
			<TD rowspan="2" width="5%"><?php $tf->aff("gfb.nom","Fab");?></TD>
			<TD rowspan="2" width="5%"><?php $tf->aff("of.d_fin_sap","Date SAP");?></TD>
			<TD rowspan="2" width="2%"><?php $tf->aff("of.quantite_ok","Qté");?></TD>
			<TD colspan="2" width="4%">Prix de vente Interne</TD>
			<?php 
			if($_SESSION['of_rapprochement_r_etat']!=0)
				{ ?>
				<TD rowspan="2" width="2%"><?php $tf->aff("ora.qte","Qté rapprochée");?></TD>
				<TD colspan="2" width="4%">Prix de vente Client</TD>
				<TD rowspan="2" width="5%"><?php $tf->aff("diff","Différence de prix");?></TD>
				<?php 
				if($_SESSION['of_rapprochement_r_etat']!=2)
					{ ?>
					<TD rowspan="2" width="4%"><?php $tf->aff("mt_facture","Mt facturé");?></TD>
					<TD rowspan="2" width="4%"><?php $tf->aff("qte_non_facture","Qté non facturé");?></TD>
				<?php	}
				} ?>
		</TR>
		<TR class="m3">
			<TD width="2%"><?php $tf->aff("prix_vente","Prix/u");?></TD>
			<TD width="2%"><?php $tf->aff("total","Total");?></TD>
			<?php
			if($_SESSION['of_rapprochement_r_etat']!=0)
				{?>
				<TD width="2%"><?php $tf->aff("pum_client","Prix/u");?></TD>
				<TD width="2%"><?php $tf->aff("mt","Total");?></TD>
				<?php }
		echo '</TR>';
	}
  	?>

<style type="text/css" media="all">
	.cachediv
		{
		display: none;
		}
</style>
<script src="/js/ajax.js" type="text/javascript"></script>
<script LANGUAGE="JavaScript">
function DivStatus(divID)
	{
	Pdiv=document.getElementById(divID);
	Pdiv.className=(Pdiv.className=='cachediv') ? '' : 'cachediv';
	findap(divID);
	}
function findap(id_of)
	{
	var req=null;
	req=get_xhr();
	req.onreadystatechange=function()
		{
		if(req.readyState==4)
			{
			if(req.status==200)
				{
				document.getElementById(id_of).innerHTML=req.responseText;
				//alert(req.responseText);
				}
			else
				{
				alert("Error: returned status code "+req.status+" "+req.statusText);
				}
			}
		};

	var url ="req_ajax.php?id_req=42&id_of="+id_of;
	req.open("POST",url,true);
	req.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	req.send(null);
	}
</script>
<?php
if($r_date=='')
	{
	echo "Vous devez ajouter au moins 1 date!";
	exit;
	}
$turn=0;
entete();
if($print<>1) echo'<tbody style="width:100%;height:540;overflow-Y:auto;">';
/*
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
WHERE pi.facturation=1 $of_rapprochement_req AND pi.is_facturation=1 AND of.id_affaire_type=1 AND of.etat=2 AND pi.statut=2
ORDER BY pi.id_gt ASC,".$_SESSION['of_rapprochement_trier']." ".$_SESSION['of_rapprochement_ordre']."  ";

SELECT ap.mt,ap.p_u,of.quantite_ok
FROM of
LEFT JOIN of_rapprochement ora ON ora.id_of=of.id
LEFT JOIN affaire_prev ap ON ap.id=ora.id_affaire_prev
WHERE 
GROUP BY ora.id_of,of.id

if(ap.id_facture=0,sum(ap.p_u * ora.qte),0)
*/
$req2="SELECT pi.id_of,pi.id,pi.gt_code,pi.statut,g.prix_vente,of.numero_client,of.of_client,of.ref,of.poste,of.designation,gf.nom,of.is_rapprochement,gc.nom as cat,gfb.nom as fab,
	of.quantite_ok,of.d_fin_sap,sum(ora.qte) as ora_qte,of.quantite_ok*g.prix_vente as mt_gamme,if(sum(ora.qte)>0 ,sum(ap.p_u * ora.qte)/sum(ora.qte),0) as pum_client,
	sum(ap.p_u * ora.qte) as mt,sum(ap.p_u * ora.qte)-(of.quantite_ok*g.prix_vente) as diff,sum(if(ap.id_facture>0,ap.p_u * ora.qte,0)) as mt_facture,
	of.quantite_ok-sum(ora.qte) as qte_non_facture
	FROM phase_i as pi
	LEFT JOIN of ON pi.id_of=of.id
	LEFT JOIN gamme_famille as gf ON gf.id=of.id_famille
	LEFT JOIN gamme_cat as gc ON gc.id=of.id_cat
	LEFT JOIN gamme g ON of.id_gamme=g.id
	LEFT JOIN gamme_fab gfb ON of.id_fab=gfb.id
	LEFT JOIN of_rapprochement ora ON ora.id_of=of.id
	LEFT JOIN affaire_prev ap ON ap.id=ora.id_affaire_prev
	WHERE pi.facturation=1 $of_rapprochement_req AND pi.is_facturation=1 AND of.id_affaire_type=1 AND of.etat=2 AND pi.statut=2
	GROUP BY pi.id
	ORDER BY pi.id_gt ASC,".$_SESSION['of_rapprochement_trier']." ".$_SESSION['of_rapprochement_ordre']."  ";
//pi.tps_devis_ori,
//pi.prix_vente_ori,
//pi.tps_obj_ori,
//((pi.tx_gt*pi.tps_reel)+ pi.mt_st) as total2,
//pi.tx_gt,
$res2=my_query($req2);
while($ligne2=mysql_fetch_array($res2))
	{
	/*if($r_etat==2)
		{
		if(($ligne2["quantite_ok"]!=$ligne2["is_rapprochement"]) || (empty($ligne2["id_ap"])) || ($ligne2["id_facture"]==0))
			{*/
	//$pv=div($ligne2["prix_vente_ori"] * $ligne2["quantite_ok"],$ligne2["quantite"]);
	$tab[$ligne2["gt_code"]]["totqte"]+=$ligne2["quantite_ok"];
	$tab[$ligne2["gt_code"]]["mt_gamme"]+=$ligne2["mt_gamme"];
	$tab[$ligne2["gt_code"]]["totqte_ra"]+=$ligne2["ora_qte"];
	$tab[$ligne2["gt_code"]]["mt_gamme_ra"]+=$ligne2["mt"];
	$tab[$ligne2["gt_code"]]["totdiff"]+=$ligne2["diff"];
	$tab[$ligne2["gt_code"]]["totfac"]+=$ligne2["mt_facture"];
	$tab[$ligne2["gt_code"]]["totnonfac"]+=$ligne2["qte_non_facture"];
	$tab[$ligne2["gt_code"]]["gt_code"]=$ligne2["gt_code"];
	//$tab[$ligne2["gt_code"]]["total_tps"]+=$ligne2["tps_reel"];
	//$tab[$ligne2["gt_code"]]["ap_totqte"]+=$ligne2["qte"];
	//$tab[$ligne2["gt_code"]]["ap_total"]+=($ligne2["mt"]);
	//$tab[$ligne2["gt_code"]]["total_somme"]+=$ligne2["total2"];
	//$tab[$ligne2["gt_code"]]["total_ecart"]+=($ligne2["prix_vente"]*$ligne2["quantite_ok"]) - $ligne2["total2"];
	$t_facture["id_facture"]=$ligne2["id_facture"];
	
	if($ligne2["is_rapprochement"]>0)
		{
		$class1="cel4";
		}
	else
		{
		$class1="cel1";
		if($ligne2["qte_non_facture"]==0)
			{
			$ligne2["qte_non_facture"]=$ligne2["quantite_ok"];
			$tab[$ligne2["gt_code"]]["totnonfac"]+=$ligne2["qte_non_facture"];
			}
		}
	if($ligne2["fab"]=="Phase")
		{
		$facon_c="cel2";
		$phase="m3";
		}
	elseif($ligne2["fab"]=="Façon complète")
		{
		$facon_c="m3";
		$phase="cel2";
		}
	if($ligne2["ora_qte"]==""){$ligne2["ora_qte"]=0;}
	$tab[$ligne2["gt_code"]]["html"].='
	<TR class="m2" align="center" onclick="DivStatus('.$ligne2["id_of"].');">
		<TD class="'.$facon_c.'" nowrap>'.$ligne2["numero_client"].'&nbsp</TD>
		<TD class="'.$facon_c.'" nowrap>'.$ligne2["poste"].'&nbsp</TD>
		<TD class="'.$phase.'" nowrap>'.$ligne2["of_client"].'&nbsp</TD>
		<TD class="'.$class1.'" nowrap><a class="b" href="of_ajouter.php?util='.$ligne2["id_of"].'&parent_id=972">'.$ligne2["id_of"].'</a></TD>
		<TD class="'.$class1.'" nowrap>'.$ligne2["gt_code"].'</TD>
		<TD class="'.$class2.'" nowrap>'.$ligne2["ref"].'</TD>
		<TD class="'.$class1.'" nowrap><DIV style="overflow:hidden;width:95%;height:100%;">'.$ligne2["designation"].'</TD>
		<TD class="'.$class1.'" nowrap>'.$ligne2["cat"].'&nbsp</TD>
		<TD class="'.$class1.'" nowrap>'.$ligne2["nom"].'&nbsp</TD>
		<TD class="'.$class1.'" nowrap>'.$ligne2["fab"].'&nbsp</TD>
		<TD class="'.$class1.'" nowrap>'.datodf($ligne2["d_fin_sap"]).'&nbsp</TD>
		<TD class="'.$class2.'" nowrap>'.$ligne2["quantite_ok"].'</TD>
		<TD class="'.$class1.'" nowrap>'.nformat($ligne2["prix_vente"],0,1).'</TD>
		<TD class="'.$class3.'" nowrap>'.nformat($ligne2["mt_gamme"],0,1).'</TD>';
	if($r_etat!=0)
		{
		$tab[$ligne2["gt_code"]]["html"].='<TD class="'.$class1.'" nowrap>'.$ligne2["ora_qte"].'</TD>
		<TD class="'.$class1.'" nowrap>'.nformat($ligne2["pum_client"],0,1).'</TD>
		<TD class="'.$class3.'" nowrap>'.nformat($ligne2["mt"],0,1).'</TD>
		<TD class="'.$class1.'" nowrap>'.nformat($ligne2["diff"],"",1).'</TD>';
		if($r_etat!=2)
			{
			$tab[$ligne2["gt_code"]]["html"].='<TD class="'.$class3.'" nowrap>'.nformat($ligne2["mt_facture"],0,1).'</TD>
			<TD class="'.$class1.'" nowrap>'.nformat($ligne2["qte_non_facture"],0,1).'</TD>';
			}
		}
	$tab[$ligne2["gt_code"]]["html"].='</TR>
	<TR id="'.$ligne2["id_of"].'" align="center" class="cachediv">
	</TR>';
	/*}}*/
	}
if(is_array($tab)) foreach($tab as $ligne)
	{
	echo $ligne["html"];
	echo '<TR align="center">
		<TD colspan="11" class="'.$class3.'">SOUS-TOTAL du GT '.$ligne["gt_code"].'</TD>
		<TD title="Quantité" class="'.$class3.'" nowrap>'.nformat($ligne["totqte"],0,1,0).'</TD>
		<TD title="Moyenne des Prix/u Interne" class="'.$class3.'" nowrap>'.nformat(div($ligne["mt_gamme"],$ligne["totqte"]),0,1).'</TD>
		<TD title="Total Interne" class="'.$class3.'" nowrap>'.nformat($ligne["mt_gamme"],0,1).'</TD>';
	if($r_etat!=0)
		{
		echo '<TD title="Quantité rapprochée" class="'.$class3.'" nowrap>'.nformat($ligne["totqte_ra"],0,1).'</TD>
		<TD title="Moyenne des Prix/u Client" class="'.$class3.'" nowrap>'.nformat(div($ligne["mt_gamme_ra"],$ligne["totqte_ra"]),0,1).'</TD>
		<TD title="Total Client" class="'.$class3.'" nowrap>'.nformat($ligne["mt_gamme_ra"],0,1).'</TD>
		<TD title="Différence de prix" class="'.$class3.'" nowrap>'.nformat($ligne["totdiff"],0,1).'</TD>';
		if($r_etat!=2)
			{
			echo '<TD title="Montant facturé" class="'.$class3.'" nowrap>'.nformat($ligne["totfac"],0,1).'</TD>
			<TD title="Quantité non facturé" class="'.$class3.'" nowrap>'.nformat($ligne["totnonfac"],0,1).'</TD>';
			}
		}
	echo '</TR>';
	$t_totqte+=$ligne["totqte"];
	$t_somme+=$ligne["mt_gamme"];
	$t_totqte_ra+=$ligne["totqte_ra"];
	$t_somme_ra+=$ligne["mt_gamme_ra"];
	$t_totdiff+=$ligne["totdiff"];
	$t_totfac+=$ligne["totfac"];
	$t_totnonfac+=$ligne["totnonfac"];
	//$t_ap_qte+=$ligne["ap_totqte"];
	//$t_ap_tot+=$ligne["ap_total"];
	//$t_total_tps+=$ligne["total_tps"];
	//$t_total_somme+=$ligne["total_somme"];
	//$t_total_ecart+=$ligne["total_ecart"];
	}
echo '<TR height="5" class="cel4"><TD colspan="20"></TD></TR>
<TR align="center">
	<TD colspan="11" height="30" class="'.$class3.'">TOTAL</TD>
	<TD title="Quantité" class="'.$class3.'" nowrap>'.nformat($t_totqte,0,1,0).'</TD>
	<TD title="Moyenne des Prix/u Interne" class="'.$class3.'" nowrap>'.nformat(div($t_somme,$t_totqte),0,1).'</TD>
	<TD title="Total Interne" class="'.$class3.'" nowrap>'.nformat($t_somme,0,1).'</TD>';
if($r_etat!=0)
	{
	echo '<TD title="Quantité rapprochée" class="'.$class3.'" nowrap>'.nformat($t_totqte_ra,0,1).'</TD>
	<TD title="Moyenne des Prix/u Client" class="'.$class3.'" nowrap>'.nformat(div($t_somme_ra,$t_totqte_ra),0,1).'</TD>
	<TD title="Total Client" class="'.$class3.'" nowrap>'.nformat($t_somme_ra,0,1).'</TD>
	<TD title="Différence de prix" class="'.$class3.'" nowrap>'.nformat($t_totdiff,0,1).'</TD>';
	if($r_etat!=2)
		{
		echo '<TD title="Montant facturé" class="'.$class3.'" nowrap>'.nformat($t_totfac,0,1).'</TD>
		<TD title="Quantité non facturé" class="'.$class3.'" nowrap>'.nformat($t_totnonfac,0,1).'</TD>';
		}
	}
echo '</TR>';
if($print<>1) echo'</tbody>';
echo "</TABLE>";

$req3="SELECT f.id,f.numero,f.date,f.mt_ht
	FROM phase_i as pi
	LEFT JOIN of ON pi.id_of=of.id
	LEFT JOIN of_rapprochement ora ON ora.id_of=of.id
	LEFT JOIN affaire_prev ap ON ap.id=ora.id_affaire_prev
	LEFT JOIN facture f ON f.id=ap.id_facture
	WHERE pi.facturation=1 $of_rapprochement_req AND pi.is_facturation=1 AND of.id_affaire_type=1 AND of.etat=2 AND pi.statut=2 and f.id > 0
	GROUP BY f.id
	ORDER BY f.id";
$res3=my_query($req3);
echo "<BR><BR><TABLE class='forumline' width='50%' align='center' cellSpacing='1' cellPadding='2' border='0'>
	<TR class='m3'><TD colspan='4'HEIGHT=30>Factures utilisées dans ce rapprochement</TD></TR>
	<TR class='m3'>
		<TD width='25%'>Id facture</TD>
		<TD width='25%'>Numéro</TD>
		<TD width='25%'>Date</TD>
		<TD width='25%'>Montant HT</TD>
	</TR>";
while($ligne=mysql_fetch_array($res3))
	{
	echo '<TR align="center" class="cel1">
		<TD>'.$ligne["id"].'</TD>
		<TD><a class="b" href="/data/facture/facture_'.$ligne["numero"].'.pdf">'.$ligne["numero"].'</a></TD>
		<TD>'.datodf($ligne["date"]).'</TD>
		<TD>'.nformat($ligne["mt_ht"],0,1).'</TD>
	</TR>';
	$total+=$ligne["mt_ht"];
	}
echo '<TR align="center" class="m3">
	<TD colspan="3">TOTAL</TD>
	<TD>'.nformat($total,0,1).'</TD>
</TR>';
echo "</TABLE>";
echo pied_page();
?>
