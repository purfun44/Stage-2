<?
include ('fonction.php');
include("fonction_affaire.php");

if ($id_req==1) 
{	

	$sql="select p.id_of, p.id_gt, p.numero, p.designation, p.clos, a.id, a.designation1, p.d_fin_sap, o.numero_client, o.msn from phase_i p left join of o on p.id_of=o.id left join affaire a on o.id_affaire=a.id where p.id='".$n_bon."'";
	$res=my_query($sql);
	
	if ($row=mysql_fetch_array($res))
	{
		echo "<br>	
			<table class=forumline2 cellSpacing=1 cellPadding=2 width='100%' align=center border=0>
				<tr class=m3>
					<td align=center>Affaire</td>
					<td align=center>Designation</td>
					<td align=center>Date de cloture</td>
				</tr>
				<tr class='cel2'>
					<td align=center>".$row['id']."</td>
					<td align=center>".$row['designation1']."</td>
					<td align=center>".datodf($row['d_fin_sap'])."</td>
				</tr>
			</table>
			<br>
			<table class=forumline2 cellSpacing=1 cellPadding=2 width='100%' align=center border=0>
				<tr class=m3>
					<td>OF</td>
					<td>GT</td>
					<td>Phase</td>
					<td>Designation</td>
					<td>Clos</td>
				</tr>
				<tr class='cel2'>
					<td align=center>".$row['id_of']."</td>
					<td align=center>".$row['id_gt']."</td>
					<td align=center>".format_0($row['numero'],4)."</td>
					<td align=center>".$row['designation']."</td>
					<td align=center><img src='images/statut".$row['clos'].".gif'></td>
				</tr>
			</table>
			<br>
			<input type='hidden' name='id_of' value='".$row['id_of']."' >
			<input type='hidden' name='id_gt' value='".$row['id_gt']."' >
			<input type='hidden' name='n_phase' value='".format_0($row['numero'],4)."' >
			<input type='hidden' id='id_affaire' name='id_affaire' value='".$row['id']."' >
			<input type='hidden' name='bon' value='1'>";

	}
	else
	{
		echo "<font size=2><center>Ce numero de bon n'existe pas</center></font>
			<input type='hidden' name='bon' value='0'>";
	}
}

if ($id_req==2) 
{	
	$mat_ok=1;
	if (strpos($matricule,",")>0){$mat=array_unique(explode(",",$matricule));}else{$mat[]=$matricule;}

	foreach ($mat as $m)
		{
		if (is_numeric($m))
			{
			$sql="select nom, prenom from interne where mat=".$m;
			$res=my_query($sql);
			
			if ($row=mysql_fetch_array($res))
				{	
				echo "<br>".(string)str_pad($m,4,"0",STR_PAD_LEFT)." - ".$row[0]." ".$row[1];	
				}
				else 
				{
				$mat_ok=0;
				}
			}
			else
			{
			$mat_ok=0;
			}				
		}
		echo '<input type="hidden" name="mat_ok" value="'.$mat_ok.'">';
		if($mat_ok==0)echo "<br>Ce matricule n'existe pas";
	
}

if ($id_req==3) 
{	

	if (istime4($temps))
	{
		echo t4totime($temps);
		echo "<input type='hidden' id='temps_hhmm' name='temps_hhmm' value='1'>";
	}
	else
	{
		echo 'Erreur de saisie';
		echo "<input type='hidden' id='temps_hhmm' name='temps_hhmm' value='0'>";
	}
}

if ($id_req==4) 
{	


echo "<input type='hidden' id='temps_hhmm' name='temps_hhmm' value='1'>";

}
// suppression d'une phase dans une gamme
if ($id_req==5) 
	{	
	del_visit(__FILE__,__LINE__,DL_1,"phase","where id=".$del_phase);
	}

//Verification du numero client dans l'ajout d'un of
if ($id_req==6)
{
$of=nombre_de("select id from of where numero_client like '".$numero_client."' and poste like '".$poste."' limit 1");
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
if(($of>0) and ($numero_client!='*'))
	{
	?>
	<root>
		<cell>1</cell>
		<cell>statut0.gif</cell>
		<cell><?echo "Le numéro d'ordre ".$numero_client." est déjà attribué à l'OF ".$of." et au poste ".$poste." !";?> </cell>
	</root>
	<?
	}
else
	{
	?>
	<root>
		<cell>0</cell>
		<cell>statut1.gif</cell>
		<cell>Ce N° d'ordre est libre</cell>
	</root>
	<?
	}
}

//recherche par id_phase_i
if ($id_req==7)
{
$sql="select pi.id, of.numero_client, pi.statut, pi.gt_code, pi.numero, pi.quantite, pi.id_of from phase_i pi left join of of on pi.id_of=of.id where pi.id='".$n_bon."'";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne>0)
	{
		$row=mysql_fetch_array($res);	
		if ($row['statut']==$j_statut_corr[$statut])
			{
			?>
			<root>
				<cell><?echo $n_bon;?></cell>
				<cell><?echo $row['numero_client'];?> </cell>
				<cell><?echo $row['gt_code'];?></cell>
				<cell><?echo format_0($row['numero'],4);?></cell>
				<cell><?echo $row['quantite'];?></cell>
				<cell><?echo $row['id_of'];?></cell>
				<cell>tick.png</cell>
				<cell>Statut valide</cell>
			</root>
			<?
			}
			else
			{
			?>
			<root>
				<cell><?echo $n_bon;?></cell>
				<cell><?echo $row['numero_client'];?> </cell>
				<cell><?echo $row['gt_code'];?></cell>
				<cell><?echo format_0($row['numero'],4);?></cell>
				<cell><?echo $row['quantite'];?></cell>
				<cell><?echo $row['id_of'];?></cell>
				<cell>Warning.png</cell>
				<cell>Statut non valide : <?echo $j_statut[$row['statut']];?></cell>
			</root>
			<?
			}
	}
	else
	{
	?>
	<root>
		<cell> </cell>
		<cell> </cell>
		<cell>Numero de bon non valide.</cell>
		<cell> </cell>
		<cell> </cell>
		<cell> </cell>
		<cell>Warning.png</cell>
		<cell>Numero de bon non valide.</cell>
	</root>
	<?
	}

}

