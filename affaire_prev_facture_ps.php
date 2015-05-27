<?
include("fonction.php");
include("fonction_affaire.php");
if (!d_ok(960)){header("location: accueil.php");exit;}

$_SESSION['en_cour']="affaire_prev_facture_ps.php";

if($parent_id > 0){$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori == 1)or($_SESSION[$_SESSION['en_cour']]=="")){$_SESSION[$_SESSION['en_cour']]=parent(960);}


if ($mode == "9")
{
$r_date="";
$r_date2="";
$r_po="";
$r_cde="";
$r_ref="";
$r_aff="";
$fac_only=1;
$mode="1";
}

if (isset($fac_only))$fac_only=1;
if (!isset($_SESSION['affaire_prev_facture_ps_fac_only']))$_SESSION['affaire_prev_facture_ps_fac_only']=1;

if ($mode == "1")
{
$_SESSION['affaire_prev_facture_ps_date'] = $r_date;
$_SESSION['affaire_prev_facture_ps_date2'] = $r_date2;
$_SESSION['affaire_prev_facture_ps_po']  = $r_po;
$_SESSION['affaire_prev_facture_ps_cde']  = $r_cde;
$_SESSION['affaire_prev_facture_ps_ref']  = $r_ref;
$_SESSION['affaire_prev_facture_ps_aff']  = $r_aff;
$_SESSION['affaire_prev_facture_ps_fac_only']  = $fac_only;

$p_en=1;
}


$affaire_prev_facture_ps_req="";

$r_date   = $_SESSION['affaire_prev_facture_ps_date'];
$r_date2   = $_SESSION['affaire_prev_facture_ps_date2'];
$r_po    = $_SESSION['affaire_prev_facture_ps_po'];
$r_cde    = $_SESSION['affaire_prev_facture_ps_cde'];
$r_ref    = $_SESSION['affaire_prev_facture_ps_ref'];
$r_aff    = $_SESSION['affaire_prev_facture_ps_aff'];
$fac_only    = $_SESSION['affaire_prev_facture_ps_fac_only'];

if ($r_po <> '')$affaire_prev_facture_ps_req .= " and ap.poste = '$r_po' ";
if ($r_cde <> '')$affaire_prev_facture_ps_req .= " and (ap.n_cde_c) like '$r_cde' ";
if ($r_ref <> '')$affaire_prev_facture_ps_req .= " and ap.observation like '%$r_ref%' ";
if ($r_aff <> '')$affaire_prev_facture_ps_req .= " and ap.id_affaire = '$r_aff' ";
if ($fac_only==1)$affaire_prev_facture_ps_req .= " and d_facture > 0 and mt > 0 and poste > 0 and id_facture = 0";

if((isdf($r_date))and(isdf($r_date2))){$affaire_prev_facture_ps_req .= " and (( ap.d_cde >= '".dftoda($r_date)."')and ( ap.d_cde <= '".dftoda($r_date2)."')) ";}
else if(isdf($r_date)){$affaire_prev_facture_ps_req .= " and ap.d_cde = '".dftoda($r_date)."' ";}



//créer une facture en fonction de la facture prévisionnel sélectionné


