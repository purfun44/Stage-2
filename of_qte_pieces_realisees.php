<?php
include("fonction.php");
if (!d_ok(973)){header("location: espace_doc.php");exit;}

$_SESSION['en_cour']="of_qte_pieces_realisees.php?print=".$print;
$j_entite=dbtodata("SELECT id, nom FROM entite");

if($parent_id > 0){$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori == 1)or($_SESSION[$_SESSION['en_cour']]=="")){$_SESSION[$_SESSION['en_cour']]=parent(973);}

if (!$_SESSION['of_qte_pieces_realisees_saisie']>0){$r_saisie=1;$mode=1;}

if($mode=="9")
	{
	$r_ref="";
	$r_entite="0";
	$r_cat="0";
	$r_famille="0";
	$r_avion="0";
	$r_date="";
	$r_date2="";
	$r_semaine=strftime("%V");
	$r_annee=date("Y");
	$r_semaine2=strftime("%V");
	$r_annee2=date("Y");
	$r_saisie=1;
	$r_saisietot=0;
	$r_piece=0;
	$mode="1";
	}
	
if(!isdf($_SESSION['of_qte_pieces_realisees_date']))$r_date=date('d/m/Y');
if(!isdf($_SESSION['of_qte_pieces_realisees_date2']))$r_date2=date('d/m/Y');

if($mode=="1")
	{
	$_SESSION['of_qte_pieces_realisees_ref']=$r_ref;
	$_SESSION['of_qte_pieces_realisees_entite']=$r_entite;
	$_SESSION['of_qte_pieces_realisees_cat']=$r_cat;
	$_SESSION['of_qte_pieces_realisees_famille']=$r_famille;
	$_SESSION['of_qte_pieces_realisees_avion']=$r_avion;
	$_SESSION['of_qte_pieces_realisees_date']=$r_date;
	$_SESSION['of_qte_pieces_realisees_date2']=$r_date2;
	$_SESSION['of_qte_pieces_realisees_semaine']=$r_semaine;
	$_SESSION['of_qte_pieces_realisees_annee']=$r_annee;
	$_SESSION['of_qte_pieces_realisees_semaine2']=$r_semaine2;
	$_SESSION['of_qte_pieces_realisees_annee2']=$r_annee2;
	$_SESSION['of_qte_pieces_realisees_saisie']=$r_saisie;
	$_SESSION['of_qte_pieces_realisees_saisietot']=$r_saisietot;
	$_SESSION['of_qte_pieces_realisees_piece']=$r_piece;
	$p_en=1;
	}
	
