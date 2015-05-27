<?
include("fonction.php");
if (!d_ok(101)){header("location: accueil.php");exit;}
$_SESSION['en_cour']="piece_consulter.php";

$j_avion = dbtodata("select id , nom from piece_avion; ");
$j_famille = dbtodata("select id , nom from gamme_famille; ");
$j_cat = dbtodata("select id , nom from gamme_cat; ");

if(!isset($_SESSION['piece_consulter_actif']))$_SESSION['piece_consulter_actif']=1;

if ($mode == "9")
	{
	$r_id="";
	$r_type="0";
	$r_ref="";
	$r_ind="";
	$r_des="";
	$r_id_fab="";
	$r_id_affaire_type="";
	$r_id_famille="";
	$r_id_cat="";
	$r_id_avion="";
	$r_actif="1";
	$r_aff="";
	$mode="1";
	}
if (isset($r_tout))$r_tout=1;

if ($mode == "1")
	{
	$_SESSION['piece_consulter_id']=$r_id;
	$_SESSION['piece_consulter_type']=$r_type;
	$_SESSION['piece_consulter_ref']=$r_ref;
	$_SESSION['piece_consulter_ind']=$r_ind;
	$_SESSION['piece_consulter_des']=$r_des;
	$_SESSION['piece_consulter_fab']=$r_id_fab;
	$_SESSION['piece_consulter_affaire_type']=$r_id_affaire_type;
	$_SESSION['piece_consulter_famille']=$r_id_famille;
	$_SESSION['piece_consulter_cat']=$r_id_cat;
	$_SESSION['piece_consulter_avion']=$r_id_avion;
	$_SESSION['piece_consulter_actif']=$r_actif;
	$_SESSION['piece_consulter_aff']=$r_aff;
	$_SESSION['piece_consulter_tout'] = $r_tout;
	$p_en=1;
	}

if($p_en > 0)$_SESSION["piece_consulter_p_en"]=$p_en;
if($mode > 0){$p_en=1;}else{$p_en=$_SESSION["piece_consulter_p_en"];}
if(!($p_en>0))$p_en=1;
$_SESSION["piece_consulter_p_en"]=$p_en;

$piece_consulter_req="";

$r_id=$_SESSION['piece_consulter_id'];
$r_type=$_SESSION['piece_consulter_type'];
$r_ref=$_SESSION['piece_consulter_ref'];
$r_ind=$_SESSION['piece_consulter_ind'];
$r_des=$_SESSION['piece_consulter_des'];
$r_id_fab=$_SESSION['piece_consulter_fab'];
$r_id_affaire_type=$_SESSION['piece_consulter_affaire_type'];
$r_id_famille=$_SESSION['piece_consulter_famille'];
$r_id_cat=$_SESSION['piece_consulter_cat'];
$r_id_avion=$_SESSION['piece_consulter_avion'];
$r_actif=$_SESSION['piece_consulter_actif'];
$r_aff=$_SESSION['piece_consulter_aff'];
$r_tout= $_SESSION['piece_consulter_tout'];

if ($r_id > 0)$piece_consulter_req .= " and p.id='$r_id' ";
if (is_array($r_type))$piece_consulter_req .= " and p.id_affaire_type in (".tabtosql($r_type).") ";
if ($r_ref <> "")$piece_consulter_req .= " and p.ref like '%$r_ref%' ";
if ($r_ind <> "")$piece_consulter_req .= " and p.indice like '$r_ind' ";
if ($r_des <> "")$piece_consulter_req .= " and p.designation like '%$r_des%' ";
if (is_array($r_id_fab))$piece_consulter_req .= " and p.id_fab in (".tabtosql($r_id_fab).") ";
if ($r_id_affaire_type>0)$piece_consulter_req .= " and p.id_affaire_type = ".$r_id_affaire_type." ";
if ($r_id_famille > 0)$piece_consulter_req .= " and p.id_famille = '".$r_id_famille."' ";
if ($r_id_cat > 0)$piece_consulter_req .= " and p.id_cat = '".$r_id_cat."' ";
if (is_array($r_id_avion))$piece_consulter_req .= " and p.id_avion in (".tabtosql($r_id_avion).") ";
if ($r_actif > -1)$piece_consulter_req .= " and p.actif = '".$r_actif."' ";
if ($r_aff > 0)$piece_consulter_req .= " and p.id_affaire = '".$r_aff."' ";

$_SESSION['piece_consulter_req']=$piece_consulter_req;

if($_SESSION['piece_consulter_trier']==''){$_SESSION['piece_consulter_trier']='p.ref ';}
if(isset($trier)){$_SESSION['piece_consulter_trier']=$trier;}

if($_SESSION['piece_consulter_ordre']==''){$_SESSION['piece_consulter_ordre']='asc ';}
if(isset($ordre)){$_SESSION['piece_consulter_ordre']=$ordre;}