if ($save == 1)
	{
	$j=count($id)+1;
	for ($i=1;$i < $j;$i++)
		if(($id[$i] > 0)or($d_cde[$i]<>''))
		{
		$p_u[$i]=clean_nb($p_u[$i]);
		$qte[$i]=clean_nb($qte[$i]);
		
		$vf = new valid_form ;
		
		if((($observation[$i]<>$observation_old[$i])or($observation[$i]==""))and($of_client[$i]<>""))
			{
			$res = my_query("select id_affaire, ref from of where numero_client = ".$of_client[$i]." or of_client = ".$of_client[$i]." limit 1");
			if(mysql_num_rows($res))
				{
				$row = mysql_fetch_array ($res);
				$observation[$i]=$row["ref"];
				$id_affaire[$i] = nombre_de("select id_affaire from piece where ref like '".$row["ref"]."'");
				}

//echo $sql;
			}
			else if((($id_affaire[$i]==0 )and($observation[$i]<>""))or($observation[$i]<>$observation_old[$i]))
				{
				$res = my_query("select id_affaire, id_cat from piece where ref like '".$observation[$i]."' AND id_affaire > 0  limit 1");
				if(mysql_num_rows($res) > 0)
					{
					$row = mysql_fetch_array ($res);
					$id_affaire[$i] = $row["id_affaire"];
					}


					
			}
		$vf->add("id_affaire", $id_affaire[$i] );
		$vf->add("d_cde", dftoda($d_cde[$i]));
		$vf->add("n_cde_c", $n_cde_c[$i]);
		$vf->add("des", 'POSTE '.format_0($poste[$i],4));
		$vf->add("poste", $poste[$i]);
		$vf->add("qte", $qte[$i]);
		$vf->add("p_u", $p_u[$i]);
		$vf->add("mt", $qte[$i]*$p_u[$i]);
		$vf->add("commentaire", $commentaire[$i]);
		$vf->add("observation", $observation[$i]);
		$vf->add("d_facture", dftoda($d_facture[$i]));
		$vf->add("n_facture", $n_facture[$i]);
		$vf->add("prev_ps", 1);
		
		if($id[$i] > 0)
			{
			if(($p_u[$i])<>0)
				{
				$vf->update("affaire_prev"," where id = '".$id[$i]."'");
				$vf->log(__FILE__,__LINE__,DL_1);
				}
				else if (!($id_facture[$i]>0))
				{
				del_visit(__FILE__,__LINE__,DL_1,"affaire_prev","where id = '".$id[$i]."'");
				}
			}
			else if(($qte[$i]*$p_u[$i])<>0)
			{
			$vf->insert("affaire_prev");
			$vf->log(__FILE__,__LINE__,DL_1);
			}
		//$id_affaire[$i]=1;
		}

	
	foreach($id_affaire as $k => $v)if($k > 0)
		{
		$vf = new valid_form ;
		$vf->add("mt_facture_prev", nombre_de("select sum(mt) from affaire_prev where id_affaire = ".$k." group by id_affaire")+0);
		$vf->update("affaire","where id = ".$k,"",1);
		$vf->log(__FILE__,__LINE__,DL_3);
	
		if(nombre_de('select avenant_auto from affaire where id = '.$k)==1)maj_mt_marche($k);
		}
	}