//verification reference gamme
if ($id_req==8)
{
	$x=nombre_de("select count(*) from piece where ref like '".$ref."' and id <> '$ref_id'");
	if($x>0){echo '0';}else{echo '1';}
	
}

//recherche bon par numero client
if ($id_req==9)
{
	$sql="select id from of  where numero_client='".$ligne_ordre."'";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne==1)
		{
		$row=mysql_fetch_array($res);
		$row2=phase_en_cour($row['id']);
		if (is_array($row2))
			{
			?>
			<root>
				<cell>1</cell>
				<cell><?echo $row2['id'];?></cell>
			</root>
			<?
			}
			else
			{
			?>
			<root>
				<cell>2</cell>
				<cell><?echo 'Aucune phase en cour pour l\'OF '.$row2['id'].'.';?></cell>
			</root>
			<?
			}
		}
		else
		{
		?>
		<root>
			<cell>2</cell>
			<cell><?echo 'Le numero client '.$ligne_ordre.' n\'existe pas.';?></cell>
		</root>
		<?
		}
}

//recherche par of
if ($id_req==10)
{
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	$row=phase_en_cour($id_of);
	if (is_array($row))
		{
		?>
		<root>
			<cell>1</cell>
			<cell><?echo $row['id'];?></cell>
		</root>
		<?
		}
		else
		{
		?>
		<root>
			<cell>2</cell>
			<cell><?echo 'L\'OF '.$id_of.' n\'existe pas.';?></cell>
		</root>
		<?
		}
	
}

//Confirmation des bons
if ($id_req==11)
{
	$sql="select pi.id, of.numero_client, pi.statut, pi.gt_code, pi.numero, pi.quantite, pi.id_of from phase_i pi left join of of on pi.id_of=of.id where pi.id='".$n_bon."'";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	if ($nb_ligne>0)
	{
		$row=mysql_fetch_array($res);	
		if ($row['statut']==$j_statut_corr[$statut])
			{
			echo $n_bon."#!#".$row['numero_client']."#!#".$row['gt_code']."#!#".$row['numero']."#!#".$row['quantite']."#!#".$row['id_of']."#!#tick.png#!#Statut valide#!#";
			}
			else
			{
			echo $n_bon."#!#".$row['numero_client']."#!#".$row['gt_code']."#!#".$row['numero']."#!#".$row['quantite']."#!#".$row['id_of']."#!#Warning.png#!#Statut non valide : ".$j_statut[$row['statut']].".#!#";
			}
	}
	else
	{
	echo ' #!# #!#<b> Numero de bon non valide.</b>#!# #!# #!# #!#Warning.png#!# Numero de bon non valide.#!#';
	}
	
}

if ($id_req==12)
{
	$sql="select id from of where numero_client='".$ligne_ordre."'";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	if ($nb_ligne==1)
	{
		$row=mysql_fetch_array($res);
		$row2=phase_en_cour($row['id']);
		if (is_array($row2))
			{
			echo "1#!#".$row2['id']."#!#";
			}
			else
			{
			echo '2#!#<b> Aucune phase en cour pour l\'OF '.$row2['id'].'.</b>#!#';
			}
	}
	else
	{
	echo '2#!# <b>Le numero client '.$ligne_ordre.' n\'existe pas.</b>#!#';
	}
}

if ($id_req==13)
{
	$row=phase_en_cour($id_of);
	if (is_array($row))
	{
			echo "1#!#".$row['id']."#!#";
	}
	else
	{
	echo '2#!# <b>L\'OF '.$id_of.' n\'existe pas.</b>#!#';
	}
	
}


// droit_afficher.php
if ($id_req==14)
{
$res=my_query("update droit set g".$groupe." = if(g".$groupe."=0,1,0) where id = ".$id."");
if(nombre_de("select g".$groupe." from droit where id = $id")) echo $groupe; else echo '-';
}


// affaire_difusion_doc_ajouter.php

if ($id_req==15)
{
$sql='select ref from diff_docs where ref regexp "^'.$_SESSION['affaire_en_cour'].'-[0-9]{2}-[A-Z]{3}$" order by ref desc limit 1';
$tableau=explode('-',nombre_de($sql));
$var=$tableau[1]+1;
//$req="select acr from diff_docs_type where id=4";
$ref=$_SESSION['affaire_en_cour'].'-'.format_0($var,2).'-'.$acr;
echo $ref;
}


// requete_verif.php
if ($id_req==16)
{
$sql='select req from requete_verif where id='.$id;
$etat=nombre_de(nombre_de($sql))>0?0:1;
echo $etat;
}

//requeteur.php
if ($id_req==17)
{
$req="select type, liste_d2, tb, nom_champ_table from requeteur_struct where id='".$champ."'";
$res=my_query($req);
$res2=mysql_fetch_assoc($res);
switch($res2['type'])
{
	case "text" :
		$inp='<input type=text size=50 maxlenght=100 name="critere1" value="">';
		break;
	case "liste_d2" :
		$inp='<select name="critere1" size="1" '.$res2['liste_d2'].'>';
		$resultat=my_query('select id, '.$res2['nom_champ_table'].' from '.$res2['tb'].'');
		while($row=mysql_fetch_array($resultat))
		{
			$inp.='<option value="'.$row[0].'">'.$row[1].'</option>';
		}
		$inp.='</select>';
		break;
	case "date" :
		$inp='<INPUT onclick="return showCalendar(\'sel1\',\'%d/%m/%Y\');"  id=sel1 size=11 type="text"  name="critere1" value=""> &nbsp; &nbsp; au &nbsp; &nbsp; <INPUT onclick="return showCalendar(\'sel2\',\'%d/%m/%Y\');"  id=sel2 size=11 type="text"  name="critere2" value=""> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; / &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type=checkbox name="critere3"  > Vide &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=checkbox name="critere4"  > Pas vide</td>';
		break;
	case "liste_db" :
		$inp='<select name="critere1" size="1" '.$res2['liste_d2'].'>';
		$resultat=my_query('select id, '.$res2['nom_champ_table'].' from '.$res2['tb'].' where 1');
		while($row=mysql_fetch_array($resultat))
		{
			$inp.='<option value="'.$row[0].'">'.$row[1].'</option>';
		}
		$inp.='</select>';
		break;
	case "textarea" :
		$inp='<textarea cols="" rows="" name="critere1" value="">';
		break;
	case "checkbox" :
		$inp='<input type=checkbox name="critere1"  > Vide &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=checkbox name="critere2"  > Pas vide';
		break;
}
echo $inp;
}



