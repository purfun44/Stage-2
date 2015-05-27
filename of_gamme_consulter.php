<?
include("fonction.php");
include("fonction_affaire.php");

if ((!d_ok(203))){header("location: accueil.php");exit;}

$_SESSION['en_cour']="of_gamme_consulter.php";

if(d_ok(951))$d_devis=1;
if(d_ok(953))$d_obj=1;
$colspan_moin = $d_devis + $d_obj - 2;

if($parent <> ""){$_SESSION[$_SESSION['en_cour']]=$parent;}
elseif($parent_id > 0){$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori == 1)or($_SESSION[$_SESSION['en_cour']]=="")){$_SESSION[$_SESSION['en_cour']]=parent(203);}




if($util>0)
	{
	$_SESSION['of_en_cour']=$util;
	$_SESSION['affaire_en_cour']=nombre_de("select id_affaire from of where id=".$util);
	}	

if(!isset($date))$date=date("d/m/Y");

if($change_of > 0)change_of_en_cour($change_of);

$type_affaire=nombre_de("select type from affaire where id=".$_SESSION['affaire_en_cour']);
$etat_affaire=nombre_de("select etat from affaire where id=".$_SESSION['affaire_en_cour']);

if ($change_statut>0)
	{
	$recap=change_statut_simple($_SESSION['of_en_cour'],$change_statut-1,1,dftoda($date),$ri,$ra);
		$tab_recap="<table class=forumline width=70% cellSpacing=1 cellPadding=2 border=0 align=center>
					<tr class=m3>
						<td>N° bon</td>
						<td>Désignation</td>
						<td>Statut</td>
					</tr>".
					$recap
				."</table>";
	}

//modifier tps_p + tps_u + d_fin_sap+tx_gt
if ($save == 5)
	{
	for ($i=0;$i < count($pg_id);$i++)
		{
		$vf = new valid_form;
		$vf->add("tx_gt",$tx_gt[$i]);
		$vf->add("tps_devis",$tps_devis[$i]);
		$vf->add("tps_obj",$tps_devis[$i]);
		$vf->add("avt",$avt[$i]);
		$vf->add("cycle_prev",$cycle_prev[$i]);
		$vf->add("d_p_deb",dftoda($d_p_deb[$i]));
		$vf->add("d_p_fin",dftoda($d_p_fin[$i]));
		if ($avt[$i]==100)
			{
			if($d_fin_sap[$i]=='0000-00-00')$d_fin_sap[$i]=date("Y-m-d");
			$vf->add("d_fin_sap",dftoda($d_fin_sap[$i]));
			$vf->add("statut",2);
			$vf->add("clos",1);
			}
		if (($cloture[$i]==1) and ($avt[$i]<100))
			{
			$vf->add("statut",1);
			$vf->add("clos",0);
			}
		if($avt[$i]<>$avt_old[$i])$vf->add("maj_avt",date("Y-m-d"));
		$vf->update("phase_i"," where id = '".$pg_id[$i]."'");
		$vf->log(__FILE__,__LINE__,DL_1);
		}
	of_verif_cloture($_SESSION["affaire_en_cour"],1);
	maj_affaire($_SESSION["affaire_en_cour"]);
	}

$sql="SELECT * FROM of where id = '".$_SESSION["of_en_cour"]."' limit 1";
$res=my_query($sql);
$nb_ligne=mysql_num_rows($res);
$row = mysql_fetch_array($res);

$id_gamme = $row["id_gamme"];
$valid = $row["valid"];
$quantite = $row["quantite"];
$etat = $row["etat"];
$ref = $row["ref"];
$numero_client = $row["numero_client"];
$of_client = $row["of_client"];
$dedoublement= $row['dedoublement'];

$n_lot = $row['n_lot'];

if($valid == 0)valid_of($_SESSION["of_en_cour"],$id_gamme);

