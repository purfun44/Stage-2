<?php
include("fonction.php");
if (!d_ok(632)){header("location: accueil.php");exit;}

$_SESSION['en_cour']="pointage_synthese.php";

$j_interne = dbtodata("SELECT id , concat(nom,' ',prenom) FROM interne");

if ($mode == "9")
	{
	$r_mat="";
	$r_nom="";
	$r_date="";
	$r_date2="";
	$mode=1;
	}

if (isset($r_tout))$r_tout=1;

if ($mode == "1")
	{
	$_SESSION['pointage_synthese_mat']=$r_mat;
	$_SESSION['pointage_synthese_nom']=$r_nom;
	$_SESSION['pointage_synthese_date']=$r_date;
	$_SESSION['pointage_synthese_date2']=$r_date2;
	$_SESSION['pointage_synthese_tout']=$r_tout;
	$p_en=1;
	}

if($p_en > 0)$_SESSION["pointage_synthese_p_en"]=$p_en;
if($mode > 0){$p_en=1;}else{$p_en=$_SESSION["pointage_synthese_p_en"];}
if(!($p_en > 0))$p_en=1;
$_SESSION["pointage_synthese_p_en"]=$p_en;
$pointage_synthese_req="";

$r_mat=$_SESSION['pointage_synthese_mat'];
$r_nom=$_SESSION['pointage_synthese_nom'];
$r_date=$_SESSION['pointage_synthese_date'];
$r_date2=$_SESSION['pointage_synthese_date2'];
$r_tout= $_SESSION['pointage_synthese_tout'];

if ($r_mat > 0)$pointage_synthese_req .= " and p.mat in ('.$r_mat.') ";
if ($r_nom <> "")$pointage_synthese_req .= " and i.nom like ('.$r_nom.') ";


if((isdf($r_date))and(isdf($r_date2))){$pointage_synthese_req .= " and h.date between '".dftoda($r_date)."' and '".dftoda($r_date2)."' ";}
else if(isdf($r_date)){$pointage_synthese_req .= " and h.date = '".dftoda($r_date)."' ";}


$page = new page;
$page->head("Synthèse des pointages");
$page->body();
$page->entete("Synthèse des pointages");
$page->add_button(1,0);
$page->add_button(2,1,parent(632));
$page->add_button(3,0);
$page->add_button(0,2);
$page->fin_entete();
$page->datescript();
?>

<FORM method="post" name="formulaire1" action="pointage_synthese.php"  target="principal">
	<INPUT type="hidden" name="mode" value="1"/>
	<TABLE class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
		<TR class="m3">
			<TD height="30">
				Matricule : <INPUT size="15" id="r_mat" type="text" name="r_mat" value="<?php echo $r_mat; ?>"/>&nbsp;&nbsp;
				Nom : <INPUT size="25" id="r_nom" type="text" name="r_nom" value="<?php echo $r_nom; ?>"/>&nbsp;&nbsp;
				&nbsp;&nbsp;Date&nbsp;&nbsp;
				<INPUT onclick="return showCalendar('sel3','%d/%m/%Y');" id="sel3" class="button2" size="11" type="text" name="r_date" value="<?php echo $r_date; ?>"/>
				<?php echo calendar('sel3');?>
				&nbsp; &nbsp;à&nbsp; &nbsp;
				<INPUT onclick="return showCalendar('sel4','%d/%m/%Y');" id="sel4" class="button2" size="11" type="text" name="r_date2" value="<?php echo $r_date2; ?>"/>
				<?php echo calendar('sel4');?>&nbsp; &nbsp;
				<INPUT type="checkbox" name="r_tout"<?php if ($r_tout == 1)echo 'checked';?>/>
				Sur 1 page
			</TD>
			<TD rowspan="2">
				<INPUT type="submit" id="button3" value="Filtrer"/>
				<INPUT id="button3" type="button" value="Clear" onclick="document.location.href='pointage_synthese.php?mode=9';"/>
			</TD>
		</TR>
	</TABLE>
</FORM>

