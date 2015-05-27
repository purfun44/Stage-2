<?php
include("fonction.php");
if (!d_ok(993)){header("location: espace_doc.php");exit;}

$j_gamme_famille=dbtodata("SELECT id,nom FROM gamme_famille");
$j_gamme_cat=dbtodata("SELECT id,nom FROM gamme_cat");
$j_piece_avion=dbtodata("SELECT id,nom FROM piece_avion");
$j_entite=dbtodata("SELECT id,nom FROM entite");
$j_section=dbtodata("SELECT code,nom FROM section");

$_SESSION['en_cour']="of_tps_piece_lire.php";

if($parent_id>0) {$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori==1)or($_SESSION[$_SESSION['en_cour']]=="")){$_SESSION[$_SESSION['en_cour']]=parent(994);}
if (!$_SESSION['of_tps_piece_lire_saisie']>0){$r_saisie=1;$mode=1;}
if($mode=="9")
	{
	$r_ref="";
	$r_entite="0";
	$r_cat="0";
	$r_famille="0";
	$r_fab="0";
	$r_avion="0";
	$r_code_gt="0";
	$r_machine="0";
	$r_date="";
	$r_date2="";
	$r_semaine=strftime("%V");
	$r_annee=date("Y");
	$r_semaine2=strftime("%V");
	$r_annee2=date("Y");
	$r_saisie=1;
	$mode="1";
	}

/*$d_devis=0;
$d_obj=0;


if($print>0)
	{
	if(d_ok(952)){$d_devis=1;}
	if(d_ok(954)){$d_obj=1;}
	if(!d_ok(952)){$d_devis=0;$devis=0;}
	if(!d_ok(954)){$d_obj=0;$obj=0;}
	}
else
	{
	if(d_ok(951))$d_devis=1;
	if(d_ok(953))$d_obj=1;
	}
*/
if(!isdf($_SESSION['of_tps_piece_lire_date']))$r_date=date('d/m/Y');
if(!isdf($_SESSION['of_tps_piece_lire_date2']))$r_date2=date('d/m/Y');
if (isset($indice))$indice=1;

if($mode=="1")
	{
	$_SESSION['of_tps_piece_lire_ref']=$r_ref;
	$_SESSION['of_tps_piece_lire_entite']=$r_entite;
	$_SESSION['of_tps_piece_lire_cat']=$r_cat;
	$_SESSION['of_tps_piece_lire_famille']=$r_famille;
	$_SESSION['of_tps_piece_lire_fab']=$r_fab;
	$_SESSION['of_tps_piece_lire_avion']=$r_avion;
	$_SESSION['of_tps_piece_lire_machine']=$r_machine;
	$_SESSION['of_tps_piece_lire_code_gt']=$r_code_gt;
	$_SESSION['of_tps_piece_lire_date']=$r_date;
	$_SESSION['of_tps_piece_lire_date2']=$r_date2;
	$_SESSION['of_tps_piece_lire_semaine']=$r_semaine;
	$_SESSION['of_tps_piece_lire_annee']=$r_annee;
	$_SESSION['of_tps_piece_lire_semaine2']=$r_semaine2;
	$_SESSION['of_tps_piece_lire_annee2']=$r_annee2;
	$_SESSION['of_tps_piece_lire_saisie']=$r_saisie;
	$_SESSION['of_tps_piece_lire_indice']=$indice;
	$p_en=1;
	}
	