function une_ligne($i,$id=0,$d_cde="",$id_affaire="",$n_cde_c="",$poste="",$observation="",$qte="",$p_u="",$mt="",$commentaire="",$d_facture="",$n_facture="",$id_facture="",$type_fac="",$d_paiement="",$mt_fac="",&$t_diff="",&$nb_coche=0,&$rowspan="",$tot_prev="",&$t_mt_fac=0,$id_pere=0, $of_client="")
{
global $j_facture_type,$id_util;
if(($id_util==1)and($id>0))$md='ondblclick="upd_id.form(this,'.$id.');"';
$s1 = ' class="cel1" '; $s2 = ' class="cel2" ';

if($id_facture>0) $readonly = ' readonly ';else $readonly = '';

echo '<tr align=center>
<td '.$s1.'>';

if(($d_cde != '' && $d_cde != '0000-00-00') || ($d_facture != '' && $d_facture != '0000-00-00') || $n_cde_c != '' || $poste > 0 && $p_u != '' && $qte != '' && $mt != '')
	echo '<img src="images/redo.gif" border="0" onclick="dupliquer(\''.($i-1).'\');">';
	
if($d_cde == '' || $d_cde == '0000-00-00' && $d_facture == '' || $d_facture == '0000-00-00' && $n_cde_c == '' && $poste == 0 && $observation == '' && $qte > 0 && $p_u <> 0 && $mt <> 0)
	$vide = 0;
	else $vide = 1;
	
echo '</td>
<td '.$s2.'><input type=hidden name="nbligne['.$i.']" id="nbligne" value="'.$vide.'"><input type=hidden name="id_facture['.$i.']" id="id_facture" value="'.$id_facture.'"><input type=hidden name="id['.$i.']" id="id" value="'.$id.'"><input type=hidden name="of_client['.$i.']" id="of_client" value="'.$of_client.'"><input  style="border:0;text-align:center;" '.$s2.' type="text" name="d_cde['.$i.']" id="d_cde" value="'.datodf($d_cde).'" size="12" ></td>
<td '.$s2.'><input style="border:0;text-align:center;" '.$readonly.' '.$s2.' type="text" title="'.$id.'" name="d_facture['.$i.']" id="d_facture" value="'.datodf($d_facture).'" size="12" ></td>
<td '.$s2.'><input type="hidden" name="id_affaire['.$i.']" value="'.$id_affaire.'">'.$id_affaire.'</td>
<td '.$s2.'><input style="border:0;text-align:center;" '.$readonly.' '.$s2.' type="text" maxlength="15" name="n_cde_c['.$i.']" id="n_cde_c"  value="'.$n_cde_c.'" size="10"></td>
<td '.$s2.'><input style="border:0;text-align:center;" '.$readonly.' '.$s2.' type="text" maxlength="5" name="poste['.$i.']" id="poste"  value="'.$poste.'" size="5" ></td>
<td '.$s2.'><input style="border:0;text-align:center;" '.$readonly.' '.$s2.' type="text" maxlength="5" name="of_client['.$i.']" id="of_client"  value="'.$of_client.'" size="5" ></td>
<td '.$s1.'><input style="border:0;text-align:center;" '.$readonly.' '.$s1.' type="text" maxlength="50" name="observation['.$i.']" value="'.$observation.'" size="20" ><input type="hidden" name="observation_old['.$i.']" value="'.$observation.'"  ></td>
<td '.$s2.'><input style="border:0;text-align:center;"  '.$s2.' type="text" maxlength="5" name="qte['.$i.']" value="'.$qte.'" size="5" ></td>
<td '.$s2.'><input style="border:0;text-align:center;"  '.$s2.' type="text" maxlength="12" name="p_u['.$i.']" value="'.nformat($p_u,'',1).'" size="8" ></td>
<td '.$s1.'>'.nformat($mt,'',1).'</td>';
if($id_facture > 0)
	{
	if($rowspan[$id_facture] > 0)
		{
		if($type_fac==2)$mt_fac=$mt_fac*-1;
		if($id_pere>0)$mt_fac=total_facture($id_pere,$mt_fac);
		$t_mt_fac += $mt_fac;

		echo '<td '.$s1.' rowspan="'.$rowspan[$id_facture].'">'.nformat($mt_fac,'',1).'</td>';
		
		echo '<td '.$s1.' rowspan="'.$rowspan[$id_facture].'">';
 		if($mt_fac <> '' && $mt_fac <> 0 && $tot_prev[$id_facture] <> '' && ($mt_fac-$tot_prev[$id_facture]) <> 0)
	 		{
			echo nformat($mt_fac-$tot_prev[$id_facture],'',2);
			$t_diff += $mt_fac-$tot_prev[$id_facture];
 			}
		echo '</td>';
		}
	}
	else
	{
	echo '<td '.$s1.'></td><td '.$s1.'></td>';
	}
	
echo '<td '.$s1.'>'.$commentaire.'</td>';
if($id_facture > 0)
	{
	if($rowspan[$id_facture] > 0)
		{
		echo '<td '.$s1.' rowspan="'.$rowspan[$id_facture].'" '.$md.'><a class="b" href="facture_ajouter.php?util='.$id_facture.'&parent='.$_SESSION['en_cour'].'">'.$n_facture.'</a></td>
			 <td '.$s1.' rowspan="'.$rowspan[$id_facture].'">'.$j_facture_type[$type_fac].'</td>
			 <td '.$s1.' rowspan="'.$rowspan[$id_facture].'">'.datodf($d_paiement).'</td>
			 <td '.$s1.' rowspan="'.$rowspan[$id_facture].'"></td>';
		}
	$rowspan[$id_facture] = 0;
	}
else
	{
	echo '<td '.$s1.' '.$md.'></td>
		 <td '.$s1.' ></td>
		 <td '.$s1.'></td>
		 <td '.$s1.'>';
	if(!empty($d_facture) && $d_facture != '0000-00-00' && !empty($mt) && !empty($poste) && d_ok(302) && $id_affaire>0)
		{
		$nb_coche++;
		echo '<input type="checkbox" id="fp_coche'.$nb_coche.'" name="fp_coche[]" value="'.$i.'">';
		}
	echo '</td>';
	}
	echo '</tr></tr>';
}

$page = new page;
$page->head("Carnet de commande");
$page->body();
$page->entete("Carnet de commande" );
$page->add_button(1,0);
$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
$page->add_button(3,0);
$page->add_button(0,2);
$page->add_button(4,1,"document.f2.submit();");
$page->add_button(0,2);
$page->add_button(0,2);
$page->add_button(16,1,"affaire_prev_facture_ps_import.php","Importer une fiche facture ");

$page->fin_entete();
$page->datescript();

?>
<script language="JavaScript" >
function creer_facture(nb)
	{

	coche = true;	
	if(!coche) alert("Vous n'avez coché aucun Carnet de commande à transformer en facture !");
	else
		{
		document.f2.save.value = "";
		document.f2.action = "facture_ajouter.php?parent=<? echo $_SESSION['en_cour']; ?>&transformer=1&type_aff=1";
		document.f2.submit();
		alert("Formulaire envoyé !");
		}
	}