$tf=new tri;
$tf->tri_ec=$_SESSION['piece_consulter_trier'];
$tf->ordre_ec=$_SESSION['piece_consulter_ordre'];
$tf->page_ec=$_SESSION['en_cour'];

$page = new page;
$page->head("Gestion des références");
$page->body();
$page->entete("Gestion des références");
$page->add_button(1,0);
$page->add_button(2,1,parent(101));
$page->add_button(3,0);
$page->add_button(0,2);
if(d_ok(102))$page->add_button(5,1,"piece_ajouter.php?new=1","Ajouter une pièce");
$page->fin_entete();
$page->datescript();
?>
<script LANGUAGE = "JavaScript">
	menu = new Array()
	<?
	$corr =  "menu_corr = new Array()\n\n";
	$i=0;
	$corr .= 'menu_corr[0] = '.$i.";\n";

	echo "menu[$i] = new Array()\n\n";
	$j=0;
	echo 'menu['.$i.']['.$j.']=new Option("","")'."\n";
	$j++;
	$res2 = my_query("SELECT id , nom FROM gamme_cat order by nom ");
	while ($row2 = mysql_fetch_array($res2))
		{
		if($row2[0] == $r_id_famille) {$select_cat = '<script LANGUAGE = "JavaScript" > changecat(); document.formulaire.r_id_famille.options['.$j.'].selected = true;</script>';}
		echo 'menu['.$i.']['.$j.'] = new Option("'.$row2[1].'","'.$row2[0].'")'."\n";
		$j++;
		}
	echo "\n\n\n";
	$s .= '<OPTION '.$selected.' VALUE = ""></OPTION>'."\n";
	$i++;
	$res = my_query("SELECT id , nom FROM gamme_cat order by nom");
	while($row = mysql_fetch_array($res))
		{
		$corr .= 'menu_corr['.$row[0].'] = '.$i.";\n";
		if($r_id_cat == $row[0]) {$selected = ' selected ';} else {$selected = '';}
		$s .= '<OPTION '.$selected.' VALUE="'.$row[0].'">'.$row[1].'</OPTION>'."\n";
		echo "menu[$i] = new Array()\n\n";
		$j = 0;
		echo 'menu['.$i.']['.$j.'] = new Option("","")'."\n";
		$j++;
		$res2 = my_query("SELECT id, nom FROM gamme_famille WHERE id_cat = '".$row[0]."' order by nom ");
		while ($row2 = mysql_fetch_array($res2))
			{
			if($row2[0] == $r_id_famille) {$select_cat = '<script LANGUAGE="JavaScript">changecat();document.formulaire1.r_id_famille.options['.$j.'].selected = true;</script>';}
			echo 'menu['.$i.']['.$j.'] = new Option("'.$row2[1].'","'.$row2[0].'")'."\n";
			$j++;
			}
		echo "\n\n\n";
		$i++;
		}
	echo $corr;
	?>
	function changecat()
		{
		numeroMenu = menu_corr[document.formulaire1.r_id_cat.options[document.formulaire1.r_id_cat.selectedIndex].value];
		f = document.formulaire1.r_id_famille;
		for(i=f.options.length-1; i>0; i--)
			{
			f.options[i] = null
			}
		for (i=0; i<menu[numeroMenu].length; i++)
			{
			f.options[i] = new Option(menu[numeroMenu][i].text,menu[numeroMenu][i].value)
			}
		f.selectedIndex=0
		}
</script>
<form style="position:relative;z-index:1;" method="post" name="formulaire1" action="piece_consulter.php?mode=1"  target="principal">
	<table  class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
		<tr>
			<td class="m3">
				ID :
				<input type=text name="r_id" size=4 maxlength=5 value="<? echo $r_id;?>"  onchange="document.formulaire1.submit();" >&nbsp; &nbsp;
				<? echo liste_ms($j_affaire_type,$r_type,"r_type","Type"); ?>&nbsp; &nbsp;
				Ref :
				<input type=text name="r_ref" size=20 maxlength=20 value="<? echo $r_ref;?>"  onchange="document.formulaire1.submit();" >&nbsp; &nbsp;
				Designation :
				<input type=text name="r_des" size=10 maxlength=20 value="<? echo $r_des;?>"  onchange="document.formulaire1.submit();" >&nbsp; &nbsp;
				Affaire :
				<input type=text name="r_aff" size=6 maxlength=10 value="<? echo $r_aff;?>"  onchange="document.formulaire1.submit();" >&nbsp; &nbsp;
				Actif :
				<select id="button" name="r_actif" size="1" onchange="document.formulaire1.submit();" >
					<option value="-1" <? if ($r_actif==-1){echo selected;} ?>> </option>
					<option value="0" <? if ($r_actif==0){echo selected;} ?>> non </option>
					<option value="1" <? if ($r_actif==1){echo selected;} ?>> oui </option>
				</select><BR>
				<?php 
				echo liste_ms("SELECT id,nom FROM gamme_fab ORDER BY nom ASC",$r_id_fab,"r_id_fab","Fabrication")."&nbsp; &nbsp;&nbsp;
				Type d'affaire : ";
				echo liste_d2($j_affaire_type,$r_id_affaire_type,"r_id_affaire_type");?>&nbsp; &nbsp;&nbsp;
				Catégorie :
				<SELECT id=button  style="width:100;" NAME="r_id_cat"  SIZE=1 onChange="changecat()"><? echo $s;?></SELECT>&nbsp; &nbsp;
				Famille :
				<SELECT id=button style="width:100;" NAME="r_id_famille" onchange="document.formulaire1.submit();">
					<OPTION VALUE="0"></OPTION>
				</SELECT>
				<? echo $select_cat;?>&nbsp; &nbsp;
				<? echo liste_ms("select pa.id,pa.nom from piece_avion as pa order by pa.nom asc",$r_id_avion,"r_id_avion","Avion"); ?>&nbsp; &nbsp;&nbsp;
				<label><input class=m3 type=checkbox name="r_tout" <? if ($r_tout == 1)echo 'checked';?> > Sur 1 page</label>
			</td>
		</tr>
		<tr>
			<td class="m3">
				<input type="submit" id=button3 value="Filtrer" > &nbsp;
				<input id=button3 type=button value="Clear" onclick="document.location.href='piece_consulter.php?mode=9';">
			</td>
		</tr>
	</table>