if($p_en>0)$_SESSION["of_tps_piece_lire_p_en"]=$p_en;
if($mode>0) {$p_en=1;} else {$p_en=$_SESSION["of_tps_piece_lire_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["of_tps_piece_lire_p_en"]=$p_en;

$of_tps_piece_lire_req="";

$r_ref=$_SESSION['of_tps_piece_lire_ref'];
$r_entite=$_SESSION['of_tps_piece_lire_entite'];
$r_cat=$_SESSION['of_tps_piece_lire_cat'];
$r_famille=$_SESSION['of_tps_piece_lire_famille'];
$r_fab=$_SESSION['of_tps_piece_lire_fab'];
$r_avion=$_SESSION['of_tps_piece_lire_avion'];
$r_machine=$_SESSION['of_tps_piece_lire_machine'];
$r_date=$_SESSION['of_tps_piece_lire_date'];
$r_date2=$_SESSION['of_tps_piece_lire_date2'];
$r_semaine=$_SESSION['of_tps_piece_lire_semaine'];
$r_annee=$_SESSION['of_tps_piece_lire_annee'];
$r_semaine2=$_SESSION['of_tps_piece_lire_semaine2'];
$r_annee2=$_SESSION['of_tps_piece_lire_annee2'];
$r_saisie=$_SESSION['of_tps_piece_lire_saisie'];
$indice=$_SESSION['of_tps_piece_lire_indice'];

if ($r_ref<>"")$of_tps_piece_lire_req.=" AND of.ref like '".$r_ref."%'";
if (is_array($r_entite))$of_tps_piece_lire_req.=" AND of.id_entite in (".tabtosql($r_entite).")";
if (is_array($r_cat))$of_tps_piece_lire_req.=" AND of.id_cat in (".tabtosql($r_cat).")";
if (is_array($r_famille))$of_tps_piece_lire_req.=" AND of.id_famille in (".tabtosql($r_famille).")";
if (is_array($r_fab))$of_tps_piece_lire_req.=" AND g.id_fab in (".tabtosql($r_fab).")";
if (is_array($r_avion))$of_tps_piece_lire_req.=" AND of.id_avion in (".tabtosql($r_avion).")";
if (is_array($r_machine))$of_tps_piece_lire_req.=" AND pi.id_machine in (".tabtosql($r_machine).")";
if (is_array($r_code_gt))$of_tps_piece_lire_req.=" AND pi.id_gt in (".tabtosql($r_code_gt).")";

if ($indice>0){$of_tps_piece_lire_req2 ="of_id_gamme";} else{$of_tps_piece_lire_req2 ="of_id_piece";}
$trier=$of_tps_piece_lire_req2;

if($r_saisie==1)
	{
	if((isdf($r_date))and(isdf($r_date2))) {$of_tps_piece_lire_req.=" AND pi.d_fin_sap BETWEEN '".dftoda($r_date)."' AND '".dftoda($r_date2)."'";}
	else if(isdf($r_date)) {$of_tps_piece_lire_req.=" AND pi.d_fin_sap='".dftoda($r_date)."'";}
	}
else if($r_saisie==2) {$of_tps_piece_lire_req.=" AND pi.d_fin_sap BETWEEN '".dftoda(weektoday($r_semaine,$r_annee,1))."' AND '".dftoda(weektoday($r_semaine2,$r_annee2,7))."'";}

$_SESSION['of_tps_piece_lire_req']=$of_tps_piece_lire_req;

if($_SESSION['of_tps_piece_lire_trier']=='') {$_SESSION['of_tps_piece_lire_trier']=$of_tps_piece_lire_req2;}
if(isset($trier)) {$_SESSION['of_tps_piece_lire_trier']=$trier;}

if($_SESSION['of_tps_piece_lire_ordre']=='') {$_SESSION['of_tps_piece_lire_ordre']='ASC ';}
if(isset($ordre)){$_SESSION['of_tps_piece_lire_ordre']=$ordre;}

$tf= new tri;
$tf->tri_ec=$_SESSION['of_tps_piece_lire_trier'];
$tf->ordre_ec=$_SESSION['of_tps_piece_lire_ordre'];
$tf->page_ec=$_SESSION['en_cour'];

if($print>0)
	{
	echo '<HTML><HEAD>'.$j_meta.$j_style.'</HEAD><BODY class="vide"><CENTER><h2>Temps moyen par pièce<BR>Date Edition : '.date('d/m/Y').'</h2></CENTER>';
	$j_even_dispo_color[0]='FFFFFF';
	}
else
	{
	$page=new page;
	$page->head("Temps moyen par pièce");
	$page->body();
	$page->entete("Temps moyen par pièce");
	$page->add_button(1,0);
	$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
	$page->add_button(3,0);
	$page->add_button(0,2);
	$page->add_button(27,1,"of_tps_piece_lire.php?print=1","Imprimer");
	$page->fin_entete();
	$page->datescript();
	?>
	<FORM style="position:relative;z-index:1;" method="post" name="f1" action="of_tps_piece_lire.php?mode=1" target="principal">
		<TABLE class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
			<TR>
				<TD class="m3">
					Réference :
					<INPUT type="text" name="r_ref" size="20" maxlength="20" value="<?php echo $r_ref;?>" onchange="document.f1.submit();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input class=cel1 type=checkbox name="indice" title="Distinguer les indices de gamme" <? if ($indice == 1)echo 'checked';?> value=1/> Indice </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
					<?php
					echo liste_ms("SELECT id, nom FROM entite ORDER BY nom ASC ",$r_entite,"r_entite" ,"UAP Gamme")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_cat,g.nom FROM gamme_cat g,of WHERE of.id_cat=g.id ORDER BY g.nom",$r_cat,"r_cat","Catégrorie")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_famille,g.nom FROM gamme_famille g,of WHERE of.id_famille=g.id ORDER BY g.nom",$r_famille,"r_famille","Famille")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,nom FROM gamme_fab ORDER BY nom",$r_fab,"r_fab","Fabrication")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT of.id_avion,p.nom FROM piece_avion p,of WHERE of.id_avion=p.id ORDER BY p.nom",$r_avion,"r_avion","Avion")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,nom FROM gamme_machine ORDER BY nom",$r_machine,"r_machine","Machine")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo liste_ms("SELECT id,code FROM gt ORDER BY code",$r_code_gt,"r_code_gt","GT")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					?>
					<BR><INPUT type="radio" id="r_saisie1" name="r_saisie" value="2" <?php if($r_saisie==2) {echo "checked=true";}?>/>
					<SPAN onclick="return document.getElementById('r_saisie1').checked=true">
						&nbsp;&nbsp;Semaine :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<SELECT size="1" name="r_semaine">
							<?php
							for($i=1;$i<54;$i++)
								{
								if($i==$r_semaine) {$s=" selected ";} else {$s="";}
								echo '<OPTION value="'.$i.'" '.$s.'>'.$i.'</OPTION>'."\n";
								}
						echo '</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;
						<SELECT id="button2" name="r_annee" size="1" onclick="document.getElementById(\'r_saisie1\').checked=true">';
							for($i=date("Y");$i>2000;$i--) {if($r_annee==$i) {$s=' selected ';} else {$s='';} echo "\t<OPTION value=$i $s >$i</OPTION>\n";}
						echo '</SELECT>&nbsp;&nbsp;à&nbsp;&nbsp;
						<SELECT size="1" name="r_semaine2">';
							for($i=1;$i<54;$i++) {if($i==$r_semaine2) {$s=" selected ";} else {$s="";} echo '<OPTION value="'.$i.'" '.$s.'>'.$i.'</OPTION>'."\n";}
						echo '</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;
						<SELECT id="button2" name="r_annee2" size="1" onclick="document.getElementById(\'r_saisie1\').checked=true">';
							for($i=date("Y");$i>2000;$i--) {if($r_annee2==$i) {$s=' selected ';} else {$s='';} echo "\t<OPTION value=$i $s >$i</OPTION>\n";}
							?>
						</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</SPAN>
					<INPUT type="radio" id="r_saisie2" name="r_saisie" value="1" <?php if($r_saisie==1) {echo "checked=true";}?>/>
					<SPAN onclick="return document.getElementById('r_saisie2').checked=true">
						&nbsp;&nbsp;Date :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<INPUT onclick="return showCalendar('sel1','%d/%m/%Y');" id="sel1" class="button2" size="11" type="text"  name="r_date" value="<?php echo $r_date;?>"/>
						<?php echo calendar('sel1');?>&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
						<INPUT onclick="return showCalendar('sel2','%d/%m/%Y');" id="sel2" class="button2" size="11" type="text"  name="r_date2" value="<?php echo $r_date2;?>"/>
						<?php echo calendar('sel2');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</SPAN><BR>
				</TD>
				<TD class="m3">
					<INPUT type="submit" id="button3" value="Go"/>&nbsp;&nbsp;
					<INPUT id="button3" type="button" value="Clear" onclick="document.location.href='of_tps_piece_lire.php?mode=9';"/>
				</TD>
			</TR>
		</TABLE>
	</FORM>
	<?php
	
	}