//recherche par ref
if ($id_req==18)
{
$sql="select * from piece where actif = 1 and ref like '$ref' and id_affaire > 0";
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne>0)
		{
		$row=mysql_fetch_array($res);	
		?>
		<root>
			<cell>tick.png</cell>
			<cell>Référence valide</cell>
			<cell><?echo $row['id_affaire'];?></cell>
			<cell><?echo $row['designation'];?></cell>
			<cell><?echo htmlentities(liste_db("select id,indice from gamme where id_piece = ".$row['id']."  and valid = 1 order by id desc",0,"gamme[$n_ligne]","style=\"width:100;\"",""));?></cell>
		</root>
		<?
//concat(indice,' ',commentaire)
		}
	else
		{
		?>
		<root>
			<cell>Warning.png</cell>
			<cell>Référence non valide.</cell>
			<cell></cell>
			<cell></cell>
			<cell></cell>
		</root>
		<?
		}

}

//recherche n_ordre
if ($id_req==19)
{
$sql="select * from of where numero_client like '$ordre' and poste like '$poste' ";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
	
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne==0)
		{
		?>
		<root>
			<cell>tick.png</cell>
			<cell>Numéro d'ordre valide</cell>
			<cell>0</cell>
		</root>
		<?

		}
	else
		{
		$row=mysql_fetch_array($res);	

		?>
		<root>
			<cell>Warning.png</cell>
			<cell>Numéro d'ordre déjà utilisé dans l'of <?echo format_0($row['id'],6)." avec le poste ".$poste;?>.</cell>
			<cell>1</cell>
		</root>
		<?
		}

}