//ajout d'une phase dans cet of
if ($save == 6)
	{
	$p_suiv=phase_avant($_SESSION["of_en_cour"], $numero,0);
	$p_prec=phase_avant($_SESSION["of_en_cour"], $numero);

	//ajout si pas de phase suivante ou phase suivante en disp ou of clos
	if (((is_array($p_suiv))and($p_suiv['statut']==0))or(!(is_array($p_suiv)) ))
		{
		if(($numero>0)and(nombre_de("select count(*) from phase_i where id_of = '".$_SESSION["of_en_cour"]."' and numero = '".$numero."' ")==0))
			{
			$gt_code= nombre_de("select code from gt where id = '".$id_gt."' ");
			$tx_gt= nombre_de("select tx_horaire from gt where id = '".$id_gt."' ");
			$section= nombre_de("select section from gt where id = '".$id_gt."' ");
			$quantite=nombre_de("select quantite from of where id = '".$_SESSION['of_en_cour']."'");

			$vf3 = new valid_form ;
			$vf3->add("id_affaire", $_SESSION["affaire_en_cour"]);
			$vf3->add("id_of", $_SESSION["of_en_cour"]);
			$vf3->add("id_gt", $id_gt);
			$vf3->add("tx_gt", $tx_gt);
			$vf3->add("gt_code", $gt_code);
			$vf3->add("numero", $numero);
			$vf3->add("section", $section);
			$vf3->add("quantite", $quantite);
			$vf3->add("tps_obj", $tps_obj);
			$vf3->add("tps_devis", $tps_obj);
			$vf3->add("tps_obj_ori", $tps_obj);
			$vf3->add("tps_devis_ori", $tps_obj);
			$vf3->add("designation", $designation2);
			$vf3->add("notice", $notice);
			$vf3->add("commentaire", $commentaire);
			$vf3->add("cycle_prev", $cycle_p);
			$vf3->insert("phase_i");
			$vf3->log(__FILE__,__LINE__,DL_1);
			
			oftopointeuse($_SESSION["of_en_cour"]);
			}
			else
			{
			echo "<h3>Erreur : ce numéro de phase est déjà utilisé dans cette gamme !</h3>";
			}
			if ((is_array($p_prec)) and (datotimestamp($p_prec['d_p_fin'])>0))
				{
				phase_date_prev($_SESSION["of_en_cour"], $p_prec['d_p_fin'], $p_prec['numero']);
				}
		}
		else
		{
		echo "<h3>Erreur : la phase suivante ".$p_suiv['numero']." est déjà distribuée !</h3>";
		}
	}

//Cloture de toutes les phases de l'of
if ($save==41)cloture_of($_SESSION["of_en_cour"]);

if(($save == 2)and(d_ok(202)))
{

	$vf = new valid_form ;
	$vf->add("id_of", $id_of);
	$vf->add("id_phase_i", $id_phase_i);
	$vf->add("status", $statut_qual);
	$vf->add("d_deb", dftoda($d_deb));

	if (($id_of > 0)and($id_phase_i>0)and($d_deb<>""))
	{
	$vf->insert("of_histo");
	$vf->log(__FILE__,__LINE__,DL_1);
	}

	my_query("update of set bloque_qualite=1 where id=$id_of");
}

if(($id_del_histo > 0)and(d_ok(202)))
	{
	del_visit(__FILE__,__LINE__,DL_1,"of_histo","where id = $id_del_histo");
	$res=my_query('SELECT id_phase_i , sum(nb_jour)as nb_jour FROM of_histo WHERE id_of = '.$_SESSION["of_en_cour"].' and d_fin <> "0000-00-00" group by id_phase_i');
	while ($row=mysql_fetch_array($res))
		{
		my_query("update phase_i set cycle_qual = '".$row["nb_jour"]."' where id = '".$row["id_phase_i"]."' ");
		}
	$x = nombre_de('select count(*) from of_histo where  id_of = '.$_SESSION["of_en_cour"].' and d_fin = "0000-00-00" limit 1');
	my_query("update of set bloque_qualite = $x where  id = ".$_SESSION["of_en_cour"].";");
	}


$page = new page;
$page->head("Avancement de phase de l'OF ".format_0($_SESSION["of_en_cour"],6));