if($r_saisie==1 && $r_date==""){echo "<BR>Veuillez choisir un intervalle de semaine à semaine ou de date à date<BR>";exit;}
echo '<TABLE class="forumline" width="100%" align="center" cellSpacing="1" cellPadding="2" border="0">
	<TR class="m3">
		<TD width="20%">'.$tf->aff($of_tps_piece_lire_req2,"Réference",1).'</TD>
		<TD width=3%>'.$tf->aff("g.indice","Indice",1).'</TD>
		<TD width=20%>'.$tf->aff("of.designation","Désignation",1).'</TD>
		<TD width=12%>'.$tf->aff("of.id_famille","Famille",1).'</TD>
		<TD width=6%>'.$tf->aff("of.id_cat","Catégorie",1).'</TD>
		<TD width=8%>'.$tf->aff("of.id_avion","Avion",1).'</TD>
		<TD width=2%>'.$tf->aff("pi.numero","Phase",1).'</TD>
		<TD width=4%>'.$tf->aff("of.id_entite","UAP",1).'</TD>
		<TD width=4%>'.$tf->aff("pi.gt_code","Code GT",1).'</TD>
		<TD width=4%>'.$tf->aff("pi.section","Section",1).'</TD>
		<TD width=5%>'.$tf->aff("gm.nom","Ressource",1).'</TD>
		<TD width=4%>'.$tf->aff("tpm","Tps passé moyen",1).'</TD>
		<TD width=4%>'.$tf->aff("pi.tps_devis","Tps devis",1).'</TD>
		<TD width=4%>'.$tf->aff("coef_devis","Coef. Devis",1).'</TD>
		<TD width=4%>'.$tf->aff("pi.tps_obj","Objectif",1).'</TD>
		<TD width=4%>'.$tf->aff("coef_obj","Coef. Objectif",1).'</TD>
	</TR>';