function dupliquer(ligne)
	{
	var nbligne = 0;
	var i = 0;
	var arret = false;
	while(!arret && i<document.f2.nbligne.length)
		{
		if(document.f2.nbligne[i].value == 1) nbligne++;
		else arret = true;
		i++;
		}
	var d_cde = document.f2.d_cde[ligne].value;
	var d_facture = document.f2.d_facture[ligne].value;
	var n_cde_c = document.f2.n_cde_c[ligne].value;
	var poste = document.f2.poste[ligne].value;

	document.f2.d_cde[nbligne].value = d_cde;
	document.f2.d_facture[nbligne].value = d_facture;
	document.f2.n_cde_c[nbligne].value = n_cde_c;
	document.f2.poste[nbligne].value = poste;
	document.f2.nbligne[nbligne].value = 1;
	}
</script>

<script LANGUAGE="JavaScript" src="js/ajax.js"></script>
<script LANGUAGE="JavaScript" src="js/Update.js"></script>
<script LANGUAGE="JavaScript">
	var upd_id = new Update("upd_id","affaire_prev","id_facture","id")
	upd_id.setParam(" size=7 maxlength=7 ");
	

</script>


<form method="post" name="formulaire1" action="affaire_prev_facture_ps.php"  target="principal">
	<input type=hidden name="mode" value="1">
	<table  class=forumline cellSpacing=1 cellPadding=2 width="80%" align=center border=0>
		<TR>
			<TD class="m3" height="30" >
			N° CDE : <input size=15 id=button2 type=text name="r_cde" value="<? echo $r_cde; ?>" onchange="formulaire1.submit();">
			&nbsp; Ref : <input size=30 id=button2 type=text name="r_ref" value="<?echo $r_ref;?>" onchange="formulaire1.submit();">
			&nbsp; Affaire : <input size=10 id=button2 type=text name="r_aff" value="<?echo $r_aff;?>" onchange="formulaire1.submit();">
			&nbsp; Poste : <input size=10 id=button2 type=text name="r_po" value="<?echo $r_po;?>" onchange="formulaire1.submit();">
			&nbsp; Date CDE : <INPUT onclick="return showCalendar('sel3','%d/%m/%Y');"  id=sel3 class="button2" size=11 type="text"  name="r_date" value="<? echo $r_date; ?>"><? echo calendar('sel3');?>
			&nbsp; à &nbsp;<INPUT onclick="return showCalendar('sel4','%d/%m/%Y');"  id=sel4 class="button2" size=11 type="text"  name="r_date2" value="<? echo $r_date2; ?>"><? echo calendar('sel4');?>
			<br /><label>Facture pouvant être créée : <input type="checkbox" name="fac_only" id="fac_only" <? if($fac_only==1)echo 'checked';?> ></label>
			&nbsp; &nbsp;<input type="submit" id="button3" value="Go">&nbsp; |&nbsp; <input id=button3 type=button value="Clear" onclick="document.location.href='affaire_prev_facture_ps.php?mode=9';"></td>
			</TD>
		</TR>
	</table>