$page->body($body);
$page->entete("OF : ".format_0($_SESSION["of_en_cour"],6)." ".$ref." (Affaire : ".format_0($_SESSION["affaire_en_cour"],5).", Ordre : $numero_client, OF Client : $of_client)");
$page->add_button(1,1,"of_gamme_consulter.php?change_of=1");
$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
$page->add_button(3,1,"of_gamme_consulter.php?change_of=2");
$page->add_button(0,2);
if(d_ok(204)and $etat_affaire < 4)$page->add_button(4,1,"validation();");
$page->add_button(0,2);
if(d_ok(220)and $etat_affaire < 4)$page->add_button(5,1,"of_phase_ajouter.php","Ajouter une phase à cet OF");
$page->add_button(0,2);
if(d_ok(220))$page->add_button(12,1,"of_bon_consulter.php","Impression des bons");
if((d_ok(161))and ($type_affaire!=1)) $page->add_button(15 ,1, "imp_prd()","Imprimer le dossier de production");
$page->add_button(0,2);
$page->add_button(0,2);
$page->add_button(0,2);
if(d_ok(203) and ($etat < 2))$page->add_button(11,1,"of_gamme_consulter.php?save=41","Valider cette pièce","principal","Attention ceci va cloturer les bons de cet OF !");
$page->add_button(0,2);
if($type_affaire==1)$page->add_button(26,1,"of_consulter_statut.php?mode=1&r_of=".$_SESSION['of_en_cour']."&retour=of_gamme_consulter.php","Historique des changements de statut");
$page->add_button(0,2);

if (($etat==1)and($quantite>1) and ($dedoublement==0) and (nombre_de("select id from phase_i where id_of=".$_SESSION['of_en_cour']." and statut=1 limit 1")>0) ) $page->add_button(15,1,"dedoublement();");

$page->fin_entete();
$page->datescript();

if($valid>0)$img='<img style="cursor:pointer;" src="images/plus.gif" title="Ajouter une phase dans cet OF" onclick="document.location=\'of_phase_ajouter.php\';">';
?>
<script src="/js/ajax.js" type="text/javascript"></script>

<script LANGUAGE="JavaScript">

var fois=0;

function validation()
	{
	if(fois==0){fois++;document.f1.submit();}
	}

function dedoublement()
	{
	if (document.getElementById('dedoublement').style.visibility=="visible")
		{
		document.getElementById('dedoublement').style.visibility="hidden";
		}
		else
		{
		document.f2.numero_client.value='';
		document.getElementById('span_client_ok').innerHTML='';
		document.getElementById('dedoublement').style.visibility="visible";
		document.f2.numero_client.focus();
		}
	}
	
function imp_prd()
	{
	//document.write(document.f1.chid.length);
	document.f1.action='affaire_fiche_production.php';
	document.f1.target='_blank';
	document.f1.submit();
	document.f1.action='of_gamme_consulter.php';
	document.f1.target='_self';

	}
	