</form><br>
<?if($r_id_famille==0){?>
<script LANGUAGE = "JavaScript">
	changecat();
</script>
<?}
function entete()
	{
	global $tf,$d_obj,$d_devis;
	?>
	<table class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
	<tr >
		<td class= "m3" width=2% ><? $tf->aff("p.id","id");?></td>
		<td class= "m3" width=20% height=23><? $tf->aff("ref","Référence");?></td>
		<td class= "m3" width=20% ><? $tf->aff("designation","Désignation");?></td>
		<td class= "m3" width=10% ><? $tf->aff("id_affaire_type","Type");?></td>
		<td class= "m3" width=10% ><? $tf->aff("id_cat","Cat.");?></td>
		<td class= "m3" width=10% ><? $tf->aff("id_famille","Famille");?></td>
		<td class= "m3" width=10% ><? $tf->aff("id_avion","Avion");?></td>
		<td class= "m3" width=10% ><? $tf->aff("id_affaire","Affaire");?></td>
		<td class= "m3" width=5% ><? $tf->aff("actif","valide");?></td>
	</tr>
	<?
	}
$req="select * from piece p where 1  $piece_consulter_req order by ".$_SESSION['piece_consulter_trier']." ".$_SESSION['piece_consulter_ordre']." , p.ref asc  ";
$ic=0;
$res=my_query($req);
$ligne_page=$lpp;	//ligne par page
$p_pf=20;		//page par feuille
$nb_ligne=mysql_num_rows($res);
if($nb_ligne==0){echo "<br>Aucun enregistrement trouvé<br>";exit;}
$prem_ligne=(($p_en - 1) * $ligne_page);
mysql_data_seek($res,0);
mysql_data_seek($res,$prem_ligne);
entete();
while ($ligne=mysql_fetch_array($res))
	{
	if(($ic % 2)==0){$cid="class= \"cel2\"";}else {$cid="class= \"cel1\"";}
	if($ligne["valid"]){$alt='Active';}else{$alt='Inactive';}
	?>
	<tr>
	<td align=center <? echo $cid;?>  ><? echo $ligne["id"];?></td>
	<td align=center <? echo $cid;?>  ><a class="b" href="piece_ajouter.php?util=<? echo $ligne["id"];?>"><? echo $ligne["ref"];?></a></td>
	<td align=center <? echo $cid;?>  ><? echo $ligne["designation"];?></td>
	<td align=center <? echo $cid;?>  ><? echo $j_affaire_type[$ligne["id_affaire_type"]];?></td>
	<td align=center <? echo $cid;?>  ><? echo $j_cat[$ligne["id_cat"]];?></td>
	<td align=center <? echo $cid;?>  ><? echo $j_famille[$ligne["id_famille"]];?></td>
	<td align=center <? echo $cid;?>  ><? echo $j_avion[$ligne["id_avion"]];?></td>
	<td align=center <? echo $cid;?>  ><? echo $ligne["id_affaire"];?></td>
	<td align=center <? echo $cid;?>  ><img border=0 alt="<? echo $alt;?>" src="images/statut<? echo $ligne["actif"];?>.gif"></td>
	</tr>
	<?
	$ic++;
	if(($r_tout == 0 )and($ic == $ligne_page)){break;}
	}
echo "</table>";
if ($r_tout == 0 )echo bar("piece_consulter.php",'',$p_en,$nb_ligne,$ligne_page);
echo pied_page();
?>