if($p_en>0)$_SESSION["of_qte_pieces_realisees_p_en"]=$p_en;
if($mode>0){$p_en=1;}else{$p_en=$_SESSION["of_qte_pieces_realisees_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["of_qte_pieces_realisees_p_en"]=$p_en;

$of_qte_pieces_realisees_req="";
$r_ref=$_SESSION['of_qte_pieces_realisees_ref'];
$r_entite=$_SESSION['of_qte_pieces_realisees_entite'];
$r_cat=$_SESSION['of_qte_pieces_realisees_cat'];
$r_famille=$_SESSION['of_qte_pieces_realisees_famille'];
$r_avion=$_SESSION['of_qte_pieces_realisees_avion'];
$r_date=$_SESSION['of_qte_pieces_realisees_date'];
$r_date2=$_SESSION['of_qte_pieces_realisees_date2'];
$r_semaine=$_SESSION['of_qte_pieces_realisees_semaine'];
$r_annee=$_SESSION['of_qte_pieces_realisees_annee'];
$r_semaine2=$_SESSION['of_qte_pieces_realisees_semaine2'];
$r_annee2=$_SESSION['of_qte_pieces_realisees_annee2'];
$r_saisie=$_SESSION['of_qte_pieces_realisees_saisie'];
$r_saisietot=$_SESSION['of_qte_pieces_realisees_saisietot'];
$r_piece=$_SESSION['of_qte_pieces_realisees_piece'];

if ($r_ref<>"")$of_qte_pieces_realisees_req.=" AND of.ref like '".$r_ref."%'";
if (is_array($r_entite))$of_qte_pieces_realisees_req.=" AND of.id_entite in (".tabtosql($r_entite).")";
if (is_array($r_cat))$of_qte_pieces_realisees_req.=" AND of.id_cat in (".tabtosql($r_cat).")";
if (is_array($r_famille))$of_qte_pieces_realisees_req.=" AND of.id_famille in (".tabtosql($r_famille).")";
if (is_array($r_avion))$of_qte_pieces_realisees_req.=" AND of.id_avion in (".tabtosql($r_avion).")";

if($r_saisie==1)
	{
	if((isdf($r_date))and(isdf($r_date2))) {$of_qte_pieces_realisees_req.=" AND pi.d_fin_sap BETWEEN '".dftoda($r_date)."' AND '".dftoda($r_date2)."'";}
	else if(isdf($r_date)) {$of_qte_pieces_realisees_req.=" AND pi.d_fin_sap='".dftoda($r_date)."'";}
	}
else if ($r_saisie==2)
	{$of_qte_pieces_realisees_req.=" AND pi.d_fin_sap BETWEEN '".dftoda(weektoday($r_semaine,$r_annee,1))."' AND '".dftoda(weektoday($r_semaine2,$r_annee2,7))."'";}
if($r_piece==0) {$of_qte_pieces_realisees_req.=" AND pi.is_realisation=1";}
else {$of_qte_pieces_realisees_req.=" AND pi.is_livraison=1";}

$_SESSION['of_qte_pieces_realisees_req']=$of_qte_pieces_realisees_req;

if($_SESSION['of_qte_pieces_realisees_trier']=='') {$_SESSION['of_qte_pieces_realisees_trier']='of.ref';}
if(isset($trier)) {$_SESSION['of_qte_pieces_realisees_trier']=$trier;}

if($_SESSION['of_qte_pieces_realisees_ordre']=='') {$_SESSION['of_qte_pieces_realisees_ordre']='ASC';}
if(isset($ordre)){$_SESSION['of_qte_pieces_realisees_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['of_qte_pieces_realisees_trier'];
$tf->ordre_ec=$_SESSION['of_qte_pieces_realisees_ordre'];
$tf->page_ec=$_SESSION['en_cour'];

if($print==1)
	{
	if($r_piece==0){$piece="réalisées";} else {$piece="livrées";}
	echo '<HTML><HEAD>'.$j_meta.$j_style.'</HEAD><BODY class="vide"><CENTER><h2>Pièces '.$piece.'<BR>Date Edition : '.date('d/m/Y').'</h2></CENTER>';
	$j_even_dispo_color[0]='FFFFFF';
	}
else
	{
	$page=new page;
	$page->head("Pièces réalisées / livrées");
	$page->body();
	$page->entete("Pièces réalisées / livrées");
	$page->add_button(1,0);
	$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
	$page->add_button(3,0);
	$page->add_button(0,2);
	$page->add_button(27,1,"of_qte_pieces_realisees.php?print=1","Imprimer");
	$page->fin_entete();
	$page->datescript();
	?>
	<FORM style="position:relative;z-index:1;" method="post" name="f1" action="of_qte_pieces_realisees.php?mode=1" target="principal">
		<TABLE class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
			<TR>
				<TD class="m3">
					Pièces :
					<SELECT id="button" name="r_piece" size="1" onchange="document.formulaire1.submit();">
							<OPTION value="0" <?php if($r_piece==0){echo "selected";}?>>Réalisées</OPTION>
							<OPTION value="1" <?php if($r_piece==1){echo "selected";}?>>Livrées</OPTION>
					</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Affichage :
					<SELECT id="button" name="r_saisietot" size="1" onchange="document.formulaire1.submit();">
							<OPTION value="0" <?php if($r_saisietot==0){echo "selected";}?>>Tout</OPTION>
							<OPTION value="1" <?php if($r_saisietot==1){echo "selected";}?>>Détail</OPTION>
							<OPTION value="2" <?php if($r_saisietot==2){echo "selected";}?>>Sous-totaux</OPTION>
					</SELECT>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Réference :
					<INPUT type="text" name="r_ref" size="20" maxlength="20" value="<?php echo $r_ref;?>" onchange="document.f1.submit();"/>&nbsp;&nbsp;
					<?php
					echo liste_ms("SELECT id, nom FROM entite ORDER BY nom ASC ",$r_entite,"r_entite" ,"UAP Gamme")."&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_cat,g.nom FROM gamme_cat g,of WHERE of.id_cat=g.id ORDER BY g.nom",$r_cat,"r_cat","Catégrorie")."&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_famille,g.nom FROM gamme_famille g,of WHERE of.id_famille=g.id ORDER BY g.nom",$r_famille,"r_famille","Famille")."&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_avion,p.nom FROM piece_avion p,of WHERE of.id_avion=p.id ORDER BY p.nom",$r_avion,"r_avion","Avion");
					?><BR>
					<INPUT type="radio" id="r_saisie1" name="r_saisie" value="2" <?php if($r_saisie==2) {echo "checked=true";}?>/>
					<SPAN onclick="return document.getElementById('r_saisie1').checked=true">
						Semaine :&nbsp;
						<SELECT size="1" name="r_semaine">
							<?php
							for($i=1;$i<54;$i++)
								{
								if($i==$r_semaine) {$s=" selected ";} else {$s="";}
								echo '<OPTION value="'.$i.'" '.$s.'>'.$i.'</OPTION>'."\n";
								}
						echo '</SELECT>&nbsp;
						<SELECT id="button2" name="r_annee" size="1" onclick="document.getElementById(\'r_saisie1\').checked=true">';
							for($i=date("Y");$i>2000;$i--) {if($r_annee==$i) {$s=' selected ';} else {$s='';} echo "\t<OPTION value=$i $s >$i</OPTION>\n";}
						echo '</SELECT>&nbsp;&nbsp;à&nbsp;&nbsp;
						<SELECT size="1" name="r_semaine2">';
							for($i=1;$i<54;$i++) {if($i==$r_semaine2) {$s=" selected ";} else {$s="";} echo '<OPTION value="'.$i.'" '.$s.'>'.$i.'</OPTION>'."\n";}
						echo '</SELECT>&nbsp;
						<SELECT id="button2" name="r_annee2" size="1" onclick="document.getElementById(\'r_saisie1\').checked=true">';
							for($i=date("Y");$i>2000;$i--) {if($r_annee2==$i) {$s=' selected ';} else {$s='';} echo "\t<OPTION value=$i $s >$i</OPTION>\n";}
							?>
						</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</SPAN>
					<INPUT type="radio" id="r_saisie2" name="r_saisie" value="1" <?php if($r_saisie==1) {echo "checked=true";}?>/>
					<SPAN onclick="return document.getElementById('r_saisie2').checked=true">
						Date&nbsp;
						<INPUT onclick="return showCalendar('sel1','%d/%m/%Y');" id="sel1" class="button2" size="11" type="text"  name="r_date" value="<?php echo $r_date;?>"/>
						<?php echo calendar('sel1');?>&nbsp;&nbsp;à&nbsp;&nbsp;
						<INPUT onclick="return showCalendar('sel2','%d/%m/%Y');" id="sel2" class="button2" size="11" type="text"  name="r_date2" value="<?php echo $r_date2;?>"/>
						<?php echo calendar('sel2');?>
					</SPAN>
				</TD>
				<TD class="m3">
					<INPUT type="submit" id="button3" value="Go"/><BR><BR>
					<INPUT id="button3" type="button" value="Clear" onclick="document.location.href='of_qte_pieces_realisees.php?mode=9';"/>
				</TD>
			</TR>
		</TABLE>
	</FORM>
<?php
	}
if($r_saisie==1 && $r_date==""){echo "<BR>Veuillez choisir un intervalle<BR>";exit;}
$entete='<TABLE class="forumline" width="100%" align="center" cellSpacing="1" cellPadding="2" border="0">
	<TR class="m3">';
		$entete.='<TD width="20%">'.$tf->aff("of.ref","Référence",1).'</TD>
		<TD width=20%>'.$tf->aff("of.designation","Désignation",1).'</TD>
		<TD width=10%>'.$tf->aff("of.numero_client","Cde Client",1).'</TD>
		<TD width=5%>'.$tf->aff("of.poste","Poste",1).'</TD>
		<TD width=10%>'.$tf->aff("of.of_client","OF client",1).'</TD>
		<TD width=10%>'.$tf->aff("of.id","OF interne",1).'</TD>
		<TD width=5%>'.$tf->aff("of.id_entite","UAP",1).'</TD>
		<TD width=5%>'.$tf->aff("of.quantite_ok","Quantité ok",1).'</TD>
		<TD width=10%>'.$tf->aff("pi.d_fin_sap","Date fin",1).'</TD>
		<TD width=5%>'.$tf->aff("s","Semaine fin",1).'</TD>';
	$entete.='</TR>';
	echo $entete;
	$req="SELECT of.ref,of.numero_client,of.poste,of.of_client,of.id,of.id_piece,of.id_entite,of.designation,pi.numero,pi.clos,pi.etat_ok ,pi.d_fin_sap, WEEK(pi.d_fin_sap,3) as s
		FROM of
		LEFT JOIN phase_i pi ON pi.id_of=of.id
		WHERE of.id_affaire_type=1 ".$_SESSION['of_qte_pieces_realisees_req']."
		ORDER BY ".$_SESSION['of_qte_pieces_realisees_trier']." ".$_SESSION['of_qte_pieces_realisees_ordre'];
	$res=my_query($req);
	$nb_ligne=mysql_num_rows($res);
	if($nb_ligne==0) {echo "<BR>Aucun enregistrement trouvé<BR>";exit;}

	while($ligne=mysql_fetch_array($res))
		{
		$t[$ligne["id"]]["id"]=$ligne["id"];
		$t[$ligne["id"]]["ref"]=$ligne["ref"];
		$t[$ligne["id"]]["id_piece"]=$ligne["id_piece"];
		$t[$ligne["id"]]["designation"]=$ligne["designation"];
		$t[$ligne["id"]]["numero_client"]=$ligne["numero_client"];
		$t[$ligne["id"]]["poste"]=$ligne["poste"];
		$t[$ligne["id"]]["of_client"]=$ligne["of_client"];
		$t[$ligne["id"]]["id_entite"]=$ligne["id_entite"];
		$t[$ligne["id"]]["numero"]=$ligne["numero"];
		$t[$ligne["id"]]["etat_ok"]=$ligne["etat_ok"];
		$t[$ligne["id"]]["d_fin_sap"]=$ligne["d_fin_sap"];
		$t[$ligne["id"]]["s"]=$ligne["s"];
	
		$tab[$ligne["id_piece"]]["total"]+=$ligne["etat_ok"];
		$tab[$ligne["id_piece"]]["ref"]=$ligne["ref"].' '.$ligne["designation"];
		$total+=$ligne["etat_ok"];
		}
	$ic=0;
	foreach($t as $ligne)
		{
		if(($_SESSION['of_qte_pieces_realisees_trier']=='of.ref')and($piece_ec<>$ligne['id_piece'])and($ic>0)and($r_saisietot!=1))
			{
			if($r_saisietot==2) {$class="cel3";} else {$class="m3";}
			echo '<TR align="center" class="'.$class.'">
				<TD>'.$ref_ec.'</TD>
				<TD>'.$designation_ec.'</TD>
				<TD colspan="4"></TD>
				<TD>'.$uap_ec.'</TD>
				<TD>'.nformat($tab[$piece_ec]["total"],0,1,0).'</TD>
				<TD colspan="2"></TD>
			</TR>';
			}
		if($piece_ec<>$ligne['id_piece']){$idgec=$ligne['ref'];$ic++;}
		if($ic%2 == 0){$class="cel2";} else {$class="cel4";}
		if($r_saisietot!=2)
			{
			echo '<TR class="'.$class.'" align="center">
				<TD>'.$ligne['ref'].'</TD>
				<TD>'.$ligne['designation'].'</TD>
				<TD>'.$ligne['numero_client'].'</TD>
				<TD>'.$ligne['poste'].'</TD>
				<TD>'.$ligne['of_client'].'</TD>
				<TD>'.$ligne['id'].'</TD>
				<TD>'.$j_entite[$ligne['id_entite']].'</TD>
				<TD>'.$ligne['etat_ok'].'</TD>
				<TD>'.datodf($ligne['d_fin_sap']).'</TD>
				<TD>'.$ligne['s'].'</TD>
			</TR>';
			}
		$piece_ec=$ligne['id_piece'];
		$uap_ec=$j_entite[$ligne['id_entite']];
		$ref_ec=$ligne['ref'];
		$designation_ec=$ligne['designation'];

		}
	if(($_SESSION['of_qte_pieces_realisees_trier']=='of.ref')and($r_saisietot!=1))
		{
		if($r_saisietot==2) {$class="cel3";} else {$class="m3";}
		echo '<TR align="center" class="'.$class.'">
			<TD>'.$ref_ec.'</TD>
			<TD>'.$designation_ec.'</TD>
			<TD colspan="4"></TD>
			<TD>'.$uap_ec.'</TD>
			<TD>'.nformat($tab[$piece_ec]["total"],0,1,0).'</TD>
			<TD colspan="2"></TD>
		</TR>';
		}

	echo '<TR height="5" class="cel2"><TD colspan="10"></TD></TR>
	<TR class="m3">
		<TD colspan="7">TOTAL</TD>
		<TD>'.$total.'</TD>
		<TD colspan="2"></TD>
	</TR>';
echo '</TABLE>';
echo pied_page();
?>