<?php
if ($pointage_synthese_req <> '')
	{
	if($r_date <> '' && $r_date2 <> '')
		{
		if($_SESSION['pointage_synthese_trier']==''){$_SESSION['pointage_synthese_trier']='h.date ';}
		if(isset($trier)){$_SESSION['pointage_synthese_trier']=$trier;}
		
		if($_SESSION['pointage_synthese_ordre']==''){$_SESSION['pointage_synthese_ordre']='desc ';}
		if(isset($ordre)){$_SESSION['pointage_synthese_ordre']=$ordre;}
		
		$tf=new tri;
		$tf->tri_ec=$_SESSION['pointage_synthese_trier'];
		$tf->ordre_ec=$_SESSION['pointage_synthese_ordre'];
		$tf->page_ec=$_SESSION['en_cour'];

		?>
		
		<TABLE class="forumline" cellSpacing="1" cellPadding="2" width="100%" align="center" border="0">
			<TR class= "m3">
				<TD width=12% ><?php $tf->aff("h.date","Date");?></TD>
				<TD width=8% ><?php $tf->aff("p.mat","Matricule");?></TD>
				<TD width=10% ><?php $tf->aff("i.nom","Nom");?></TD>
				<TD width=10% ><?php $tf->aff("i.prenom","Prenom");?></TD>
				<TD width=12% ><?php $tf->aff("p.h_t","Pointage");?></TD>
				<TD width=12% ><?php $tf->aff("h.horo_reel","Temps réel Horoquartz");?></TD>
				<TD width=12% ><?php $tf->aff("h.horo_valide","Temps validé Horoquartz");?></TD>
				<TD width=12% ><?php $tf->aff("time_to_sec(h.horo_reel)- sum(time_to_sec(p.h_t))","Différence réelle");?></TD>
				<TD width=12% ><?php $tf->aff("time_to_sec(h.horo_valide)-sum(time_to_sec(p.h_t))","Différence Validée");?></TD>
			</TR>
			<?php
			$req="SELECT p.date, p.mat, i.nom, i.prenom, sec_to_time(sum(time_to_sec(p.h_t)))as h_t, h.horo_reel, h.horo_valide, sec_to_time(time_to_sec(h.horo_reel)- sum(time_to_sec(p.h_t))) as diff_reel, sec_to_time(time_to_sec(h.horo_valide)-sum(time_to_sec(p.h_t))) as diff_valide
			FROM pointage p
			LEFT JOIN interne i  ON p.id_interne = i.id 
			LEFT JOIN horoquartz h ON h.badge_movex = i.badge_movex and h.date = p.date
			WHERE 1 ".$pointage_synthese_req." and h.id is not null
			GROUP BY  p.mat , p.date , h.date 
			ORDER BY ".$_SESSION['pointage_synthese_trier']." ".$_SESSION['pointage_synthese_ordre']." , p.mat ASC , h.date DESC
			";
			//echo $req;
			$res=my_query($req);
			$ligne_page=$lpp -1;	//ligne par page
			$p_pf=20;		//page par feuille
			$nb_ligne=mysql_num_rows($res);
			if ($nb_ligne==0) {echo "<BR>Aucun enregistrement trouvé<BR>";exit;}
			$prem_ligne=(($p_en - 1) * $ligne_page);
			mysql_data_seek($res,0);
			mysql_data_seek($res,$prem_ligne);
			while ($ligne=mysql_fetch_array($res))
				{
				echo '<TR class="cel2" align="center">
					<TD>'.datodf($ligne['date']).'</TD>
					<TD>'.$ligne['mat'].'</TD>
					<TD>'.$ligne['nom'].'</TD>
					<TD>'.$ligne['prenom'].'</TD>
					<TD>'.$ligne['h_t'].'</TD>
					<TD>'.$ligne['horo_reel'].'</TD>
					<TD>'.$ligne['horo_valide'].'</TD>
					<TD>'.$ligne['diff_reel'].'</TD>
					<TD>'.$ligne['diff_valide'].'</TD>
				</TR>';
				if (($r_tout == 0 )and($ic == $ligne_page)){break;}
				}
		echo '</TABLE>';
		if ($r_tout == 0 )echo bar("pointage_synthese.php","",$p_en,$nb_ligne,$ligne_page);
		}
	else echo "Il faut obligatoirement saisir un intervalle de dates.";
	}
echo pied_page();
?>