</form>
	<form name="f2"  method="post" action="affaire_prev_facture_ps.php" target="principal">
	<input type=hidden name="save" value="1"/>
	<table id="matable" class=forumline cellSpacing=1 cellPadding=1 width="100%" align=center border=0>
		<tr>
			<td class="m3" colspan="3">&nbsp;</td>
			<td class="m3" colspan="8">Carnet de commande</td>
			<td class="m3" colspan="9">Réalisé</td>
		</tr>
		<tr>
			<td class="m3" width="1%"><img src="images/redo.gif" border="0" alt="Copier la ligne" title="Copier la ligne"></td>
			<td class="m3" width="10%">Date commande</td>
			<td class="m3" width="10%">Date facture</td>
			<td class="m3" width="10%">Affaire</td>
			<td class="m3" width="10%">N°CDE Client</td>
			<td class="m3" width="10%">Poste</td>
			<td class="m3" width="10%">OF Client</td>
			<td class="m3" width="10%">Référence</td>
			<td class="m3" width="10%">Qté</td>
			<td class="m3" width="10%">P.U</td>
			<td class="m3" width="10%">Montant prévisionnel</td>
			<td class="m3" width="10%">Montant facture</td>
			<td class="m3" width="10%">Différence</td>
			<td class="m3" width="10%">Commentaire</td>
			<td class="m3" width="10%">N° de facture</td>
			<td class="m3" width="10%">Type</td>
			<td class="m3" width="10%">Date de paiement</td>
			<? if(d_ok(302)) echo '<td class="m3" width="10%"><img title="Facture à créer" src="images/add.gif" onclick="sel();" border="0"></td>';
			?>
		</tr>
		<?
		$nb_ligne=1;
		$r=my_query("select id_facture, count(*) as rowspan, sum(mt) as mt_prev from affaire_prev where prev_ps = 1 group by id_facture");
		while($l=mysql_fetch_array($r))
			{
			$rowspan[$l["id_facture"]] = $l["rowspan"];
			$tot_prev[$l["id_facture"]] = $l["mt_prev"];
			}
		//$order_by = "d_cde desc, ap.n_cde_c desc, des asc, d_facture asc";
		$order_by = "ap.id_facture desc,  ap.n_cde_c asc";
		//ap.d_facture desc,
		$sql="select ap.*, f.numero as n_fac, f.type as type_fac, f.mt_ht as mt_fac, f.d_reglement as d_reglement , f.id_pere
		from affaire_prev ap
		left join facture as f on f.id = ap.id_facture
		where ap.prev_ps = 1 ".$affaire_prev_facture_ps_req."
		order by ".$order_by .", poste asc";
		
		//echo $sql;
		$res=my_query($sql);
		//echo $sql;
		$nb=mysql_num_rows($res) + 2;
		$nb_coche = 0;
		$t_diff = 0;
		$t_mt_fac = 0;
		while($row=mysql_fetch_array($res))
			{
			une_ligne($nb_ligne,$row["id"],$row["d_cde"],$row["id_affaire"],$row["n_cde_c"],$row["poste"],$row["observation"],$row["qte"],$row["p_u"],$row["mt"],$row["commentaire"],$row["d_facture"],$row["n_fac"],$row["id_facture"],$row["type_fac"],$row["d_reglement"],$row["mt_fac"],$t_diff,$nb_coche,$rowspan,$tot_prev,$t_mt_fac,$row["id_pere"], $row['of_client']);
			if($row["id"]>0 and $row["id_facture"]==0 and isda($row["d_facture"]) and $row["mt"]<>0 and $row["poste"]>0 )
				{
				$tc[]=$nb_ligne;
				$nb_ligne++;
				}
			
			$n=0;
			$t_mt += $row["mt"];
			}

		for($j=1;$j<21;$j++)
			{
			une_ligne($nb_ligne);
			$nb_ligne++;
			$n=0;
			}
		?>
		<tr>
			<td class="m3" colspan=10>TOTAL : </td>
			<td class="m3" nowrap><? echo nformat($t_mt,'',1); ?>&nbsp;&euro;</td>
			<td class="m3" nowrap><? echo nformat($t_mt_fac,'',1); ?>&nbsp;&euro;</td>
			<td class="m3" nowrap><? echo nformat($t_diff,'',1); ?>&nbsp;&euro;</td>
			<? 
			if(d_ok(302))
				{
				echo '<td class="m3" colspan=7>Date de la facture : <input size="9" type="text" name="date_fac_choisi" readonly onclick="return showCalendar(\'date_fac_choisi\', \'%d/%m/%Y\');" id="date_fac_choisi" value="'.date('d/m/Y').'">&nbsp;<input type="button" onclick="creer_facture('.$nb_coche.');" value="Créer une facture"></td>';
				}
				else
				{
				echo '<td class="m3" colspan=7>&nbsp;</td>';
				}
			?>
		</tr>
	</table>

	</form>
	
<script LANGUAGE="JavaScript">
var select = 0;

function sel()
{
if(select == 0)
	{
	select=1;
	sel_all();
	}
	else
	{
	select=0;
	dsel_all();
	}
}

function sel_all()
{
<?
for($i=1;$i<=$nb_coche;$i++)echo "document.getElementById('fp_coche".$i."').checked=true;\n";
//foreach($tc as $k => $i)
?>
}
function dsel_all()
{
<?
//foreach($tc as $k => $i)echo "document.getElementById('fp_coche".$i."').checked=false;\n";
for($i=1;$i<=$nb_coche;$i++)echo "document.getElementById('fp_coche".$i."').checked=false;\n";
?>
}
</script>

<?
echo pied_page();
?>