// Recupere les informations du filtre pour une colonne (affichage_liste_ajouter.php)
if ($id_req==20)
	{
	$res = my_query('
		SELECT filtre_type, filtre_info
		FROM affichage_colonne
		WHERE id = "'.$id_col.'"');

	$row = mysql_fetch_array($res);

	$filtre_type = $row["filtre_type"];
	$filtre_info = $row["filtre_info"];

	$t = filtre_gen($filtre_type, $filtre_info);
	/*
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	echo '<root>
		<cell>'.htmlentities($t[1]).'</cell>
		<cell>'.htmlentities($t[0]).'</cell>
	</root>';
	*/
	echo $t[0].'@##@'.$t[1];
	}

// Genere le champ du choix de l'operateur en fonction du type de filtre de la colonne
function filtre_gen($filtre_type, $filtre_info) 
{
switch($filtre_type)
	{
	// type = int(id)
	case 1:
	$t[0] = '
		<select name="operateur_ajax">
			<option value="=">Je veux voir</option>
			<option value="!=">Je veux pas voir</option>
		</select>';
	$t[1] = '<input '.$filtre_info.' name="valeur_ajax" />';
	break;
	
	// type = int
	case 2:
	$t[0] = '
		<select name="operateur_ajax">
			<option value=">">></option>
			<option value="<"><</option>	
			<option value=">=">>=</option>
			<option value="<="><=</option>
			<option value="=">=</option>	
		</select>';
	$t[1] = '<input '.$filtre_info.' name="valeur_ajax" />';
	break;
	
	// type = varchar
	case 3:
	$t[0] =	'
		<select name="operateur_ajax">
			<option value="like">Contient</option>
			<option value="not like">Ne contient pas</option>
		</select>
		';
	$t[1] = '<input '.$filtre_info.' name="valeur_ajax" />';
	break;
	
	// type = textarea
	case 4:
	$t[0] = '
		<select name="operateur_ajax">
			<option value="like">Contient</option>
			<option value="not like">Ne contient pas</option>
		</select>';
	$t[1] = '<textarea '.$filtre_info.' name="valeur_ajax" >';
	break;
	
	// type = date
	case 6:
	$t[0] = '
		<select name="operateur_ajax">
			<option value=">">></option>
			<option value="<"><</option>	
			<option value=">=">>=</option>
			<option value="<="><=</option>
			<option value="=">=</option>	
		</select>';
	$t[1] = '<input '.$filtre_info.' name="valeur_ajax" size="11" type="text" id="val_calendar" onclick="return showCalendar(\'val_calendar\',\'%d/%m/%Y\');" />'.calendar('val_calendar');
	break;
		
	// type = liste_db
	case 7:
	$t[0] =	'
		<select name="operateur_ajax">
			<option value="=">Je veux voir</option>
			<option value="!=">Je veux pas voir</option>
		</select>';
	$t[1] = liste_db($filtre_info,'','valeur_ajax');
	break;
	
	// type = liste_d2 + checkbox + radio
	case "8" :
	case "5" :
	case "9" :
	global ${$filtre_info};
	$t[0] = '
		<select name="operateur_ajax">
			<option value="=">=</option>
			<option value="!="><></option>
		</select>';
	$t[1]= liste_d2(${$filtre_info},'','valeur_ajax');
	break;
	}
return $t;
}

if ($id_req==21)
{
if ($reservation_id>0)
	{
	echo reservation($reservation_id, $id_login);
	}
}


if ($id_req==22)
{
if ($etat == "ajouter")
	{
	my_query ("insert into externe_favoris (id_login, id_contact, maj) values (".$id_login.",".$id_contact.",'".date ("Y-m-d")."');"); 
	echo '<img src="images/checkin.png" alt="supprimer" onclick="if(confirm(\'Voulez vous vraiment supprimer ce contact de vos favoris ?\'))annuaire_favoris(\'supprimer\', '.$id_login.','.$id_contact.','.$n_favoris.');">';
	}
else if ($etat == "supprimer")
	{
	my_query ("delete from externe_favoris where id_login=".$id_login." and id_contact=".$id_contact); 
	echo '<img src="images/etoile.png" alt="ajouter" onclick="if(confirm(\'Voulez vous vraiment ajouter ce contact dans vos favoris ?\'))annuaire_favoris(\'ajouter\', '.$id_login.','.$id_contact.','.$n_favoris.');">';
	}
}

if ($id_req==23)
{
$j_categorie_piece = dbtodata("select id, nom from gamme_cat");
$j_famille_piece = dbtodata ("select id, nom from gamme_famille");
$req = ("select id from outillage_piece where id_outillage=".$id." and id_piece=".$id_piece);
$res2 = my_query ($req);
$nb_ligne=mysql_num_rows($res2);
if ($nb_ligne==0)
	{
	my_query ("insert into outillage_piece (id_outillage, id_piece, id_login, maj) values (".$id.",".$id_piece.",".$id_login.",'".date("Y-m-d")."')");
	}
	$sql=("select piece.id as id, ref, designation, id_affaire_type, id_cat, id_famille from outillage_piece inner join piece on outillage_piece.id_piece=piece.id order by id");
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	echo '<tr>
		<td class="m3" colspan=7 >Pièces liées</td>
	</tr>
	<tr>
		<td class="m3" width=10%>Id</td>
		<td class="m3" width=10%>Référence</td>
		<td class="m3" width=24% ewrap>Désignation</td>
		<td class="m3" width=25%>Catégorie</td>
		<td class="m3" width=25%>Famille</td>
		<td class="m3" width=6%>Annuler</td>
	</tr>';

	while ($row=mysql_fetch_array($res))
		{
		echo '<tr align="center">
			<td class="cel1">'.$row['id'].'</td>
			<td class="cel1">'.$row['ref'].'</td>
			<td class="cel1">'.$row['designation'].'</td>
			<td class="cel1">'.$j_categorie_piece[$row['id_cat']].'</td>
			<td class="cel1">'.$j_famille_piece[$row['id_famille']].'</td>
			<td class="cel1" align="center"><img src="images/delete.gif" alt="supprimer" onclick="if(confirm(\'Voulez vous vraiment annuler cette réservation ?\')) location.href=\'outillage_ajouter.php?del_id='.$row['id'].'&outillage_id='.$id.'\'"></td>'.
		'</tr>';
		}
}

if ($id_req==24)
{
/*//my_query("insert into affichage_colonne (id_page, m_table, m_alias, col, nom, defaut, ordre, filtre_type, filtre_info, droit, manuel, format) values (".$r_page.", 'toto', 'toto', 'toto', 'toto', '1', ".$ordre.", 1, 'toto', 1, 't','toto')");
if ($ordre % 2 == 1)
	{
	$cid = "class= \"cel2\"";
	}
	else
	{
	$cid = "class= \"cel1\"";
	}
	
$id=mysql_insert_id();
echo '<td align=center "'.$cid.'"  ><input "'.$cid.'" style="border:0;text-align:center;" readonly type="text" name="id['.$ic.']" value="'.$id.'"></td>
	<td class= "cel4"       ondblclick=upd_m_table.form(this,"'.$id.'","T")></td>
	<td class= "cel4"       ondblclick=upd_m_alias.form(this,"'.$id.'")></td>
	<td class= "cel4"       ondblclick=upd_nom.form(this,"'.$id.'")></td>
	<td class= "cel4"       ondblclick=upd_col.form(this,"'.$id.'")></td>	
	<td class= "cel4"       ondblclick=upd_ordre.form(this,"'.$id.'")>'.$ordre.'</td>	
	<td class= "cel4" align=center title="Monter ce tri" ><img src="images/haut.gif" onclick="document.location.href=\'droit_colonne_afficher.php?img_up_a='.$ordre.'&img_up_id_a=<?echo $id?>&scrolly=\'+window.scrollY"></td>																												
	<td class= "cel4"       ondblclick=upd_droit.form(this,"'.$id.'")></td>	
	<td class= "cel4"       ondblclick=upd_filtre_type.form(this,"'.$id.'")></td>		
	<td class= "cel4"       ondblclick=upd_filtre_info.form(this,"'.$id.'")></td>
	<td class= "cel4"       ondblclick=upd_manuel.form(this,"'.$id.'")></td>	
	<td class= "cel4"       ondblclick=upd_format.form(this,"'.$id.'")></td>';*/
echo liste_db($requete." and ".$tri." like '".$txt."%'",$lstname.$indice,"lstname".$indice,'style="display:none" id="lstname'.$indice.'" size="80"');
}


if($id_req==25) 
{
	$res = my_query('
		SELECT filtre_type
		FROM affichage_colonne
		WHERE id = '.$id_col);
	$row =  mysql_fetch_array($res);

	$filtre_type = $row["filtre_type"];

	$t = format_gen($filtre_type);

	echo $t[0].'@##@'.$t[1].'@##@'.$t[2];
}

function format_gen($filtre_type) 
{
// Compléter jusqu\'à <input type=text size=1 maxlenght=10 name="valeur_format[]" value="x"> caractère(s) <input type=text size=1 maxlenght=10 name="valeur_format[]" value="0"> &nbsp; &nbsp; 
switch($filtre_type)
	{
	case '1': // type = int (id)
	break;
	case '2': // type = int 
	$t[0] = '
		<table>
			<tr><td class="cel1">Arrondi</td><td><input type=text size=2 name="valeur_ajax" value="2"></td></tr>
			<tr><td class="cel1">Séparateur milliers</td><td><input type=text size=2 name="valeur_ajax" value=" "></td></tr>
			<tr><td class="cel1">Séparateur décimal</td><td><input type=text size=2 name="valeur_ajax" value="."></td></tr>
			<tr><td class="cel1">Affichage si vide</td><td><input type=text size=2 name="valeur_ajax" value="0"></td></tr>
			<tr><td class="cel1">Compléter jusqu\'à</td><td class="cel1"><input type=text size=2 name="valeur_ajax" value="0">&nbsp;&nbsp;&nbsp;caractère(s) <input type=text size=2 name="valeur_ajax" value="0"></td></tr>
			<tr><td class="cel1">Retirer les zéros à droite</td><td><input type=checkbox name="valeur_ajax" value="0" onClick="if(this.checked){this.value=\'1\';}else{this.value=\'0\';}" /></td></tr>
		</table>';	
	
	break;
	
	case '3': // type = varchar
	case '4': // type = textarea
	case '5': // type = checkbox	
	case '7': // type = liste_db
	case '8': // type = liste_d2
	case '9': // type = radio 
	$t[0] =	'
		<select name="valeur_ajax">
			<option value="1">Première lettre en majuscule</option>
			<option value="2">Mot en majuscule</option>
			<option value="3">Mot en minuscule</option>
		</select>';
	break;
	
	case '6': // type = date
	$t[0]=	'<input type=text size=20 maxlenght=100 name="valeur_ajax" value="">';
	$t[1]=  '<img src="images/detail.gif" title="Tableau des dates" onclick="window.open(\'popup_date.php\',\'Tableau des dates\',\'left=200,top=200,toolbar=0,location=0,directories=0,status=1,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=500,height=500\')">';	
	break;
	
	case '10': // type = time
	$t[0]=	'<input type=text size=20 maxlenght=100 name="valeur_ajax" value="">';
	$t[1]=  '<img src="images/detail.gif" title="Tableau des heures" onclick="window.open(\'popup_time.php\',\'Tableau des heures\',\'left=200,top=200,toolbar=0,location=0,directories=0,status=1,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=500,height=500\')">';		
	break;
	}
	
$t[2] = $filtre_type;
return $t;
}

if($id_req == 26)// affecter des equipements à un article
{
	$reponse = "";
	$tab_get = explode(",", $t_chb_val);// $tabChBval recoit xxx,yyy,zzz ...
	
	if($mode_ajout > 0)
		{
		$sqlIns = "insert into equipement_article (id_equipement, id_article) values";
		$i = 0;
		foreach($tab_get as $tab_aj)
			{
			if(nombre_de("select count(*) from equipement_article where id_equipement = $tab_aj and id_article = $id_article") == 0)
				{
				if($i==0)
					$sqlIns.=" (".$tab_aj.", $id_article)";
				else
					$sqlIns.=", (".$tab_aj.", $id_article)";
				$i++;
				}
			}
		
		if($i > 0)
			{
			my_query($sqlIns);
			$reponse = "ok";
			}
		}
		else
		{
		foreach($tab_get as $tab_del)
			{
			my_query("delete from equipement_article where id_equipement = $tab_del and id_article = $id_article");
			}
		$reponse = "ok";
		}
	echo $reponse;
}

if($id_req == 27) // Changement de la liste par défaut
{
	if(nombre_de("select count(*) from affichage_liste where id_interne=".$id_login." and id_page=".$id_page) > 0)
		my_query('update affichage_liste set du="0" where id_interne='.$id_login.' and id_page='.$id_page);
	if(nombre_de("select count(*) from affichage_liste where id=".$id_liste." and dg='0'"))
		my_query('update affichage_liste set du="1" where id='.$id_liste.'');
	
	$_SESSION['id_list'] = $id_liste;
	echo "ok";
}

if($id_req == 28) // Changement top gt
{
$x=nombre_de("select id from interne_gt where id_interne=".$id_interne." and top=1")+0;
my_query('update interne_gt set top=0 where id_interne='.$id_interne);
my_query('update interne_gt set top=1 where id='.$id);
echo "$x";
}

//autocomplétion d'une ligne si sont numéro d'of est entré
if ($id_req==29)
{
$sql="select * from of where id=".$of." ";
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne>0)
		{
		$row=mysql_fetch_array($res);	
		
		if($row['etat']==2){$qte=$row['quantite_ok'];}
		else{$qte=$row['quantite'];}
		?>
		<root>
			<cell>1</cell>
			<cell><?echo $row['ref']."  ".$row['designation'];?></cell>
			<cell><?echo $qte; ?></cell>
			<cell><?echo $row['numero_client'];?></cell>
			<cell><?echo format_0($row['poste'],4);?></cell>
			<cell><?echo $row['of_client'];?></cell>
		</root>
		<?
		}
	else
		{
		?>
		<root>
			<cell>0</cell>
		</root>
		<?
		}
}

if($id_req == 30)// affecter des equipements à un contrat
{
	$reponse = "";
	$tab_get = explode(",", $t_chb_val);// $tabChBval recoit xxx,yyy,zzz ...
	
	if($mode_ajout > 0)
		{
		$sqlIns = "insert into equipement_contrat (id_equipement, id_contrat) values";
		$i = 0;
		foreach($tab_get as $tab_aj)
			{
			if(nombre_de("select count(*) from equipement_contrat where id_equipement = $tab_aj and id_contrat = $id_contrat") == 0)
				{
				if($i==0)
					$sqlIns.=" (".$tab_aj.", $id_contrat)";
				else
					$sqlIns.=", (".$tab_aj.", $id_contrat)";
				$i++;
				}
			}
		
		if($i > 0)
			{
			my_query($sqlIns);
			$reponse = "ok";
			}
		}
		else
		{
		foreach($tab_get as $tab_del)
			{
			my_query("delete from equipement_contrat where id_equipement = $tab_del and id_contrat = $id_contrat");
			}
		$reponse = "ok";
		}
	echo $reponse;
}


if($id_req == 32)// affecter des fiches actions à une fiche enquête 5 p
{
	$reponse = "";
	$tab_get = explode(",", $t_chb_val);// $tabChBval recoit xxx,yyy,zzz ...
	
	if($mode_ajout > 0)
		{
		$sqlIns = "insert into fiche_5p_action (id_fiche_action, id_fiche_5p) values";
		$i = 0;
		foreach($tab_get as $tab_aj)
			{
			if(nombre_de("select count(*) from fiche_5p_action where id_fiche_action = $tab_aj and id_fiche_5p= $id_fiche_5p") == 0)
				{
				if($i==0)
					$sqlIns.=" (".$tab_aj.", $id_fiche_5p)";
				else
					$sqlIns.=", (".$tab_aj.", $id_fiche_5p)";
				$i++;
				}
			}
		
		if($i > 0)
			{
			my_query($sqlIns);
			$reponse = "ok";
			}
		}
		else
		{
		foreach($tab_get as $tab_del)
			{
			my_query("delete from fiche_5p_action where id_fiche_action = $tab_del and id_fiche_5p = $id_fiche_5p");
			}
		$reponse = "ok";
		}
	echo $reponse;
}

if($id_req == 33)// affecter des pièces à la gestion des équipements 
{
	$reponse = "";
	$tab_get = explode(",", $t_chb_val);// $tabChBval recoit xxx,yyy,zzz ...
	
	if($mode_ajout > 0)
		{
		$sqlIns = "insert into equipement_piece (id_piece, id_equipement) values";
		$i = 0;
		foreach($tab_get as $tab_aj)
			{
			if(nombre_de("select count(*) from equipement_piece where id_piece = $tab_aj and id_equipement= $id_equipement") == 0)
				{
				if($i==0)
					$sqlIns.=" (".$tab_aj.", $id_equipement)";
				else
					$sqlIns.=", (".$tab_aj.", $id_equipement)";
				$i++;
				}
			}
		
		if($i > 0)
			{
			my_query($sqlIns);
			$reponse = "ok";
			}
		}
		else
		{
		foreach($tab_get as $tab_del)
			{
			my_query("delete from equipement_piece where id_piece = $tab_del and id_equipement = $id_equipement");
			}
		$reponse = "ok";
		}
	echo $reponse;
}

if($id_req == 34)// affecter des pièces à la gestion des équipements 
{
	if($mode_ajout > 0)
	{
		$sqlIns = "insert into equipement_piece (id_piece, id_equipement) values";
		if(nombre_de("select count(*) from equipement_piece where id_piece = $t_chb_val and id_equipement= $id_equipement") == 0)
				{
					$sqlIns.=" ($t_chb_val, $id_equipement)";
					my_query($sqlIns);
					$reponse = "ok";
				}
	}
	else
	{
	if(nombre_de("select count(*) from equipement_piece where id_piece = $t_chb_val and id_equipement= $id_equipement") == 1)	
		{
		my_query("delete from equipement_piece where id_piece = $t_chb_val and id_equipement = $id_equipement");
		$reponse = "ok";
		}
	}
	echo $reponse;
}

if($id_req == 35)// maj_of dans achats
{
$var_of = "id_of[]";
if($of_simple <> '')$var_of = $of_simple;

	if(nombre_de("select count(*) from of where id_affaire = '$id_affaire'") > 0)
		{
		echo liste_db("select id , concat(id , ' - ',designation) from of where id_affaire = '$id_affaire'",0,$var_of);
			
		}
		else
		{
		echo '<input  type="text"  maxlength="10" name="'.$var_of.'" size="10" value="'.$row2["id_of"].'">';
		}
				
}

if($id_req == 36)// dashboard anychart
{
		
	$r_gt=$_SESSION['of_dashboard'];
	$r_saisie=$_SESSION['of_dashboard_saisie'];
	$r_semaine= $_SESSION['of_dashboard_semaine'];
	$r_annee = $_SESSION['of_dashboard_annee'];
	$r_semaine2= $_SESSION['of_dashboard_semaine2'];
	$r_annee2= $_SESSION['of_dashboard_annee2'];

	if($r_saisie==1)
		{
		$var_tps='tps_devis';
		$tps='Tps devis';
		}
		else
		{
		$var_tps='tps_obj';
		$tps='Tps obj';
		$r_saisie=2;
		}
	if($numero > 0)$req_plus=" and pi.numero = ".$numero;
	
	$req="select date_format(pi.d_fin_sap,'%x%v') as s,of.id_gamme ,of.id_piece, of.ref , of.designation , of.indice , sum($var_tps )as tps ,sum(pi.tps_reel)as tps_reel , sum($var_tps ) / sum(pi.tps_reel)as coef , pi.designation as des_pi , pi.quantite , pi.numero , pi.id_gt
		from phase_i as pi 
		left join of on of.id=pi.id_of
		where of.id_affaire_type=1 and statut=2 and of.id_gamme = $id_gamme and pi.id_dtnc = 0 and ( pi.d_fin_sap >= ( '".dftoda(weektoday($r_semaine,$r_annee,1))."')) and ( pi.d_fin_sap <= ( '".dftoda(weektoday($r_semaine2,$r_annee2,7))."')) $req_plus
		group by s,of.id_gamme 
		order by s,of.id_gamme ";

	//echo $req;
	$res=my_query($req);

	$turn=0;
	$xml="";

	$nb_ligne=mysql_num_rows($res);
	if ($nb_ligne==0){echo "<br>Aucun enregistrement trouvé <br>";exit;}

	$i=1;
	while ($ligne=mysql_fetch_array($res))
		{
		$cumul[$ligne["id_gamme"]]+=$ligne["tps"];
		$coef_semaine[$ligne["id_gamme"]]=div($ligne["tps"],$ligne["tps_reel"]);
		$cumul_reel[$ligne["id_gamme"]]+=$ligne["tps_reel"];
		$evol_moyenne[$ligne["id_gamme"]]=div($cumul[$ligne["id_gamme"]],$cumul_reel[$ligne["id_gamme"]]);

		$data_graph[$ligne["id_gamme"]][substr($ligne["s"],0,4).' S'.substr($ligne["s"],4,2)]=round($coef_semaine[$ligne["id_gamme"]],3);
		$data_graph_evol[$ligne["id_gamme"]][substr($ligne["s"],0,4).' S'.substr($ligne["s"],4,2)]=round($evol_moyenne[$ligne["id_gamme"]],3);
		$data_graph_tps[$ligne["id_gamme"]][substr($ligne["s"],0,4).' S'.substr($ligne["s"],4,2)]+=round($ligne["tps_reel"],2);
		
		$annee[substr($ligne["s"],0,4).' S'.substr($ligne["s"],4,2)]=substr($ligne["s"],0,4);
		$semaine[substr($ligne["s"],0,4).' S'.substr($ligne["s"],4,2)]=substr($ligne["s"],4,2);

		$i++;
		$reference=$ligne["ref"];
		$ref=$ligne["ref"].' - '.$ligne["designation"];
		$phase = $ligne["numero"];
		
		}
		
		if($numero > 0)$ref.= ' (phase '.$phase.')';
		
		$xml[$id_gamme] = '
	<anychart>
		<settings><animation enabled="True"/></settings>
		<charts>
			<chart plot_type="CategorizedVertical">
				<data_plot_settings default_series_type="line">
					<line_series>
						<tooltip_settings enabled="true">
<format>
{%SeriesName}
{%Value}{numDecimals:2}
</format>
						</tooltip_settings>
						<line_style><line thickness="2"  /></line_style>
						<marker_settings enabled="false"/>
					</line_series>
					
					<bar_series style="AquaLight" >
						<tooltip_settings enabled="true">
<format>
{%SeriesName}
{%Value}{thousandsSeparator: ,numDecimals:1} h
</format>
						</tooltip_settings>
					</bar_series>

				</data_plot_settings>
				<chart_settings>
					<title enabled="true">
						<text>'.$ref.'</text>
					</title>
					<axes>
						<x_axis>
						<labels display_mode="Rotated" rotation="70" align="inside"  />
						<zoom enabled="false" allow_drag="false" visible_range="90"/>
							<title enabled="false" />
						</x_axis>
						<y_axis>
							<title enabled="false" />
							<labels>
								<format>{%Value}{numDecimals:2}</format>
							</labels>
							<axis_markers>
								<lines><line value="1" color="green" thickness="2"/></lines>
							</axis_markers>
							<scale maximum="2.5" minimum="0" />
						</y_axis>
	   <extra>
            <y_axis name="extra_y_axis_1">
              <minor_grid enabled="false" />
              <major_grid enabled="false" />
<scale mode="Stacked" />
              <labels>
             <format>{%Value}{numDecimals:0}h</format>
              </labels>
              <title>
                <text>Pointages</text>
              </title>
            </y_axis>
          </extra>
					</axes>
				</chart_settings>
				<data>
					<series name="Coef semaine">';
					foreach ($data_graph[$id_gamme] as $k=>$d) if(!is_array($d)) $xml[$id_gamme] .= '<point name="'.($k).'" y="'.$d.'" />';
					$xml[$id_gamme] .= '</series>

					<series name="moyenne">';
					foreach ($data_graph_evol[$id_gamme] as $k=>$d) if(!is_array($d)) $xml[$id_gamme] .= '<point name="'.($k).'" y="'.$d.'" />';
					$xml[$id_gamme] .= '</series>

						<series y_axis="extra_y_axis_1" name="Pointage" type="Bar">';
					foreach ($data_graph_tps[$id_gamme] as $k=>$d) if(!is_array($d)) $xml[$id_gamme] .= '<point name="'.($k).'" y="'.$d.'" >
						<actions>
							<action type="Call" function="popup_ed(\''.$annee[$k].'\',\''.$semaine[$k].'\',\''.$reference.'\', \''.$numero.'\'  )" />
						</actions></point>';
					$xml[$id_gamme] .= '</series>
					
					
					
					
				</data>
			</chart>
		</charts>
	</anychart>
	';
	
echo $xml[$id_gamme];

}

if($id_req == 37) // cloture de facture d'achat
{
	$vf = new valid_form ;
	$vf->add("id_login",$id_login);
	$vf->add("maj",date('Y-m-d'));
	$vf->add("qte",$qte);
	$vf->add("p_u",$p_u);
	$vf->add("remise",$remise);
	$vf->add("code_tva",$code_tva);
	if($mode=='insert') // ajout d'une ligne de cloture de facture d'achat
	{
		$vf->add("id_achat_ligne",$id_achat_ligne);
		$vf->add("m_ht",$m_ht);
		$vf->add("m_ttc",$m_ttc);
		$vf->add("note",$note);
		$vf->add("fact_four",$fact_four);
		$vf->add("date_fact",dftoda($date_fact));
		$vf->add("date_echeance",dftoda($date_echeance));
		$new_id=$vf->insert("achat_cloture");
		echo $new_id;
	}
	else // modification quantité/p_u/tva facture achat
	{
		$vf->add("m_ht",$qte*($p_u-$remise));
		$vf->add("m_ttc",$qte*($p_u-$remise)*(1+($j_tva[$code_tva]/100)));
		$vf->update('achat_cloture',"where id=$id_achat_cloture");
		echo 'ok';
	}
	$vf->log(__FILE__,__LINE__,DL_1);
	// si besoin on cloture la ligne d'achat
	//$req = "SELECT count(*) FROM achat_ligne al WHERE al.id = $id_achat_ligne AND al.qte = (SELECT SUM(qte) FROM achat_cloture ac WHERE ac.id_achat_ligne = al.id)";
	//$res = mysql_fetch_array(my_query($req));
	$res = nombre_de("SELECT count(*) FROM achat_ligne al WHERE al.id = $id_achat_ligne AND al.qte = (SELECT SUM(qte) FROM achat_cloture ac WHERE ac.id_achat_ligne = al.id)");
	$vf = new valid_form;
	$vf->add("etat",10);
	$vf->update('achat_ligne',"where id=$id_achat_ligne AND 0 < $res[0]");
	$vf->log(__FILE__,__LINE__,DL_1);
	
	// si besoin on cloture la commande
	if($res > 0)// si on avait une ligne à cloturer
	{
		$res2 = my_query("SELECT ac.id FROM achat_ligne al
				LEFT JOIN achat_cmd ac ON al.id_cmd = ac.id
				WHERE al.id=$id_achat_ligne AND 0 = (SELECT count(*) FROM achat_ligne al2 WHERE al2.id_cmd = ac.id AND al2.etat < 10)");
		$row = mysql_fetch_array($res2);
		if(isset($row[0]) && $row[0] != null)
		{
			$vf = new valid_form;
			$vf->add("etat",4);
			$vf->update('achat_cmd',"where id=$row[0]");
			$vf->log(__FILE__,__LINE__,DL_1);
		}
	}
}

if($id_req == 38)// modification de la note d'une ligne de cloture
{
	$sql = "UPDATE achat_cloture SET note='$new_note' WHERE id=$id_achat_cloture";
	my_query($sql);
	echo 'ok';
}
if($id_req == 39)// sauvegarde nouveau numéro lot matière pour un of
{
	$sql = "SELECT n_lot FROM of WHERE id = $of";
	$res = my_query($sql);
	$tmp = mysql_fetch_array($res);
	$old_n_lot = $tmp[0];
	if($old_n_lot <> ''){$old_n_lot .= ';;';}
	$vf = new valid_form;
	$vf->add('n_lot',$old_n_lot.$new_n_lot);
	$vf->update('of',"WHERE id=$of");
	$vf->log(__FILE__,__LINE__,DL_1);
	echo 'ok';
}


if ($id_req==40)
{
$of=nombre_de("select id from of where of_client like '".$of_client."' limit 1");
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
if(($of>0) and ($of_client!='*'))
	{
	?>
	<root>
		<cell>1</cell>
		<cell>statut0.gif</cell>
		<cell><?echo "Le numéro d'OF Client ".$of_client." est déjà attribué à l'OF ".$of."  !";?> </cell>
	</root>
	<?
	}
else
	{
	?>
	<root>
		<cell>0</cell>
		<cell>statut1.gif</cell>
		<cell>Ce N° d'OF Client est libre</cell>
	</root>
	<?
	}
}

//recherche n_ordre
if ($id_req==41)
{
$sql="select * from of where of_client like '$of_client'  ";
	
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
	
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>',"\n";
	if ($nb_ligne==0)
		{
		?>
		<root>
			<cell>tick.png</cell>
			<cell>Numéro d'OF Client valide</cell>
			<cell>0</cell>
		</root>
		<?

		}
	else
		{
		$row=mysql_fetch_array($res);	

		?>
		<root>
			<cell>Warning.png</cell>
			<cell>Numéro d'OF Client déjà utilisé dans l'of <?echo format_0($row['id'],6);?>.</cell>
			<cell>1</cell>
		</root>
		<?
		}

}

//of_rapprochement2.php //recherche affaire prev d'of
if ($id_req==42)
	{
	$j_devise=dbtodata("SELECT id,nom FROM devise");
	$sql="SELECT ap.id,f.id as id_f,ap.of_client,ap.d_cde,ap.d_facture,ap.id_affaire,ap.n_cde_c,ap.poste,ap.qte,ap.p_u,ap.mt,f.id_devise,f.devise_tx,f.numero,f.date,f.mt_ht,f.type
		FROM of_rapprochement ora
		LEFT JOIN affaire_prev ap ON ap.id=ora.id_affaire_prev
		LEFT JOIN facture f ON f.id=ap.id_facture
		WHERE ora.id_of='".$id_of."'
		GROUP BY ap.id,ap.id_facture,ap.of_client";
	$res=my_query($sql);
	$nb_ligne=mysql_num_rows($res);
	header("Content-Type: text/xml");
 	echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
	if($nb_ligne==0){echo '<TD colspan="21">Il n\'y a aucun enregistrement!</TD>';}
	else
		{?>
		<TD colspan="21">
			<TABLE width="100%" border="1" rules="no">
				<TR class="m3">
					<TD width="7%">
						Cde Client
					</TD>
					<TD width="6%">
						Poste
					</TD>
					<TD width="7%">
						OF Client
					</TD>
					<TD width="7%">
						Date de Cde
					</TD>
					<TD width="7%">
						Date de facture
					</TD>
					<TD width="6%">
						Affaire
					</TD>
					<TD width="6%">
						Quantité
					</TD>
					<TD width="6%">
						Prix unitaire
					</TD>
					<TD width="6%">
						Montant
					</TD>
					<TD width="3%">
					</TD>
					<TD width="6%">
						Devise
					</TD>
					<TD width="6%">
						Devise taux
					</TD>
					<TD width="7%">
						Numéro
					</TD>
					<TD width="7%">
						Date
					</TD>
					<TD width="7%">
						Montant HT
					</TD>
					<TD width="6%">
						Type de facture
					</TD>
				</TR>
				<?php
				while($ligne=mysql_fetch_array($res))
					{
					$t[$ligne["id"]]["id"]=$ligne["id"];
					$t[$ligne["id"]]["n_cde_c"]=$ligne["n_cde_c"];
					$t[$ligne["id"]]["poste"]=$ligne["poste"];
					$t[$ligne["id"]]["of_client"]=$ligne["of_client"];
					$t[$ligne["id"]]["d_cde"]=$ligne["d_cde"];
					$t[$ligne["id"]]["d_facture"]=$ligne["d_facture"];
					$t[$ligne["id"]]["id_affaire"]=$ligne["id_affaire"];
					$t[$ligne["id"]]["qte"]=$ligne["qte"];
					$t[$ligne["id"]]["p_u"]=$ligne["p_u"];
					$t[$ligne["id"]]["mt"]=$ligne["mt"];
					$t[$ligne["id"]]["id_f"]=$ligne["id_f"];
					//$t2[$ligne["id_f"]]["id_f"]=$ligne["id_f"];
					$t2[$ligne["id_f"]]["id_devise"]=$ligne["id_devise"];
					$t2[$ligne["id_f"]]["devise_tx"]=$ligne["devise_tx"];
					$t2[$ligne["id_f"]]["numero"]=$ligne["numero"];
					$t2[$ligne["id_f"]]["date"]=$ligne["date"];
					$t2[$ligne["id_f"]]["mt_ht"]=$ligne["mt_ht"];
					$t2[$ligne["id_f"]]["type"]=$ligne["type"];
					$t2[$ligne["id_f"]]['rowspan2']+=1;
					$t2[$ligne["id_f"]]['print']=0;
					}
				$class="cel2";
				foreach($t as $ligne)
					{
					echo "<TR class='".$class."' align='center'>
						<TD class='cel3'>".$ligne['n_cde_c']."</TD>
						<TD>".$ligne['poste']."	</TD>
						<TD class='cel3'>".$ligne['of_client']."</TD>
						<TD>".datodf($ligne['d_cde'])."</TD>
						<TD class='cel3'>".datodf($ligne['d_facture'])."</TD>
						<TD >".$ligne['id_affaire']."</TD>
						<TD class='cel3'>".nformat($ligne['qte'],0,1)."</TD>
						<TD>".nformat($ligne['p_u'],0,1)."</TD>
						<TD class='cel3' >".nformat($ligne['mt'],0,1)."</TD>";
						if($ligne['id_f']>0 and $t2[$ligne["id_f"]]['print']==0)
							{
							$t2[$ligne["id_f"]]['print']=1;
							echo "	<TD class='m3' rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'></TD>
							<TD class='cel3' rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'>".$j_devise[$t2[$ligne["id_f"]]['id_devise']]."</TD>
							<TD rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'>".nformat($t2[$ligne["id_f"]]['devise_tx'],0,2)."</TD>
							<TD class='cel3' rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'><a class='b' href='/data/facture/facture_".$t2[$ligne["id_f"]]['numero'].".pdf'>".$t2[$ligne["id_f"]]['numero']."</a></TD>
							<TD rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'>".datodf($t2[$ligne["id_f"]]['date'])."</TD>
							<TD class='cel3' rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'>".nformat($t2[$ligne["id_f"]]['mt_ht'],0,1)."</TD>
							<TD rowspan='".$t2[$ligne["id_f"]]['rowspan2']."'>".$j_facture_type[$t2[$ligne["id_f"]]['type']]."</TD>";
							}
						else if($ligne['id_f']==0)
							{
							echo "	<TD colspan=7>Pas de facture</TD>";
							}
					echo "</TR>";
					}
			echo "</TABLE>
		</TD>";
		}
	}
?>