$req="SELECT g.id, g.indice, g.ref, g.id_piece,of.id_piece as of_id_piece, of.id_gamme as of_id_gamme, g.designation, of.id_famille, of.id_cat, of.id_avion,pi.numero, of.id_entite,pi.section, pi.gt_code,gm.nom as ressource,
	AVG(pi.tps_reel/pi.etat_ok) as tpm, 
	AVG(pi.tps_devis/pi.quantite)as tps_devis,
	AVG(pi.tps_obj/pi.quantite)as tps_obj,
	IF ( AVG(pi.tps_reel/pi.etat_ok) > 0, (AVG(pi.tps_devis/pi.quantite) /  AVG(pi.tps_reel / pi.etat_ok)) , 0)  as coef_devis, 
	IF ( AVG(pi.tps_reel/pi.etat_ok) > 0, (AVG(pi.tps_obj/pi.quantite)   /  AVG(pi.tps_reel / pi.etat_ok)) , 0)  as coef_obj
	FROM phase_i pi
	LEFT JOIN of ON of.id=pi.id_of
	LEFT JOIN gamme g ON of.id_gamme=g.id
	LEFT JOIN gamme_machine gm ON gm.id=pi.id_machine
	WHERE of.id_affaire_type=1 AND pi.id_gamme>0 and pi.clos = 1 $of_tps_piece_lire_req
	GROUP BY $of_tps_piece_lire_req2, pi.numero 
	ORDER BY of.ref,  ".$_SESSION['of_tps_piece_lire_trier']." ".$_SESSION['of_tps_piece_lire_ordre']." ,pi.numero ";


$res=my_query($req);
$nb_ligne=mysql_num_rows($res);
if($nb_ligne==0) {echo "<BR>Aucun enregistrement trouvé<BR>";exit;}
$ic=0;
while($ligne=mysql_fetch_array($res))
	{
	if($idgec<>$ligne[$of_tps_piece_lire_req2]){$idgec=$ligne[$of_tps_piece_lire_req2];$ic++;}
	if($ic%2 == 0){$class="cel2";} else {$class="cel3";}
	echo '<TR class="'.$class.'" align="center">
		<TD>'.$ligne['ref'].'</TD>
		<TD>'.($indice?$ligne['indice']:'').'</TD>
		<TD>'.$ligne['designation'].'</TD>
		<TD>'.$j_gamme_famille[$ligne['id_famille']].'</TD>
		<TD>'.$j_gamme_cat[$ligne['id_cat']].'</TD>
		<TD>'.$j_piece_avion[$ligne['id_avion']].'</TD>
		<TD>'.$ligne['numero'].'</TD>
		<TD>'.$j_entite[$ligne['id_entite']].'</TD>
		<TD>'.$ligne['gt_code'].'</TD>
		<TD>'.$ligne['section'].'</TD>
		<TD>'.$ligne['ressource'].'</TD>
		<TD>'.nformat($ligne['tpm'],' ',1,2).'</TD>
		<TD>'.nformat($ligne['tps_devis'],' ',1,2).'</TD>
		<TD class="m3">'.nformat($ligne['coef_devis'],' ',1,2).'</TD>
		<TD>'.nformat($ligne['tps_obj'],' ',1,2).'</TD>
		<TD class="m3">'.nformat($ligne['coef_obj'],' ',1,2).'</TD>
	</TR>';
	}
echo '</TABLE>';

echo pied_page();
?>