function verif_n_client()
	{
	var req = null;
	req=get_xhr();
	req.onreadystatechange = function()
	{ 
		if(req.readyState == 4)
		{
			if(req.status == 200)
			{
				document.getElementById("span_client_ok").innerHTML=req.responseText;
			}	
			else	
			{
			alert("Error: returned status code " + req.status + " " + req.statusText);
			}
		}
	};
	var url ="req_ajax.php?id_req=6&numero_client=" + document.getElementById("numero_client").value ;
	req.open("POST", url, true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send(null);
	}

</script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="js/overlib.js"></script>
<script LANGUAGE="JavaScript" src="js/ajax.js"></script>
<script LANGUAGE="JavaScript" src="js/Update.js"></script>
<script language="JavaScript">

function dup_sap()
	{
	var j = document.getElementById('nb_ligne2').value;
	var t = document.getElementById('dr_ori').value;
	for (var i=0; i < j;i++) 
		{
		if(document.getElementById('sel'+i).value=='')document.getElementById('sel'+i).value = t;
		}
	}

function of_ok()
	{
	if (document.getElementById('numero_client').value != '')
		{
		if (document.getElementById('client_ok').value=='1')
			{
			return true;
			}
			else
			{
			alert ("Le numéro client saisi est invalide !");
			return false;
			}
		}
		else
		{
		alert("Erreur lors de la saisie !");
		return false;
		}
	}
	

function maj_statut(sens)
	{
	document.getElementById('change_statut').value=sens;
	document.f3.submit();
	}

var pointage_move = new Update("pointage_move","interne_formation","couts","id");
pointage_move.setParam("size=8");
pointage_move.setAction(4);
</script>

<form name="f1"  method="post" action="of_gamme_consulter.php"   target="principal">
<?
//aero
if ($type_affaire==1)
	{
	$sql="SELECT * FROM phase_i where id_of = '".$_SESSION["of_en_cour"]."' order by numero asc";
	$res=my_query($sql);
		echo '<TABLE class=forumline cellSpacing=1 cellPadding=4 width="100%" align=center>
		<tr class=m3>
			<td colspan=4></td>';
			if($colspan_moin<>-2)echo '
			<td width="30px" colspan='.(2 + $colspan_moin).'>Temps alloué</td>';
			echo '
			<td >&nbsp;</td>
			<td colspan=3>Quantité livrée</td>
			<td rowspan=2>Date Debut</td>
			<td >&nbsp;</td>
			<td rowspan=2>Date Fin</td>
			<td colspan=3>Cycle</td>
			<td colspan=2>Dates prév.</td>
			<td rowspan=2>Cumul Ecart</td>
			<td rowspan=2>Tps passé</td>';
			if($colspan_moin<>-2)echo '
			<td colspan='.(2 + $colspan_moin).'>Coeff.</td>';
		echo '<td rowspan=2 title="Facturation"><img src="images/money.png"></td>
			<TD rowspan=2 title="Réalisation"><img src="images/site1.gif"></TD>
			<TD rowspan=2 title="Livraison"><img src="images/next.gif"></TD>
		</tr>
		<tr class=m3>
			<td>Numéro</td>
			<td>N° Bon</td>
			<td>GT</td>
			<td>Designation</td>';
			if($d_devis)echo '<td>Devis</td>';
			if($d_obj)echo '<td>Obj.</td>';
			echo '<td>Quantité</td>
			<td>OK</td>
			<td>RI</td>
			<td>RA</td>
			<td>Statut</td>
			<td>Prév.</td>
			<td>Réel</td>
			<td>Ecart</td>
			<td>Debut</td>
			<td>Fin</td>';
			if($d_obj)echo '<td>Obj</td>';
			if($d_devis)echo '<td>Devis</td>';
		echo '</tr>';
		
		$cumul_ecart=0;
	while($row = mysql_fetch_array($res))
		{
		if (($ic % 2)==0){$cid="class=\"cel2\"";}else {$cid="class=\"cel1\"";}
		$ecart=0;
		if ($row["statut"]==2)
			{
			$ecart=($row["cycle_prev"]-$row["cycle_reel"]);
			$total_ecart+=$ecart;
			$total_cumul_ecart=$row['cumul_ecart'];
			$total_reel+=$row['cycle_reel'];
			$total_tps_passe+=$row["tps_reel"];
			}
   
		/*
		$t1='';
		if($row["cycle_annule"]>0)
			{
			$annule+=$row["cycle_annule"];
			$t1=' title="Cycle Annulé : '.$row["cycle_annule"].'"';
			$t2=' title="Cycle Annulé : '.$annule.'"';
			}
		*/
		$total_prev+=$row['cycle_prev'];
		$total_tps_int+=$row['tps_obj'];
		$total_tps_devis+=$row['tps_devis'];
		$j_machine = dbtodata("select id, nom from gamme_machine order by nom asc; ");
		$info = "<ul><li>Mt sous-traitance : ".$row["mt_st"]."<li>Tps machine : ".$row['tps_machine']." ".$j_machine[$row["id_machine"]]."<li>Prix vente : ".$row['prix_vente']."&euro;<li>Commentaire : ".$row['commentaire']."</ul>";

		if($row["is_facturation"]==1)$f_class="m3";else $f_class="cel2";
		
		if($row["id_gamme"]>0){$f_class2='cel2';}
		else if($row["id_dtnc"]>0){$f_dtnc=' title="Bon de retouche DTNC '.$row["id_dtnc"].'"';$f_class2='cel3';}
		else {$f_dtnc=' title="Phase ajoutée manuellement"';$f_class2="cel4";}
		
		if($row[is_realisation] == 1) {$is_realisation='<img src="images/tick.png">';}else {$is_realisation='';}
		if($row[is_livraison] == 1) {$is_livraison='<img src="images/tick.png">';}else {$is_livraison='';}
		
		echo   '<tr align=center>
				<td class='.$f_class2.$f_dtnc.'>'.format_0($row["numero"],4).'</td>
				<td class='.$f_class2.$f_dtnc.'>'.format_0($row["id"],8).'</td>
				<td class=cel1>'.$row["gt_code"].'</td>
				<td nowrap class=cel1 onmouseover="return overlib(\''.addslashes($info).'\', CAPTION,\'&nbsp;<img src=images/info.png > Information\' );" onmouseout="return nd();">'.$row["designation"].'</td>';
				if($d_devis)echo '<td class=cel2 title="Tps devis origine : '.$row['tps_devis_ori'].'">'.$row["tps_devis"].'</td>';
				if($d_obj)echo '<td class=cel2 title="Tps obj origine : '.$row['tps_obj_ori'].'">'.$row["tps_obj"].'</td>';
				echo '<td class=cel1 title="'.$row["facturation"].'">'.$row["quantite"].'</td>
				<td class=cel2>'.$row["etat_ok"].'</td>
				<td class=cel2>'.$row["etat_ri"].'</td>
				<td class=cel2>'.$row["etat_ra"].'</td>
				<td class=cel1>'.datodf($row["date_deb"]).'</td>
				<td class=cel2>'.$j_statut[$row["statut"]].'</td>
				<td class=cel1>'.datodf($row["d_fin_sap"]).'</td>
				<td class=cel2>'.$row["cycle_prev"].'</td>
				<td class=cel2>'.$row["cycle_reel"].'</td>
				<td class=cel2>'.$ecart.'</td>
				<td class=cel1>'.datodf($row["d_p_deb"]).'</td>
				<td class=cel1>'.datodf($row["d_p_fin"]).'</td>
				<td class=cel2>'.$row['cumul_ecart'].'</td>
				<td class=cel1>'.$row["tps_reel"].'</td>';
				if($d_obj)echo '<td class=cel2>'.round(div($row["tps_obj"],$row["tps_reel"]),3).'</td>';
				if($d_devis)echo '<td class=cel2>'.round(div($row["tps_devis"],$row["tps_reel"]),3).'</td>';
			echo '<td class='.$f_class.' title="'.datodf($row["d_fin_sap_facture"]).'"><img src="images/statut'.$row["facturation"].'.gif"></td>
				<TD class="cel2">'.$is_realisation.'</TD>
				<TD class="cel2">'.$is_livraison.'</TD>
			</tr>';
		$ic++;
		}
	echo '<tr class=m3>
			<td colspan=4>&nbsp;</td>';
			if($d_devis)echo '<td>'.$total_tps_devis.'</td>';
			if($d_obj)echo '<td>'.$total_tps_int.'</td>';
			echo '<td colspan=7>&nbsp;</td>
			<td>'.$total_prev.'</td>
			<td>'.$total_reel.'</td>
			<td>'.$total_ecart.'</td>
			<td colspan=2>&nbsp;</td>
			<td '.$t2.'>'.$total_cumul_ecart.'</td>
			<td>'.$total_tps_passe.'</td>';
			if($d_obj)echo '<td>'.round(div($total_tps_int,$total_tps_passe),3).'</td>';
			if($d_devis)echo '<td>'.round(div($total_tps_devis,$total_tps_passe),3).'</td>';
		echo '<td colspan=3></td></tr>';
	echo '</table>
	</form>
	<input type=hidden id="nb_ligne2" name="nb_ligne2" value="'.$ic.'">';
	?>
	<form name="form_n_lot" method="post" action="of_gamme_consulter.php">
	<input type="hidden" name="n_lot_save" value="0" />
	<table class=forumline cellSpacing=1 cellPadding=4 width="15%" align=center>
	<tr class="m3"><td colspan=2>N° lot :</td></tr>
	<?
		foreach(explode(';;',$n_lot) as $lot)
		{
			if($lot <> '')
			{
				echo '<tr class=cel2><td>'.$lot.'</td><td></td></tr>';
			}
		}
	?>
	<tr class="cel2"><td><input type="text" name="n_lot" id="n_lot" onkeypress="if(event.keyCode==13){this.parentNode.getElementById('save_n_lot').click();}" /></td><td><img id="save_n_lot" src="images/save.gif" alt="Enregistrer ce n° de lot" onclick="save_new_n_lot(this.parentNode.parentNode,<? echo $_SESSION["of_en_cour"]; ?>);" /></td></tr>
	</table>
	</form>
	<script language="JavaScript">
		function save_new_n_lot(elt,of)
		{
			//alert(elt.innerHTML);
			var n_lot = document.getElementById('n_lot').value;
			if(n_lot != '')
			{
				var req = get_xhr();
	
				req.onreadystatechange = function()
				{
					if(req.readyState == 4)
					{
						if(req.status == 200)
						{
							document.getElementById('save_n_lot').parentNode.innerHTML = '';
						}	
						else	
						{
							alert("Error: returned status code " + req.status + " " + req.statusText);
						}
					}
				};
				var url ='req_ajax.php?id_req=39&of='+of+'&new_n_lot='+n_lot;
				req.open("POST", url, true);
				req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				req.send(null);
			}
		}
	</script>
	<?
	if(d_ok(968))
		{
		?>
		<form name="f3" method="post" action="of_gamme_consulter.php">
		<input type=hidden id=change_statut name=change_statut >
		<table class=forumline cellSpacing=1 cellPadding=4 width="30%" align=center>
			<tr class=m3>
				<td rowspan=2><img src="images/gauche.gif" onclick="maj_statut(1);"></td>
				<td colspan=3>Changer le statut </td>
				<td rowspan=2><img src="images/droite.gif" onclick="maj_statut(2);"></td>
			</tr>
			<tr align=center>
				<td class=cel2>Date &nbsp;<INPUT onclick="return showCalendar('sel','%d/%m/%Y');"  id=sel class='cel2' size=10 type='text'  name='date' value='<? echo $date; ?>'><? echo calendar('sel');?></td>
				<td class=cel2>Rebuts Interne:&nbsp;<input name='ri' class='cel2' size='5' type='text'></td>
				<td class=cel2>Rebuts Airbus:&nbsp;<input name='ra' class='cel2' size='5' type='text'></td>
			</tr>
		</table>
		</form>
		<?
		echo $tab_recap;
		}
	
		// of en qualité

		?>

		<!-- Données des avenants -->
		<script LANGUAGE="JavaScript" src="js/ajax.js"></script>
		<script LANGUAGE="JavaScript" src="js/Update.js"></script>
		<script LANGUAGE="JavaScript">
		var upd_d_f = new Update("upd_d_f","of_histo","d_fin","id");
		upd_d_f.setAction(1);
		
		</script>
		<?
		$res = my_query("select ofh.* , pi.numero , pi.gt_code , pi.designation from of_histo as ofh left join phase_i pi on ofh.id_phase_i = pi.id where ofh.id_of = '".$_SESSION['of_en_cour']."' order by ofh.d_deb asc;");
		$d_fin_ok=true;
		while($row=mysql_fetch_array($res))
			{
			if($row['d_fin']==0) $d_fin_ok=false;
			$avenants .= '
			<tr class=cel1 align=center>
				<td>'.format_0($row['numero'],4).'</td>
				<td>'.$row['gt_code'].'</td>
				<td>'.$j_statut_qual[$row['status']].'</td>
				<td>'.$row['designation'].'</td>
				<td>'.datodf($row['d_deb']).'</td>
				<td ondblclick="upd_d_f.form(this,'.$row['id'].',\'D\');">'.datodf($row['d_fin']).'</td>
				<td><A href="of_gamme_consulter.php?id_del_histo='.$row['id'].'"><img src="images/trash_2.png" title="supprimer"></A></td>
			</tr>
			';
			}

		if($d_fin_ok)
		{
		?>
		<!-- Formulaire d'ajout d'un etat qualité-->
		<form method="post" name="formulaire2" action="of_gamme_consulter.php"  target="principal">
			<input type=hidden name="save" value="2">
			<input type=hidden name="id_of" value="<? echo $_SESSION['of_en_cour'];?>">
			<table  TABLE class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
			<tr >
			<td class="m3" >
			<? 
			echo '
			&nbsp; Statut : '.liste_d2($j_statut_qual,0,"statut_qual").'
			&nbsp; Numéro : '.liste_db("select id , concat(numero,' ', gt_code) from phase_i where id_of = ".$_SESSION['of_en_cour'],"","id_phase_i").'
			&nbsp; Début : <input  readonly onfocus="return showCalendar(\'sel3\', \'%d/%m/%Y\');" id=sel3  type="text" name="d_deb" size=10 maxlength=10 value="'.date('d/m/Y').'" >'.calendar('sel3').'
			&nbsp;&nbsp; &nbsp;
			<input type="submit" id="button3" value="Go"  >';
			?>
			</td>
			</tr>
			</table>
		</form>
		<?
		}
		?>

		<!--Affichage des avenants-->
		<TABLE class=forumline width=100% cellSpacing=1 cellPadding=1 align=center border=0>
		<tr>
			<td class="m3" width="10%">Phase</td>
			<td class="m3" width="10%">GT</td>
			<td class="m3" width="10%">Statut qualité</td>
			<td class="m3" width="40%">Désignation</td>
			<td class="m3" width="10%">Début</td>
			<td class="m3" width="10%">Fin</td>
			<td class="m3" width="3%"><img src='images/trash_2.png'></td>
		</tr>
		<?
		$nb_ligne=1;

		echo $avenants;

		echo '</table>';

	}
	else
	{
	$ic=0;
	$ic_d=0;
	$sql="SELECT * FROM phase_i where id_of = '".$_SESSION["of_en_cour"]."' order by section , numero asc";
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);

	if ($nb_ligne)
		{
		echo '<input type="hidden"  name="save" value="5">
		<TABLE class=forumline cellSpacing=1 cellPadding=4 width="100%" align=center>
		<tr class=m3>
			<td onclick="if (document.f1.seltout.value==0){sel_all();}else{dsel_all();}">Tout<input type=hidden name="seltout" value="0"></td>
			<td >Numéro</td>
			<td >GT</td>
			<td >Designation</td>
			<td >Tps devis</td>
			<td >Tx horaire</td>
			<td >AVT</td>
			<td >Cycle</td>
			<td >Debut Prev.</td>
			<td >Fin Prev.</td>
			<td >Fin<br><input class=m3 onclick=" return showCalendar(\'dr_ori\', \'%d/%m/%Y\');"  id="dr_ori" type=text size=9 maxlenght=10  name="dr_ori" value="" ><img src="images/bas.gif" onclick="dup_sap();">'.calendar('dr_ori').'</td>
			<td >Bon</td>
			<td >Tps passé</td>
			<td >Clos</td>
		</tr>';
		}
	while($row = mysql_fetch_array($res))
		{
		$read='';

		//if (($row['id_gamme']==0) and ($row["clos"]==1)) {$read=" readonly ";}
		//ondblclick="pointage_move.form(this,'.$row["id"].')"
		echo '<tr align=center>
				<td class=cel2>
				<input type=checkbox id="chid['.$ic.']" name="chid['.$ic.']" value="'.$row["id"].'"></td>
				<td class=cel2><input type=hidden name="pg_id['.$ic.']" value="'.$row["id"].'"><b>'.format_0($row["numero"],4).'</b></td>
				<td class=cel2>'.$row["gt_code"].'</td>
				<td class=cel1>'.$row["designation"].'</td>
				<td class=cel2><input '.$read.' align=center class=cel2 type=text size=8 name="tps_devis['.$ic.']" value="'.$row["tps_devis"].'"></td>
				<td class=cel2><input '.$read.' align=center class=cel2 type=text size=8 name="tx_gt['.$ic.']" value="'.$row["tx_gt"].'"></td>
				<td class=cel2><input '.$read.' align=center class=cel2 type=text size=8 name="avt['.$ic.']" value="'.$row["avt"].'"><input type=hidden name="avt_old['.$ic.']" value="'.$row["avt"].'"></td>
				<td class=cel1><input '.$read.' align=center class=cel1 type=text size=8 name="cycle_prev['.$ic.']" value="'.$row["cycle_prev"].'"></td>
				<td class=cel2><input class=cel2 onclick="return showCalendar(\'sel'.$ic_d.'\', \'%d/%m/%Y\');"  id="sel'.$ic_d.'"  type="text" maxlength="10" name="d_p_deb['.$ic.']" size="10" value="'.datodf($row["d_p_deb"]).'">'.calendar('sel'.$ic_d).'</td>
				<td class=cel2><input class=cel2 onclick="return showCalendar(\'sel'.($ic_d+1).'\', \'%d/%m/%Y\');"  id="sel'.($ic_d+1).'"  type="text" maxlength="10" name="d_p_fin['.$ic.']" size="10" value="'.datodf($row["d_p_fin"]).'">'.calendar('sel'.($ic_d+1)).'</td>
				<td class=cel1><input class=cel1 onclick="return showCalendar(\'sel'.($ic_d+2).'\', \'%d/%m/%Y\');"  id="sel'.($ic_d+2).'"  type="text" maxlength="10" name="d_fin_sap['.$ic.']" size="10" value="'.datodf($row["d_fin_sap"]).'">'.calendar('sel'.($ic_d+2)).'</td>
				<td class=cel2 >'.format_0($row["id"],8).'</td>
				<td class=cel1 >'.$row["tps_reel"].'</td>
				<td class=cel2><img title="'.$j_yn[$row["clos"]].' '.datodf($row["maj_avt"]).'" src="images/statut'.$row["clos"].'.gif">
	<input type=hidden  id=cloture name="cloture['.$ic.']" value="'.$row["clos"].'"></td>
			</tr>';
		$ic++;
		$ic_d+= 3;
		}
	echo '</table>
	</form>
	<input type=hidden id="nb_ligne2" name="nb_ligne2" value="'.$ic.'">';
	}
?>
<script LANGUAGE="JavaScript">
function sel_all()
{
<?
for($i=0;$i<$ic;$i++)echo "document.getElementById('chid[".$i."]').checked=true;\n";
?>
document.f1.seltout.value=1;
}
function dsel_all()
{
<?
for($i=0;$i<$ic;$i++)echo "document.getElementById('chid[".$i."]').checked=false;\n";
?>
document.f1.seltout.value=0;
}
</script>

<span id="dedoublement" style="visibility:hidden;">
<table class="GB_overlay">
	<tr>
		<td>
		<form name="f2"  onsubmit="return of_ok();" method="post" action="of_ajouter.php" target="principal">
		<input type=hidden name=dedoublement value=1>
		<TABLE class=forumline2 cellSpacing=1 cellPadding=4 width="50%" align=center>
			<tr class=m3>
				<td colspan=2>
					DEDOUBLEMENT 
				</td>
			</tr>
			<tr>
				<td width=40% class=cel2>Nouveau Numero Client</td>
				<td class=cel1><input type=text id=numero_client name=numero_client onchange='verif_n_client()'><span id=span_client_ok></span></td>
			</tr>
			<tr>
				<td class=cel2>Quantite</td>
				<td class=cel1>
					<select name=quantite id=quantite>
					<?
					for ($i=1;$i<$quantite;$i++)
						{
						echo '<option value='.$i.'>'.$i.'</option>';
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class=m3 colspan=2 align=center>
					<input type=submit  value='Ok' >
					<input type=button value="Annuler" onclick='document.getElementById("dedoublement").style.visibility="hidden";'>
				</td>
			</tr>
		</table>
		</form>
		</td>
	</tr>
</table>
</span>
<?
echo pied_page();
?>
