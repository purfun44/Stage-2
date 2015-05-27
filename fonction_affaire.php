<?

	
///////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////          OF            //////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

function maj_affaire($id_affaire,$parent=1)
{
	maj_affaire_recap0($id_affaire);
	maj_affaire_recap1($id_affaire);
	maj_affaire_recap2($id_affaire);
	maj_affaire_recap3($id_affaire);
	maj_affaire_recap4($id_affaire);
	maj_affaire_recap5($id_affaire);

	maj_of_achat_real($id_affaire);
	
	maj_affaire_achat1($id_affaire);
	maj_affaire_achat2($id_affaire);
	//maj_affaire_achat_ventil($id_affaire);
	maj_affaire_achat3($id_affaire);
	maj_affaire_achat4($id_affaire);
	maj_affaire_achat5($id_affaire);
	if($parent)maj_affaire_recap_aieux($id_affaire);
}



function maj_affaire_recap_aieux($id_affaire)
{
$t=mes_aieux($id_affaire);
if (is_array($t)) foreach ($t as $l) 
	{
	maj_affaire_recap4($l);
	maj_affaire_recap5($l);

	maj_affaire_achat4($l);
	maj_affaire_achat5($l);
	}
}

function maj_affaire_pere($id_affaire,$tab = "")
{

$res = my_query("select id from affaire where pere='".$id_affaire."'");
while ($row=mysql_fetch_array($res))
	{
	$tab[]=$row[0];
	$tab=maj_affaire_pere($row[0],$tab);
	}
maj_affaire($id_affaire,0);
return $tab;
}

function ma_descendance ($id_affaire,$tab = "")
{

$res = my_query("select id from affaire where pere='".$id_affaire."'");
while ($row=mysql_fetch_array($res))
	{
	$tab[]=$row[0];
	$tab=ma_descendance($row[0],$tab);
	}
return $tab;
}


function mes_aieux ($id_affaire,$tab = "")
{
//if ($tab=="") $tab[]=$id_affaire;
$mon_pere=nombre_de("select pere from affaire where id='".$id_affaire."'");
if ($mon_pere>0)
{
	$tab[]=$mon_pere;
	$tab=mes_aieux($mon_pere,$tab);
}
return $tab;
}

function mon_origine($id_affaire)
{
//if ($tab=="") $tab[]=$id_affaire;
$tab=nombre_de("select pere from affaire where id='".$id_affaire."'");
if ($tab>0)
{
	$tab=mon_origine($tab);
}
if($tab>0){return $tab;}else{return $id_affaire;}
}

function ma_famille ($id_affaire)
{
$tab[0]=mon_origine($id_affaire);
$tab=ma_descendance($tab[0],$tab);
return $tab;
}

////////////////////////////
///////////////MO
////////////////////////////

//Somme des heures par of et par section pour id_affaire
function maj_affaire_recap0($id_affaire)
{
my_query("delete from affaire_recap_mo_0 where id_affaire = '$id_affaire'");
$res = my_query("select id_of, section, sum(tps_devis) as h_devis, sum(tps_devis * tx_gt) as cout_mo, 
sum(tps_devis * avt) as h_avt, sum(tps_reel) as h_real, sum(tps_reel * tx_gt) as cout_real , avg(tx_gt) as avg_tx
from phase_i where id_affaire = '$id_affaire' and id_of > 0 group by id_of, section");
while($row = mysql_fetch_array($res))
	{
	$tx=div($row["cout_mo"], $row["h_devis"]);
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("id_of", $row["id_of"]);
	$vf->add("section", $row["section"]);
	$vf->add("h_devis", $row["h_devis"]);
	$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
	$vf->add("cout_theo", $row2["cout_mo"]);
	$vf->add("avt", $row["h_devis"]>0?div($row["h_avt"], $row["h_devis"]):100);
	$vf->add("h_real", $row["h_real"]);
	$vf->add("cout_real", $row["cout_real"]);
	$vf->insert("affaire_recap_mo_0");
	}
}


//somme par of
function maj_affaire_recap1($id_affaire)
{
my_query("delete from affaire_recap_mo_1 where id_affaire = '$id_affaire'");
$res = my_query("select id_of, section, sum(tps_devis) as h_devis, sum(tps_devis * tx_gt) as cout_mo, 
sum(tps_devis * avt) as h_avt, sum(tps_reel) as h_real, sum(tps_reel * tx_gt) as cout_real, avg(tx_gt) as avg_tx
from phase_i where id_affaire = '$id_affaire' and id_of > 0 group by id_of");
while($row = mysql_fetch_array($res))
	{
	$tx=div($row["cout_mo"], $row["h_devis"]);
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("id_of", $row["id_of"]);
	$vf->add("h_devis", $row["h_devis"]);
	$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
	$vf->add("cout_theo", $row2["cout_mo"]);
	$vf->add("avt", $row["h_devis"]>0?div($row["h_avt"], $row["h_devis"]):100);
	$vf->add("h_real", $row["h_real"]);
	$vf->add("cout_real", $row["cout_real"]);
	$vf->insert("affaire_recap_mo_1");
	}
}


//Somme des heures groupé par section pour id_affaire
function maj_affaire_recap2($id_affaire)
{
my_query("delete from affaire_recap_mo_2 where id_affaire = '$id_affaire'");
$res = my_query("select id_affaire, section, sum(tps_devis) as h_devis, sum(tps_devis * tx_gt) as cout_mo, 
sum(tps_devis * avt) as h_avt,avg(avt) as avg_avt, sum(tps_reel) as h_real, sum(tps_reel * tx_gt) as cout_real , avg(tx_gt) as avg_tx
from phase_i where id_affaire = '$id_affaire' and (tps_reel>0 or tps_devis>0)group by section");
while($row = mysql_fetch_array($res))
	{
	$avt = div($row["h_avt"], $row["h_devis"]);
	if($row["h_devis"]==0)$avt=$row["avg_avt"];
	$tx=div($row["cout_mo"], $row["h_devis"]);
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("section", $row["section"]);
	$vf->add("h_devis", $row["h_devis"]);
	$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
	$vf->add("cout_theo", $row["cout_mo"]);
	$vf->add("avt", $avt);
	$vf->add("h_real", $row["h_real"]);
	$vf->add("cout_real", $row["cout_real"]);
	$vf->insert("affaire_recap_mo_2");
	}
}


//Somme des heures par affaire
function maj_affaire_recap3($id_affaire)
{
my_query("delete from affaire_recap_mo_3 where id_affaire = '$id_affaire'");
$res = my_query("select section, sum(h_devis) as h_devis, sum(tx_horaire * h_devis) as cout_mo, 
sum(h_devis * avt) as h_avt, sum(h_real) as h_real, sum(cout_real) as cout_real, avg(tx_horaire) as avg_tx
from affaire_recap_mo_2 
where id_affaire ='".$id_affaire."'");

$nb_ligne=mysql_num_rows($res);
if($nb_ligne>0)
	{
	while($row = mysql_fetch_array($res))
		{
		$tx=div($row["cout_mo"], $row["h_devis"]);
		$vf = new valid_form;
		$vf->add("id_affaire", $id_affaire);
		$vf->add("section", 0);
		$vf->add("h_devis", $row["h_devis"]);
		$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
		$vf->add("cout_theo", $row["cout_mo"]);
		$vf->add("avt", div($row["h_avt"], $row["h_devis"]));
		$vf->add("h_real", $row["h_real"]);
		$vf->add("cout_real", $row["cout_real"]);
		$vf->insert("affaire_recap_mo_3");
		
		$vf = new valid_form ;
		$vf->add("h_devis", $row["h_devis"]);
		$vf->add("h_mt_devis",$row["cout_mo"] );
		$vf->add("h_avt", div($row["h_avt"], $row["h_devis"]));
		$vf->add("h_real",$row["h_real"] );
		$vf->add("h_mt_real",$row["cout_real"] );
		$vf->update("affaire","where id = '$id_affaire'","",1);
		
		}
	}
	else
	{
	$vf = new valid_form ;
	$vf->add("h_devis", 0);
	$vf->add("h_mt_devis",0 );
	$vf->add("h_avt", 0);
	$vf->add("h_real",0 );
	$vf->add("h_mt_real",0);
	$vf->update("affaire","where id = '$id_affaire'","",1);
	
	}
}


//Somme des heures par section et par affaire avec la descendance
function maj_affaire_recap4($id_affaire)
{
my_query("delete from affaire_recap_mo_4 where id_affaire = '$id_affaire'");
$t=ma_descendance($id_affaire);
$t[]=$id_affaire;
$res = my_query("select section, sum(h_devis) as h_devis, sum(tx_horaire * h_devis) as cout_mo, 
sum(h_devis * avt) as h_avt, sum(h_real) as h_real, sum(cout_real) as cout_real, avg(tx_horaire) as avg_tx
from affaire_recap_mo_2 
where id_affaire in (".tabtosql($t).") 
group by section having h_devis+h_real>0");

while($row = mysql_fetch_array($res))
	{
	$tx=div($row["cout_mo"], $row["h_devis"]);

	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("section", $row["section"]);
	$vf->add("h_devis", $row["h_devis"]);
	$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
	$vf->add("cout_theo", $row["cout_mo"]);
	$vf->add("avt", div($row["h_avt"], $row["h_devis"]));
	$vf->add("h_real", $row["h_real"]);
	$vf->add("cout_real", $row["cout_real"]);
	$vf->insert("affaire_recap_mo_4");
	}
}


//Somme des heures par affaire avec la descendance
function maj_affaire_recap5($id_affaire)
{
my_query("delete from affaire_recap_mo_5 where id_affaire = '$id_affaire'");
$t=ma_descendance($id_affaire);
$t[]=$id_affaire;

$res0 = my_query("select sum(montant_cde)as montant_cde, sum(mt_facture)as mt_facture, sum(mt_facture_paye) as mt_facture_paye , sum(mt_facture_prev) as mt_facture_prev from affaire where id in (".tabtosql($t).")");
$row0 = mysql_fetch_array($res0);



$res = my_query("select sum(h_devis) as h_devis, sum(tx_horaire * h_devis) as cout_mo, 
sum(h_devis * avt) as h_avt, sum(h_real) as h_real, sum(cout_real) as cout_real, avg(tx_horaire) as avg_tx
from affaire_recap_mo_3
where id_affaire in (".tabtosql($t).")");
while($row = mysql_fetch_array($res))
	{
	$tx=div($row["cout_mo"], $row["h_devis"]);
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("section", 0);
	$vf->add("h_devis", $row["h_devis"]);
	$vf->add("tx_horaire", ($tx>0)?$tx:$row['avg_tx']);
	$vf->add("cout_theo", $row["cout_mo"]);
	$vf->add("avt", div($row["h_avt"], $row["h_devis"]));
	$vf->add("h_real", $row["h_real"]);
	$vf->add("cout_real", $row["cout_real"]);
	$vf->insert("affaire_recap_mo_5");
	
	$vf = new valid_form ;
	$vf->add("montant_cde_c",$row0["montant_cde"] );
	$vf->add("mt_facture_c", $row0["mt_facture"]);
	$vf->add("mt_facture_paye_c", $row0["mt_facture_paye"]);
	$vf->add("h_devis_c",$row["h_devis"] );
	$vf->add("h_mt_devis_c", $row["cout_mo"]);
	$vf->add("h_avt_c", div($row["h_avt"], $row["h_devis"]));
	$vf->add("h_real_c",$row["h_real"] );
	$vf->add("h_mt_real_c", $row["cout_real"]);
	$vf->add("mt_facture_prev_c", $row["mt_facture_prev"]);
	$vf->update("affaire","where id = '$id_affaire'","",1);
	}
}


////////////////////////////
/////////ACHATS/////////////
////////////////////////////


function maj_affaire_achat1($id_affaire)
{
my_query("delete from affaire_recap_achat_1 where id_affaire = '$id_affaire'");

//Somme des achats par of
$res2 = my_query("select id_of, sum(achat) as achat_devis, designation, sum(achat * avt) as achat_avt, sum(achat_real) as achat_real
from of_achat where id_affaire = '$id_affaire' and id_of > 0 group by id_of;");
while($row2 = mysql_fetch_array($res2))
	{
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("id_of", $row2["id_of"]);
	$vf->add("designation", $row["designation"]);
	$vf->add("achat_devis", $row2["achat_devis"]);
	$vf->add("achat_real", $row2["achat_real"]);
	$vf->add("avt", div($row2["achat_avt"], $row2["achat_devis"]));
	$vf->insert("affaire_recap_achat_1");
	}
}

function maj_affaire_achat2($id_affaire)
{
my_query("delete from affaire_recap_achat_2 where id_affaire = '$id_affaire'");

//Somme des achats par tache et par affaire
$res = my_query("select id_tache, designation, sum(achat) as achat_devis, sum(achat * avt) as achat_avt, sum(achat_real) as achat_real
from of_achat where id_affaire = '$id_affaire' and id_of > 0 group by id_tache;");

while($row = mysql_fetch_array($res))
	{
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("id_tache", $row["id_tache"]);
	$vf->add("designation", $row["designation"]);
	$vf->add("achat_devis", $row["achat_devis"]);
	$vf->add("achat_real", $row["achat_real"]);
	$vf->add("avt", div($row["achat_avt"], $row["achat_devis"]));
	$vf->insert("affaire_recap_achat_2");
	}
}


//Somme des achats par affaire
function maj_affaire_achat3($id_affaire)
{
my_query("delete from affaire_recap_achat_3 where id_affaire = '".$id_affaire."'");
$res3 = my_query("select id_tache, sum(achat_devis) as achat_devis, sum(achat_devis * avt) as achat_avt, sum(achat_real) as achat_real
from affaire_recap_achat_2 where id_affaire = '$id_affaire'
group by id_affaire;");

$nb_ligne=mysql_num_rows($res3);
if($nb_ligne>0)
	{
	while($row3 = mysql_fetch_array($res3))
		{
		$vf = new valid_form;
		$vf->add("id_affaire", $id_affaire);
		$vf->add("id_tache", 0);
		$vf->add("achat_devis", $row3["achat_devis"]);
		$vf->add("avt", div($row3["achat_avt"], $row3["achat_devis"]));
		$vf->add("achat_real", $row3["achat_real"]);
		if (($row3['achat_real']>0) or ($row3['achat_devis']>0))
			{
			$vf->insert("affaire_recap_achat_3");

			$vf = new valid_form ;
			$vf->add("achat_devis",$row3["achat_devis"]);
			$vf->add("achat_avt",div($row3["achat_avt"], $row3["achat_devis"]));
			$vf->add("achat_real",$row3["achat_real"]);
			$vf->update("affaire","where id = '$id_affaire'","",1);
			}
		}
	}
	else
	{
	$vf = new valid_form ;
	$vf->add("achat_devis",0);
	$vf->add("achat_avt",0);
	$vf->add("achat_real",0);
	$vf->update("affaire","where id = '$id_affaire'","",1);
	}
}


//Somme des achats par id_tache et par affaire avec la descendance
function maj_affaire_achat4 ($id_affaire)
{
my_query("delete from affaire_recap_achat_4 where id_affaire = '".$id_affaire."'");

$t=ma_descendance($id_affaire);
$t[]=$id_affaire;
$res = my_query("select designation, id_tache, sum(achat_devis) as achat_devis, sum(achat_devis * avt) as achat_avt, sum(achat_real) as achat_real
from affaire_recap_achat_2 where id_affaire in (".tabtosql($t).")
group by id_tache having (achat_devis+achat_real)>0;");
while($row = mysql_fetch_array($res))
	{
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("id_tache", $row['id_tache']);
	$vf->add("designation", $row["designation"]);
	$vf->add("achat_devis", $row["achat_devis"]);
	$vf->add("avt", div($row["achat_avt"], $row["achat_devis"]));
	$vf->add("achat_real", $row["achat_real"]);
	$vf->insert("affaire_recap_achat_4");
	}
}

//Somme des achats pour une affaire et sa descendance
function maj_affaire_achat5 ($id_affaire)
{
my_query("delete from affaire_recap_achat_5 where id_affaire = '".$id_affaire."'");

$t=ma_descendance($id_affaire);
$t[]=$id_affaire;
$res = my_query("select sum(achat_devis) as achat_devis, sum(achat_devis * avt) as achat_avt, sum(achat_real) as achat_real
from affaire_recap_achat_3 where id_affaire in (".tabtosql($t).");");
while($row = mysql_fetch_array($res))
	{
	$vf = new valid_form;
	$vf->add("id_affaire", $id_affaire);
	$vf->add("achat_devis", $row["achat_devis"]);
	$vf->add("avt", div($row["achat_avt"], $row["achat_devis"]));
	$vf->add("achat_real", $row["achat_real"]);
	$vf->insert("affaire_recap_achat_5");
	
	$vf = new valid_form ;
	$vf->add("achat_devis_c",$row["achat_devis"]);
	$vf->add("achat_avt_c",div($row["achat_avt"], $row["achat_devis"]));
	$vf->add("achat_real_c",$row["achat_real"]);
	$vf->update("affaire","where id = '$id_affaire'","",1);
	
	$vf = new valid_form ;
	$vf->add("marge_theo","montant_cde / (achat_devis + h_mt_devis)",0,1);
	$vf->add("marge_real","(montant_cde * ((h_mt_real*h_avt+achat_real*achat_avt)/(h_mt_real+achat_real))/100) / (h_mt_real + achat_real)",0,1);
	$vf->add("marge_theo_c"," montant_cde_c / (achat_devis_c + h_mt_devis_c)",0,1);
	$vf->add("marge_real_c"," (montant_cde_c * ((h_mt_real_c*h_avt_c+achat_real_c*achat_avt_c)/(h_mt_real_c+achat_real_c))/100) / (h_mt_real_c + achat_real_c)",0,1);
	$vf->update("affaire","where id = '$id_affaire'","",1);
	}
}



function maj_of_achat_real($id_affaire = '')
{
$t='';
if($id_affaire <> '')$req_p = " and id_affaire in ($id_affaire)";
$res=my_query("select * from of_achat where 1 $req_p ");
while ($r=mysql_fetch_array($res))
	{
	$t[$r["id_of"]][$r["id_tache"]]["id"]=$r["id"];
	$t[$r["id_of"]][$r["id_tache"]]["achat"]=$r["achat"];
	$t[$r["id_of"]][$r["id_tache"]]["achat_real"]=$r["achat_real"];
	$t[$r["id_of"]][$r["id_tache"]]["achat_real_new"]=0;
	}
	
if($id_affaire <> '')$req_p = " and al.id_affaire in ($id_affaire)";

$res = my_query("select al.id_of , al.id_affaire , ac.id_devis as id_tache, ac.nom , sum(m_ht) as achat_real_new from achat_ligne as al left join article_cat as ac on al.cat = ac.id where etat in (6,7,8,9,10) $req_p group by al.id_of , id_tache;");
while ($r=mysql_fetch_array($res))
	{
	if($r["id_tache"]==0)$r["id_tache"]=510;
	$t[$r["id_of"]][$r["id_tache"]]["id_affaire"]=$r["id_affaire"];
	$t[$r["id_of"]][$r["id_tache"]]["designation"]=$r["nom"];
	$t[$r["id_of"]][$r["id_tache"]]["achat_real_new"]=$r["achat_real_new"];
	}

/*
$designation=nombre_de("select nom from devis_tache where id=900");
if($id_affaire <> '')$req_p = " and fdl.id_affaire in ($id_affaire)";
$res = my_query("select fdl.id_affaire, fdl.id_of , sum(fdl.debit) as achat_real_new from fiche_dep_liste fdl left join fiche_dep as fd on fdl.id_fiche_dep = fd.id where fd.etat in (7,8,9,10) $req_p group by fdl.id_of ;");
while ($r=mysql_fetch_array($res))
	{
	$t[$r["id_of"]][900]["id_affaire"]=$r["id_affaire"];
	$t[$r["id_of"]][900]["designation"]=$designation;
	$t[$r["id_of"]][900]["achat_real_new"]+=$r["achat_real_new"];
	}

if($id_affaire <> '')$req_p = " and ad.id_affaire in ($id_affaire)";
$res = my_query("select ad.id_affaire, ad.id_of , sum(ad.deplacement) as achat_real_new from affaire_deplacement ad where 1 $req_p group by ad.id_of ;");
while ($r=mysql_fetch_array($res))
	{
	$t[$r["id_of"]][900]["id_affaire"]=$r["id_affaire"];
	$t[$r["id_of"]][900]["designation"]=$designation;
	$t[$r["id_of"]][900]["achat_real_new"]+=$r["achat_real_new"];
	}
	*/
foreach($t as $id_of => $t_tache)
	foreach($t_tache as $id_tache => $r)
		{
		if($r["achat"]==0 and $r["achat_real_new"]==0 and $r["id"]>0) 
			{
			//delete
			del_visit(__FILE__,__LINE__,DL_1,"of_achat","where id = ".$r["id"].";");
			}
			elseif((!($r["id"]>0)) and $r["achat_real_new"] > 0)
			{
			//insert
			$vf = new valid_form;
			$vf->add("id_of", $id_of);
			$vf->add("id_affaire", $r["id_affaire"]);
			$vf->add("id_tache", $id_tache);
			$vf->add("designation", $r["designation"]);
			$vf->add("marge", 1);
			$vf->add("achat_real", $r["achat_real_new"]);
			$vf->add("maj", date("Y-m-d"));
			$vf->insert("of_achat");

			}
			
			elseif(($r["achat_real"] <> $r["achat_real_new"]) and $r["id"]>0)
			{
			//update
			my_query("update of_achat set achat_real = ".($r["achat_real_new"]+0)." where id = ".$r["id"]);
			}
		}
}

//////////////////////////////////////////////////////


function phase_avant($id_of,$numero,$avant=1)
{
if($avant==1)
	{
	$op = '<';
	$tri = 'desc';
	}
	else
	{
	$op = '>';
	$tri = 'asc';
	}
	
$res = my_query("select * from phase_i where numero ".$op." ".$numero." and id_of=".$id_of." order by numero ".$tri." limit 1;");
$nb_ligne=mysql_num_rows($res);
if($nb_ligne>0)
	{
	$row = mysql_fetch_array($res);
	return $row;
	}
	else return 0;
}


function phase_date_prev($of, $date, $numero=0)
{
	$phase_suivante=phase_avant($of,$numero,0);
	if (is_array($phase_suivante))
		{
		$d_fin=calcul_date_fin($date,$phase_suivante['cycle_prev']);
		
		$vf = new valid_form ;
		$vf->add("d_p_deb", $date);
		$vf->add("d_p_fin", $d_fin);
		$vf->update("phase_i","where id=".$phase_suivante['id'],"",1);
		$vf->log(__FILE__,__LINE__,DL_1);
		phase_date_prev($of,calcul_date_fin($d_fin,0),$phase_suivante['numero']);
		}
}

function phase_date_prev_fc($of, $date, $numero=10000)
{
	$phase_suivante=phase_avant($of,$numero);
	if (is_array($phase_suivante))
		{
		$d_fin=calcul_date_deb($date,$phase_suivante['cycle_prev']);
		
		$vf = new valid_form ;
		$vf->add("d_p_deb", $d_fin);
		$vf->add("d_p_fin", $date);
		$vf->update("phase_i","where id=".$phase_suivante['id'],"",1);
		$vf->log(__FILE__,__LINE__,DL_1);
		phase_date_prev_fc($of,calcul_date_deb($d_fin,0),$phase_suivante['numero']);
		}
}


function calcul_date_fin($date,$cycle,$jok=array(1,2,3,4,5))
{
if($cycle>0)
	while ($cycle!=0)
		{
		$date=strftime("%Y-%m-%d",datotimestamp($date, 1));
		if (in_array(strftime("%u",datotimestamp($date)),$jok) )
			{
			if(!(is_ferie(datodf($date))))$cycle--;
			}

		}
return $date;
}

function calcul_date_deb($date,$cycle,$jok=array(1,2,3,4,5))
{
if($cycle>0)
	while ($cycle!=0)
		{
		$date=strftime("%Y-%m-%d",datotimestamp($date, -1));
		if (in_array(strftime("%u",datotimestamp($date)),$jok) )
			{
			if(!(is_ferie(datodf($date))))$cycle--;
			}

		}
return $date;
}

function phase_en_cour($id_of)
{
$res=my_query("select * from phase_i where id_of=".$id_of." order by numero asc");
while ($row=mysql_fetch_array($res))
	{
	$x=$row;
	if (($row['statut']==2)or($row['statut']==5))continue;
	return $row;
	}
	return $x;
}

	
function change_statut_simple($id_of,$avancer = 1,$p_next=1,$date = '',$ri=0, $ra=0)
{

$statut=0;
if($date == '')$date=date('Y-m-d');
	$phase=phase_en_cour($id_of);
	if(!is_array($phase))return 0;
	$n_bon=$phase['id'];
	
	if ($avancer==1)
		{
		if($phase['statut']==0){$statut=1;}
		else if($phase['statut']==1){$statut=3;}
		else if($phase['statut']==3){$statut=6;}
		else if($phase['statut']==4){$statut=8;}
		}
		else
		{
		if($phase['statut']==0)
			{
			$statut=4;
			$pa=phase_avant($id_of,$phase['numero']);
			if((!is_array($pa))or(($pa['statut']==5)))return ;
			$n_bon=$pa['id'];
			}
		else if($phase['statut']==1){$statut=2;}
		else if($phase['statut']==2){$statut=4;}
		else if($phase['statut']==3){$statut=6;}
		else if($phase['statut']==4){$statut=8;}
		}
if($statut>0)return change_statut($n_bon, $statut, $date, $p_next, $ri, $ra);
}


function change_statut($n_bon, $statut, $date, $p_next, $ri, $ra)
{
global $j_statut_new,$j_statut,$j_statut_corr;

//phase_i
$vf = new valid_form ;

//of
$vf2 = new valid_form ;

$req=my_query("select * from phase_i where id=".$n_bon);
$row=mysql_fetch_array($req);

if ($row['statut']!=$j_statut_corr[$statut])
	{
	$erreur="<tr class=cel2 align=center>
			<td>".format_0($n_bon,8)."</td>
			<td>Le bon ".format_0($n_bon,8)." est en statut ".$j_statut[$row['statut']]."</td>
			<td><img src='images/Warning.png'></td>
			</tr>";
	return $erreur;
	}
	
//Même statut
if ($j_statut_new[$statut]==$row['statut'])
	{
	$erreur="<tr class=cel2 align=center>
			<td>".format_0($n_bon,8)."</td>
			<td>Le bon ".format_0($n_bon,8)." est déjà en statut ".$j_statut[$j_statut_new[$statut]]."</td>
			<td><img src='images/Warning.png'></td>
			</tr>";
	return $erreur;
	}
	
switch ($statut)
	{
		
	//disp -> distr
	case 1:
		//date de fin > date
		if (datotimestamp($date)>datotimestamp(date("Y-m-d")))
			{
			$erreur="<tr class=cel2 align=center>
					<td>".format_0($n_bon,8)."</td>
					<td>La date indiquée est supérieur à la date d'aujourd'hui.</td>
					<td><img src='images/Warning.png'></td>
					</tr>";
			return $erreur;
			}
	
		$p_prec=phase_avant($row['id_of'], $row['numero']);
		if (!(is_array($p_prec))) //recherche phase precedente
			{
			// si facon complete, on part de la fin
			if(nombre_de("select id_fab from of where id = ".$row['id_of'])==1)
				{
				phase_date_prev_fc($row['id_of'], nombre_de("select d_besoin  from of where id = ".$row['id_of']));
				}
				else
				{
				phase_date_prev($row['id_of'], $date);
				}
			$vf->add("statut",$j_statut_new[$statut]);
			$vf->add("date_deb",$date);

			$vf2->add("d_lancement",$date);
			$vf2->add("etat",1);
			}
			else
			{
			if (($p_prec['statut']==2)or($p_prec['statut']==5)) //Statut de la phase precedente en "terminé"
				{
				if (datotimestamp($date)<datotimestamp($p_prec['d_fin_sap'])) //date sap inferieur a date début
					{
					$erreur="<tr class=cel2 align=center>
								<td>".format_0($n_bon,8)."</td>
								<td>La date de début est inférieure à la date de fin de la phase précedente.</td>
								<td><img src='images/Warning.png'></td>
							</tr>";
					return $erreur;
					}
			
				$vf->add("date_deb",$date);
				$vf->add("statut",$j_statut_new[$statut]);
				}
				else
				{
				$erreur="<tr class=cel2 align=center>
							<td>".format_0($n_bon,8)."</td>
							<td>La phase precedente ".format_0($p_prec['numero'],4)." est en statut ".$j_statut[$p_prec['statut']]." (n° bon : ".format_0($p_prec['id'],8).")</td>
							<td><img src='images/Warning.png'></td>
						</tr>";
				return $erreur;
				}
			}
		break;

	//distr -> disp
	case 2:
		$p_prec=phase_avant($row['id_of'], $row['numero']);
		if (!(is_array($p_prec)))//pas de phase précedente
			{
			$vf2->add("etat",0);
			$vf2->add("d_lancement",'0000-00-00');
			
			$vf3 = new valid_form ;
			$vf3->add("d_p_deb", "0000-00-00");
			$vf3->add("d_p_fin", "0000-00-00");
			$vf3->update("phase_i","where id_of=".$row['id_of'],"",1);
			$vf3->log(__FILE__,__LINE__,DL_1);
			}
			
		$vf->add("date_deb",'0000-00-00');
		//$vf->add("cycle_annule",0);
		$vf->add("statut",$j_statut_new[$statut]);
		break;

	//distr -> terc
	case 3:
		$p_prec=phase_avant($row['id_of'], $row['numero']);
		//quantite (pour qte etat ok)
		$qte_rebut=nombre_de("select sum(etat_ri+etat_ra) from phase_i where id_of=".$row['id_of']);

		//calcul cycle reel
		$cycle_reel=count(interdate($row['date_deb'],$date,$jok=array(1,2,3,4,5),0))-1;//-$row['cycle_annule']

		//cumul ecart
		$cumul_ecart=0;
		if (is_array($p_prec))$cumul_ecart=$p_prec['cumul_ecart'];
		$cumul_ecart+=$row['cycle_prev']-$cycle_reel;

		//date de fin > date
		/*if (datotimestamp($date)>datotimestamp(date("Y-m-d")))
			{
			$erreur="<tr class=cel2 align=center>
					<td>".format_0($n_bon,8)."</td>
					<td>La date indiquée est supérieure à la date d'aujourd'hui.</td>
					<td><img src='images/Warning.png'></td>
					</tr>";
			return $erreur;
			}
	*/
		
		//date de fin > date de debut
		if (datotimestamp($date)<datotimestamp($row['date_deb']))
			{
			$erreur="<tr class=cel2 align=center>
					<td>".format_0($n_bon,8)."</td>
					<td>La date de fin est inférieure à la date de début.</td>
					<td><img src='images/Warning.png'></td>
					</tr>";
			return $erreur;
			}
			
		//quantite
		if (($ri+$ra)>($row['quantite']-$qte_rebut))
			{
			$erreur="<tr class=cel2 align=center>
					<td>".format_0($n_bon,8)."</td>
					<td>Quantité rebuts non valide.</td>
					<td><img src='images/Warning.png'></td>
					</tr>";
			return $erreur;
			}
		
		$quantite=$row['quantite']-($ri+$ra+$qte_rebut);
		
		//facturation a 1..
		if($row['is_facturation']>0)
			{
			// on paye les retouche quand elles sont faite
			$vf_f = new valid_form ;
			$vf_f->add("facturation", 1);
			$vf_f->add("d_fin_sap_facture", $date);
			$vf_f->update("phase_i","where id_of = ".$row['id_of'] ,"",1);
			$vf_f->log(__FILE__,__LINE__,DL_1);

			//my_query("update phase_i set facturation=1 , d_fin_sap_facture = '$date' where id_of = ".$row['id_of'] );
			//and numero <=  ".$row['numero']
			
			$vf2->add("quantite_ok", $quantite);
			$vf2->add("quantite_rebut", $ri+$ra+$qte_rebut);
			$vf2->add("d_fin_sap",$date);
			$vf2->add("etat",2);
			
			}
		
		if(($row['facturation']==0)and($p_prec['facturation']>0))
			{
			$vf->add("facturation",1);
			}

		
		// recalcul pour ri ra
		$vf3 = new valid_form ;
		$vf3->add("mt_st", "((mt_st_ori * ".$quantite.") / quantite)",0,1);
		$vf3->add("tps_machine", "((tps_machine_ori * ".$quantite.") / quantite)",0,1);
		$vf3->add("tps_obj", "((tps_obj_ori * $quantite) / quantite)",0,1);
		$vf3->add("tps_devis", "((tps_devis_ori * $quantite) / quantite)",0,1);
		$vf3->update("phase_i","where id_of = ".$row['id_of']." and numero  > ".$row["numero"],"",1);
		$vf3->log(__FILE__,__LINE__,DL_1);

		$vf->add("tps_devis",div($row['tps_devis_ori']*$quantite,$row['quantite']));
		$vf->add("tps_obj",div($row['tps_obj_ori']*$quantite,$row['quantite']));
		$vf->add("mt_st",div($row['mt_st']*$quantite,$row['quantite']));
		$vf->add("tps_machine",div($row['tps_machine']*$quantite,$row['quantite']));
		$vf->add("prix_vente",$row['prix_vente']);

		$vf->add("d_fin_sap",$date);
		//$vf->add("d_fin_sap_facture",$date);
		$vf->add("etat_ri",$ri);
		$vf->add("etat_ra",$ra);
		$vf->add("etat_ok",$quantite);
		$vf->add("cycle_reel",$cycle_reel);
		$vf->add("cumul_ecart",$cumul_ecart);
		$vf->add("avt",'100');
		$vf->add("statut",$j_statut_new[$statut]);
		$vf->add("clos",1);
		break;

	//terc -> distr
	case 4:
		//Cas des phases supérieur en distr
		$p_suiv=phase_avant($row['id_of'], $row['numero'],0);
		$affaire_etat=nombre_de("select etat from affaire where id =  '".$row['id_affaire']."'");
		if ((((is_array($p_suiv))and($p_suiv['statut']==0))or(!(is_array($p_suiv))))and($row['id_affaire']>0)and $affaire_etat<4)
			{
			$vf->add("d_fin_sap",'0000-00-00');
			$vf->add("etat_ri",0);
			$vf->add("etat_ra",0);
			$vf->add("etat_ok",0);
			$vf->add("cycle_reel",0);
			$vf->add("cumul_ecart",0);
			$vf->add("statut",$j_statut_new[$statut]);
			$vf->add("avt",'0');
			$vf->add("facturation",0);
			$vf->add("clos",0);

			$vf2->add("quantite_ok", 0);
			$vf2->add("quantite_rebut", 0);
			$vf2->add("d_fin_sap",'0000-00-00');
			$vf2->add("etat",1);
			}
			else
			{
			$erreur="<tr class=cel2 align=center>
			<td>".format_0($n_bon,8)."</td>
			<td>La phase n° ".$p_suiv['numero']." du bon ".format_0($p_suiv['id'],8)." n'est pas disp.</td>
			<td><img src='images/Warning.png'></td>
			</tr>";
			return $erreur;
			}
		break;

	//defaut change statut
	default:
		/*
		if ($row['statut']>2)
			{
			$cycle_annule=nombre_de("select date_sap from phase_statut_histo where id_phase_i=".$n_bon." order by id desc limit 1");
			$nb=count(interdate($cycle_annule,$date,$jok=array(1,2,3,4,5),0))-1;
			$vf->add("cycle_annule",$row['cycle_annule'] + $nb);
			}
		*/
		$vf->add("statut",$j_statut_new[$statut]);
		break;

	}
	$recap.="<tr class=cel1 align=center>
				<td>".format_0($n_bon,8)."</td>
				<td>Le bon n° ".format_0($n_bon,8)." de l'of ".$row['id_of']." est bien passé de ".$j_statut[$row['statut']]." à ".$j_statut[$j_statut_new[$statut]].".</td>
				<td><img src='images/tick.png'></td>
				</tr>";


	$vf->update("phase_i"," where id = ".$n_bon);
	$vf->log(__FILE__,__LINE__,DL_1);
	$vf2->update("of"," where id = ".$row['id_of']);
	$vf2->log(__FILE__,__LINE__,DL_1);


	$vf3 = new valid_form;

	$vf3->add("id_phase_i", $n_bon);
	$vf3->add("id_of", $row['id_of']);
	$vf3->add("date", date("Y-m-d"));
	$vf3->add("date_sap", $date);
	$vf3->add("statut", $statut);
	$vf3->add("ri", $ri);
	$vf3->add("ra", $ra);
	$vf3->add("p_next", $p_next);
	$vf3->add("requete", $vf->req);
	$vf3->insert("phase_statut_histo",'',1);
	$vf3->log(__FILE__,__LINE__,DL_1);

	//Distribution de la phase suivante (dans le cas Termine)
	if (($p_next==1)and($statut==3))
		{
		$p_suiv=phase_avant($row['id_of'], $row['numero'],0);
		if (is_array($p_suiv))change_statut($p_suiv['id'],1, $date, 0,0,0);
		}
	return $erreur.$recap;
}






function valid_of($id_of,$id_gamme)
{
$quantite=nombre_de("select quantite from of where id = '".$id_of."'");
$id_affaire = nombre_de("select id_affaire from of where id = '".$id_of."'");

	$sql="SELECT * FROM gamme where id = '".$id_gamme."'";
	$res=my_query($sql);
	$row = mysql_fetch_array($res);
		
	$vf = new valid_form ;
	$vf->add("id_avion", $row["id_avion"]);
	$vf->add("id_famille", $row["id_famille"]);
	$vf->add("id_cat", $row["id_cat"]);
	$vf->add("id_fab", $row["id_fab"]);
	$vf->add("id_secteur", $row["id_secteur"]);
	$vf->add("id_gt_delai", $row["id_gt_delai"]);
	$vf->add("ref", $row["ref"]);
	$vf->add("indice", $row["indice"]);
	$vf->add("valid", 1);
	$vf->update("of","where id = ".$id_of);
	$vf->log(__FILE__,__LINE__,DL_1);

	$sql3="SELECT * FROM phase where id_gamme = '".$id_gamme."' order by numero asc";
	$res3=my_query($sql3);
	while($row3 = mysql_fetch_array($res3))
		{
		$vf3 = new valid_form ;
		$vf3->add("id_phase", $row3["id"]);
		$vf3->add("id_of", $id_of);
		$vf3->add("id_affaire", $id_affaire);
		$vf3->add("id_gamme", $row3["id_gamme"]);
		$vf3->add("id_gt", $row3["id_gt"]);
		$vf3->add("tx_gt", $row3["gt_tx"]);
		$vf3->add("gt_code", $row3["gt_code"]);
		$vf3->add("section", $row3["section"]);
		$vf3->add("numero", $row3["numero"]);
		$vf3->add("tps_devis", (($quantite*$row3["tps_devis_u"])+$row3['tps_devis_p']));
		$vf3->add("tps_obj", (($quantite*$row3["tps_obj_u"])+$row3['tps_obj_p']));
		$vf3->add("tps_devis_ori", (($quantite*$row3["tps_devis_u"])+$row3['tps_devis_p']));
		$vf3->add("tps_obj_ori", (($quantite*$row3["tps_obj_u"])+$row3['tps_obj_p']));
		$vf3->add("prix_vente", $row3["prix_vente"]);
		$vf3->add("quantite", $quantite);
		$vf3->add("designation", $row3["designation"]);
		$vf3->add("notice", $row3["notice"]);
		$vf3->add("commentaire", $row3["commentaire"]);
		$vf3->add("cycle_prev", $row3["cycle"]);
		$vf3->add("id_machine", $row3["id_machine"]);
		$vf3->add("tps_machine", $row3["tps_machine"]*$quantite);
		$vf3->add("mt_st", $row3["mt_st"]*$quantite);
		$vf3->add("code_barre", $row3["code_barre"]);
		$vf3->add("code_barre_decoup", $row3["code_barre_decoup"]);
		$vf3->add("is_facturation", $row3["is_facturation"]);
		$vf3->add("is_realisation", $row3["is_realisation"]);
		$vf3->add("is_livraison", $row3["is_livraison"]);
		$vf3->insert("phase_i");
		$vf3->log(__FILE__,__LINE__,DL_1);
		}

//oftopointeuse($id_of);
}


function unvalid_of($id)
{
del_visit(__FILE__,__LINE__,DL_1,"phase_i","where id_of = '$id'");
my_query("UPDATE `of` SET `valid` = '0' , lu = 0  WHERE `id` = '$id' LIMIT 1 ");
}

function supprime_of($id)
{
del_visit(__FILE__,__LINE__,DL_1,"phase_i","where id_of = '$id'");
del_visit(__FILE__,__LINE__,DL_1,"of","where id = '$id'");
del_visit(__FILE__,__LINE__,DL_1,"of_achat","where id_of = '$id'");
}



function change_of_en_cour($change = 1)  // 1 : suivant 2 : precedent
{
$req=$_SESSION["of_sql"];
if($req=='')$req='select id from of order by id desc';
$res=my_query($req);
while($row=mysql_fetch_array($res))
	{
	$succ=$row["id"];
	if($fin==1)break;
	if($row["id"] == $_SESSION["of_en_cour"])
		{
		$fin=1;
		if($prec=="")$prec=$row["id"];
		}
		else
		{
		$prec=$row["id"];
		}
	}
if($change==1)
	{
	$_SESSION["of_en_cour"]=$succ;
	}
	else
	{
	$_SESSION["of_en_cour"]=$prec;
	}
}

function cloture_of($of,$date='')
{
	if($date=='')$date = date('Y-m-d');
	$res=my_query('select * from phase_i where id_of='.$of.' order by numero asc');
	while ($row = mysql_fetch_array($res))
		{
		$id=$row['id'];
		$cycle_reel=0;
		if(($row['date_deb']<>'0000-00-00'))
			{
			if($row['d_fin_sap']=='0000-00-00')$row['d_fin_sap']=$date;
			$cycle_reel=count(interdate($row['date_deb'],$row['d_fin_sap'],$jok=array(1,2,3,4,5),0))-1;//-$row['cycle_annule']
			}
		$cumul_ecart+=$row['cycle_prev']-$cycle_reel;
		$rebut+=$row['etat_ri']+$row['etat_ra'];
		$qte_ok=$row['quantite']-$rebut;
		$sql="update phase_i
				set cumul_ecart=".$cumul_ecart.",
				cycle_reel=".$cycle_reel.",
				clos=1,
				avt=100,
				statut=2,
				etat_ok='$qte_ok',
				mt_st=((mt_st_ori * ".$qte_ok.") / quantite),
				tps_machine=((tps_machine_ori * ".$qte_ok.") / quantite),
				tps_obj = ((tps_obj_ori * $qte_ok) / quantite) ,
				tps_devis = ((tps_devis_ori * $qte_ok) / quantite),
				date_deb=if(date_deb='0000-00-00' ,'".$date."',date_deb),
				d_fin_sap=if(d_fin_sap='0000-00-00' ,'".$date."',d_fin_sap),
				facturation = 1,
				d_fin_sap_facture = '".$date."',
				maj_avt = '".date("Y-m-d")."'
			where id=".$row['id']."
			and statut <> 5";
		my_query($sql);
		
		log2(__FILE__,__LINE__,DL_1,$sql);
			
		}
	my_query("update of set etat=2, quantite_ok='".$qte_ok."', quantite_rebut='".$rebut."', d_fin_sap=if(d_fin_sap='0000-00-00' ,'".$date."',d_fin_sap) where id= $of ");
	if($id>0)my_query("update phase_i set facturation=1 where id=".$id);
	my_query("update of_achat set avt = 100 where id_of = $of ;");
}
 
//Cloturation d'un of
function of_verif_cloture($affaire,$descendance=0)
{
	$tab[]=$affaire;
	if ($descendance==1){$tab=ma_descendance($affaire,$tab);}

	$sql="select of.id , sum(pi.clos) as tot1, count(*) as tot2 from of left join phase_i pi on pi.id_of = of.id
	where of.etat < 2 and of.id_affaire in (".tabtosql($tab).") group by of.id having tot1 = tot2 and tot1 > 0";
	$res=my_query($sql);
	while($l=mysql_fetch_array($res))
	{
		cloture_of($l["id"]);
	}

	$sql="select of.id , sum(pi.clos) as tot1, count(*) as tot2 from of left join phase_i pi on pi.id_of = of.id
	where of.etat = 2 and of.id_affaire in (".tabtosql($tab).") group by of.id having tot1 <> tot2 and tot2 > 0";
	$res=my_query($sql);
	while($l=mysql_fetch_array($res))
		{
		$sql="update of set etat=1, d_fin_sap='0000-00-00' where id=".$l["id"];
		my_query($sql);
		log2(__FILE__,__LINE__,DL_1,$sql);
		}
}


function cloture_pi($id)
{
$sql="update phase_i set clos=1, avt=100, statut=2, etat_ok=quantite, date_deb=if(date_deb='0000-00-00' ,'".date("Y-m-d")."',date_deb), d_fin_sap=if(d_fin_sap='0000-00-00' ,'".date("Y-m-d")."',d_fin_sap), maj_avt = '".date("Y-m-d")."' where id=".$id.' ';
my_query($sql);
log2(__FILE__,__LINE__,DL_1,$sql);
}


function cloture_affaire($id)
{
$res=my_query("select id from of where id_affaire = $id and etat < 2");
while ($row = mysql_fetch_array($res))
  {
  cloture_of($row[0]);
  }
my_query("update affaire set etat=4, d_soldee=curdate() where id=".$id);
}

function fusionne_affaire($new,$old)
{
my_query("update achat_ligne set id_affaire = $new where id_affaire = $old");
my_query("update achat_regle set id_affaire = $new where id_affaire = $old");
my_query("update affaire_avenant set id_affaire = $new where id_affaire = $old");
my_query("update affaire_deplacement set id_affaire = $new where id_affaire = $old");
my_query("update affaire_etatc set id_affaire = $new where id_affaire = $old");
my_query("update affaire_etatc_cac set id_affaire = $new where id_affaire = $old");
my_query("update affaire_etatc_pointage set id_affaire = $new where id_affaire = $old");
my_query("update affaire_etatc_pointage_cac set id_affaire = $new where id_affaire = $old");
my_query("update affaire_prev set id_affaire = $new where id_affaire = $old");
my_query("update affaire_situation set id_affaire = $new where id_affaire = $old");
my_query("update article set id_affaire = $new where id_affaire = $old");
//my_query("update article_fam set id_affaire = $new where id_affaire = $old");
my_query("update diff_docs set id_affaire = $new where id_affaire = $old");
my_query("update dtnc set id_affaire = $new where id_affaire = $old");
my_query("update facture_ligne set id_affaire = $new where id_affaire = $old");
my_query("update fiche_bl set id_affaire = $new where id_affaire = $old");
my_query("update fiche_blcc set id_affaire = $new where id_affaire = $old");
my_query("update fiche_dep_liste set id_affaire = $new where id_affaire = $old");
my_query("update indus_rpa set id_affaire = $new where id_affaire = $old");
my_query("update of set id_affaire = $new where id_affaire = $old");
my_query("update of_achat set id_affaire = $new where id_affaire = $old");
my_query("update phase_i set id_affaire = $new where id_affaire = $old");
my_query("update piece set id_affaire = $new where id_affaire = $old");
my_query("update planif_chantier set id_affaire = $new where id_affaire = $old");
my_query("update pointage set id_affaire = $new where id_affaire = $old");
my_query("update stock_histo set id_affaire = $new where id_affaire = $old");
my_query("update equipement set affaire = $new where affaire = $old");
my_query("delete from affaire where id = $old");


}

function deplace_of($id_of,$new,$old)
{
my_query("update achat_ligne set id_affaire = $new where id_of = $id_of and id_affaire = $old");
my_query("update of set id_affaire = $new where id = $id_of and id_affaire = $old");
my_query("update of_achat set id_affaire = $new where id_of = $id_of and id_affaire = $old");
my_query("update phase_i set id_affaire = $new where id_of = $id_of and id_affaire = $old");
my_query("update pointage set id_affaire = $new where id_of = $id_of and id_affaire = $old");
my_query("update stock_histo set id_affaire = $new where id_of = $id_of and id_affaire = $old");

}


function oftopointeuse($id_of)
{

global $lnk2,$j_net_n;

$i=0;
$res1=my_query("select pi.id , pi.id_of , pi.id_gt , pi.id_affaire , of.msn , of.numero_client , pi.numero from phase_i pi left join of on pi.id_of = of.id where of.id = '$id_of' and pi.clos = '0'"); 

$nb_ligne=mysql_num_rows($res1);
if(!($nb_ligne>0))return 0;
	
while ($row1=mysql_fetch_array($res1))
{
if($i >0)$sql .= ",";
$sql .= "(".$row1["id"].", ".$row1["id_of"].", ".$row1["id_gt"].",".$row1["id_affaire"].",'".$row1["msn"]."','".$row1["numero_client"]."','".$row1["numero"]."')";
$i++;
}

$res=my_query("select * from pointeuse_".$j_net_n.".info where actif > 0;");
while ($row=mysql_fetch_array($res))
	{
	if ($lnk2 = mysql_connect($row["ip"],"root","sqljallais"))
		{
		mysql_select_db("pointeuse",$lnk2);
		mysql_query("insert ignore into phase_i VALUES $sql ;",$lnk2);
		
		//echo "ee".$sql2.$row["ip"];
		}
	}

}

function maj_mt_marche($id_affaire)
{

//echo "ee";
$sql = "SELECT if(d_cde<>'0000-00-00',year(d_cde),year(d_facture))as annee,if(d_cde<>'0000-00-00',month(d_cde),month(d_facture))as mois,sum(mt)as total from affaire_prev where id_affaire = '$id_affaire' group by annee , mois";
//echo $sql;
$res=my_query($sql);
while($l=mysql_fetch_array($res))
	{
	$t[$l['annee'].'-'.format_0($l['mois'],2)]=$l['total'];
	}
//print_r($t);
$res=my_query("SELECT id,year(date)as annee,month(date)as mois,sum(mt_ht)as total from affaire_avenant where id_affaire = '$id_affaire' group by annee , mois");
while($l=mysql_fetch_array($res))
	{
	$t2[$l['annee'].'-'.format_0($l['mois'],2)]=$l['total'];
	$t_id[$l['annee'].'-'.format_0($l['mois'],2)]=$l['id'];
	}
	
	
if(is_array($t2))foreach($t2 as $p=>$mt)
	{
	if(!($t[$p]<>0))
		{
		del_visit(__FILE__,__LINE__,DL_1,"affaire_avenant","where  id = ".$t_id[$p]."");
		}
		
	}
	


if(is_array($t))foreach($t as $p=>$mt)
	{
	if($t2[$p]<>$mt)
		{
		$vf = new valid_form ;
		$vf->add("mt_ht", $mt);

		if($t_id[$p] >0)
			{
			$vf->update("affaire_avenant","where id = ".$t_id[$p],"",1);
			}
			else
			{
			$vf->add("date", $p.'-01');
			$vf->add("type", 2);
			$vf->add("des", "Calcul suivant Carnet de commande");
			$vf->add("id_affaire", $id_affaire);
			$vf->insert("affaire_avenant");
			}
		
		}
	}
	

$id_one=nombre_de("select id from affaire_avenant where id_affaire = '$id_affaire' and type=1")+0;

$id=nombre_de("select id from affaire_avenant where id_affaire = '$id_affaire' order by date asc limit 1")+0;

if($id<>$id_one)
	{
	my_query("update affaire_avenant set type = 1 where id = ".$id.""); 
	if($id_one>0)my_query("update affaire_avenant set type = 2 where id = ".$id_one.""); 
	}


}


function total_facture($id,$sum=0)
{
$r=my_query("select * from facture where id = $id");
$l=mysql_fetch_array($r);
if($l["type"] == 2){$mt = $l["mt_ht"]*-1;}else{$mt = $l["mt_ht"];}
$sum+=$mt;
if( $l["id_pere"]>0)
	{
	return total_facture($l["id_pere"],$sum);
	}
	else
	{
	return $sum;
	}
}

function calcul_en_cour($annee,$mois,$id_affaire)
{
$date = last_day($annee,$mois);//

$sql = "select sum(if(clos=1,etat_ok,quantite)*prix_vente) from phase_i 
where id_affaire = '$id_affaire' and (d_fin_sap_facture > '$date' or d_fin_sap_facture = '0000-00-00')and date_deb <= '$date' and date_deb <> '0000-00-00' ";
$total=nombre_de($sql);
//echo $sql;
return $total;
}





///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////        Etat cumulé des affaires    ////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////





function aff_ec_dfm($date) //date au format français. Retourne la date de fin du mois
	{
		$date = datotimestamp(dftoda($date));
		return datodf(last_day(date('Y',$date),date('m',$date)));
	}
	



function affaire_etat_cumul($annee_util,$util=0,$cac='')
	{
	/****** CONSTITITUION DES TABLEAUX DE REFERENCE *****/
	//Decendance des affaires
	
	//piece serie
	/*
	$t_id_piece[$l['id']]=13528;
	$t_id_piece[$l['id']]=13531;
	$t_id_piece[$l['id']]=14557;
	$t_id_piece[$l['id']]=15000;
	$t_id_piece[$l['id']]=20099;
	$t_id_piece[$l['id']]=20209;
	$t_id_piece[$l['id']]=20207;
	$t_id_piece[$l['id']]=20348;
	$t_id_piece[$l['id']]=20212;
	
	$sql="select id , id_piece from affaire  ";//where pere='0'
	if($util>0) $sql .= " where id=$util ";
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t_a2[ $l['id'] ][]=$l['id']; //$t_a2['pere'] = array fils
		$t = ma_descendance($l['id']);
		if(is_array($t)) foreach($t as $fils)$t_a2[ $l['id'] ][]=$fils;
		$t_id_piece[$l['id']]=$l['id_piece'];
		}
		*/

	if($util>0)
		{
		if(is_array($t_a2[$util]))
			{
			$famille=tabtosql($t_a2[$util]);
			}
			else
			{
			$famille=$util;
			}
		}
		
	$tx=section_tx();

	//Initialisation du tableau de données
	$t = array();

	/****** RECUPERATION DE L'EXISTANT *****/
	$sql = "select * from affaire_etatc".$cac."  where annee in ($annee_util,".($annee_util-1).") ";
	if($util>0) $sql .= " and id_affaire in (".$famille.") ";
	$r = my_query($sql);
	while($l = mysql_fetch_array($r))
		{
		if($l['annee']==$annee_util-1 && $l['mois']==12) //Données de base
			{
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['marche_c_g'] = $l["marche_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['avt'] = $l["avt"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['tcf'] = $l["tcf"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['facture_c_g'] = $l["facture_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['cout_c_g'] = $l["cout_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['tps_c_g'] = $l["tps_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['achat_c_g'] = $l["achat_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['dep_c_g'] = $l["dep_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['frais_c_g'] = $l["frais_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['total_d_c_g'] = $l["total_d_c_g"];
			$base[ $l['id_affaire'] ][ $l['annee']+1 ]['total_p_c_g'] = $l["total_p_c_g"];
			}
		elseif($l['annee']==$annee_util) //Tuples à modifier
			{
			$t[ $l['id_affaire'] ][ $l['annee'] ][ $l['mois'] ]['id'] = $l["id"];
			$t[ $l['id_affaire'] ][ $l['annee'] ][ $l['mois'] ]['avt'] = $l["avt"];
			}
		}

//print_r($base);

	/****** EPURATION DES POINTAGES *****/
	$sql="delete from affaire_etatc_pointage".$cac." where annee=$annee_util  ";
	if($util>0) $sql .= " and id_affaire in (".$famille.") ";
	my_query($sql);

	/****** TRAITEMENT DES POINTAGES [ RECUPERATION + ENREGISTREMENT ] *****/
	$sql= "select sum(time_to_sec(p.h_t)/3600) as tps, p.section , year(p.date) as annee, month(p.date) as mois, p.id_affaire , pi.tx_gt
	from pointage p
	left join phase_i pi on p.id_phase_i = pi.id
	where p.id_affaire>0
	and year(p.date) = $annee_util  ";
	if($util>0) $sql .= " and p.id_affaire in (".$famille.") ";
	$sql .= "  group by p.id_affaire, annee, mois, p.section ";
	$r=my_query($sql);
	//echo $sql;
	while($l=mysql_fetch_array($r))
		{
		//Taux en cours
		$cout=$tx[ $l['section'] ][$l['annee']][$l['mois']]*$l["tps"];

		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['cout'] += $cout;
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['tps'] += $l["tps"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['cout_c_y'] += $cout;
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['tps_c_y'] += $l["tps"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['cout_c_g'] += $cout;
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['tps_c_g'] += $l["tps"];

		$vf = new valid_form() ;
		$vf->add("id_affaire", $l['id_affaire']);
		$vf->add("section", $l['section']);
		$vf->add("tps", $l['tps']);
		$vf->add("mois", $l['mois']);
		$vf->add("annee", $l['annee']);
		$vf->add("tx", div($cout,$l['tps']));
		$vf->add("tot", $cout);
		$vf->insert("affaire_etatc_pointage".$cac);
		}

	/****** RECUPERATION DES DONNEES *****/
	
	//Avenants
	$sql= "select year(av.date) as annee, month(av.date) as mois, av.mt_ht, av.id_affaire 
	from affaire_avenant av
	left join affaire a on av.id_affaire = a.id
	where av.id_affaire>0  and av.id_affaire not in (13528,13531,14557,15000,20099,20209,20207,20348,20212)
	and year(av.date)=$annee_util ";
	//and a.id_piece = 0
	if($util>0) $sql .= "  and id_affaire in (".$famille.") ";
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['marche'] += $l['mt_ht'];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['marche_c_y'] += $l['mt_ht'];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['marche_c_g'] += $l['mt_ht'];
		}
	
	if($util>0){ $sql = "  and fl.id_affaire in (".$famille.") ";}else{$sql="";}
	//Factures
	$sql= "
	select sum(if(f.type=1,fl.m_ht / f.devise_tx,fl.m_ht / f.devise_tx *-1)) AS mt_ht, year(f.date) as annee, month(f.date) as mois, fl.id_affaire 
	from facture f left join facture_ligne fl on fl.id_facture = f.id where f.clos=1 and fl.id_pere >0  and year(f.date)=$annee_util $sql group by fl.id_affaire, annee, mois ";
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['facture'] += $l["mt_ht"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['facture_c_g'] += $l["mt_ht"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['facture_c_y'] += $l["mt_ht"];
		}
	
	//Achats
	$sql= "select year(al.d_liv_reel) annee, month(al.d_liv_reel) mois, qte_recue*p_u*(1-remise/100) as achat, id_affaire
	from achat_ligne al
	where al.id_affaire>0
	and al.etat in (7,8,9,10)
	and year(al.d_liv_reel)=$annee_util ";
	if($util>0) $sql .= "  and id_affaire in (".$famille.") ";
	//echo $sql;
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['achat'] += $l["achat"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['achat_c_y'] += $l["achat"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['achat_c_g'] += $l["achat"];

		}

	//Déplacements
	$sql= "	select year(fd.d_debut) annee, month(fd.d_debut) mois, fdl.debit as debit, fdl.id_affaire
	from fiche_dep_liste fdl
	left join fiche_dep fd on fdl.id_fiche_dep=fd.id
	where year(d_debut)=$annee_util and fd.etat in (7,8,9,10)";
	if($util>0) $sql .= "  and id_affaire in (".$famille.") ";
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep'] += $l["debit"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep_c_y'] += $l["debit"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep_c_g'] += $l["debit"];
		}

	//Déplacements 2
	$sql= "select year(date) annee, month(date) mois, sum(deplacement) as debit, id_affaire
	from affaire_deplacement
	where year(date)=$annee_util  ";
	if($util>0) $sql .= "  and id_affaire in (".$famille.") ";
	$sql .= " group by id_affaire , annee , mois";
	//echo $sql;
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep'] += $l["debit"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep_c_y'] += $l["debit"];
		$t[$l['id_affaire']][ $l['annee'] ][ $l['mois'] ]['dep_c_g'] += $l["debit"];
		}
		
		
foreach($t_a2 as $id_affaire => $fils)
	{
	foreach($fils as $id_fils)
		{
		if(is_array($t[$id_fils]))foreach($t[$id_fils] as $annee=>$data_y)
			{
			foreach($data_y as $mois=>$data_m)
				{
				$a[$id_affaire][ $annee ][ $mois ]['id'] = 		$t[$id_affaire][ $annee ][ $mois ]['id'] ;
				$a[$id_affaire][ $annee ][ $mois ]['avt'] = 		$t[$id_affaire][ $annee ][ $mois ]['avt'];
				$a[$id_affaire][ $annee ][ $mois ]['cout'] += 		$t[$id_fils][ $annee ][ $mois ]['cout'];
				$a[$id_affaire][ $annee ][ $mois ]['tps'] += 		$t[$id_fils][ $annee ][ $mois ]['tps'];
				$a[$id_affaire][ $annee ][ $mois ]['cout_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['cout_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['tps_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['tps_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['cout_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['cout_c_g'] ;
				$a[$id_affaire][ $annee ][ $mois ]['tps_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['tps_c_g'];
				$a[$id_affaire][ $annee ][ $mois ]['marche'] += 	$t[$id_fils][ $annee ][ $mois ]['marche'];
				$a[$id_affaire][ $annee ][ $mois ]['marche_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['marche_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['marche_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['marche_c_g'];
				$a[$id_affaire][ $annee ][ $mois ]['facture'] += 	$t[$id_fils][ $annee ][ $mois ]['facture'];
				$a[$id_affaire][ $annee ][ $mois ]['facture_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['facture_c_g'];
				$a[$id_affaire][ $annee ][ $mois ]['facture_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['facture_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['achat'] += 		$t[$id_fils][ $annee ][ $mois ]['achat'];
				$a[$id_affaire][ $annee ][ $mois ]['achat_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['achat_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['achat_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['achat_c_g'];
				$a[$id_affaire][ $annee ][ $mois ]['dep'] += 		$t[$id_fils][ $annee ][ $mois ]['dep'] ;
				$a[$id_affaire][ $annee ][ $mois ]['dep_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['dep_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['dep_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['dep_c_g'];
				$a[$id_affaire][ $annee ][ $mois ]['frais'] += 		$t[$id_fils][ $annee ][ $mois ]['frais'] ;
				$a[$id_affaire][ $annee ][ $mois ]['frais_c_y'] += 	$t[$id_fils][ $annee ][ $mois ]['frais_c_y'];
				$a[$id_affaire][ $annee ][ $mois ]['frais_c_g'] += 	$t[$id_fils][ $annee ][ $mois ]['frais_c_g'];
						
				}
			}
		}
	}
	
$t=$a;	
/*

echo '<pre>';
print_r($t[17363]);
echo '</pre>';
*/

	/****** ENREGISTREMENT *****/
	
	if(is_array($t)) ksort($t);
	if(is_array($t)) foreach($t as $id_affaire=>$t2) //Par affaire
		{
		if($id_affaire=="") continue;
		
		//{$calcul_ps=1;}else{$calcul_ps=0;}
		if(($annee_util>=2011)and($id_affaire==14557 or $id_affaire==13528 or $id_affaire==13531 or $id_affaire==15000 or $id_affaire==20099 or $id_affaire==20209 or $id_affaire==20207 or $id_affaire==20348 or $id_affaire==20212)){$calcul_ps=2;}
		else if($t_id_piece[$id_affaire]>0){$calcul_ps=1;}
		else{$calcul_ps=0;}
		
		//Année
		$annee = $annee_util;
		$t2=$t2[$annee];

		//Données de base
		$b=$base[$id_affaire][$annee];
		$marche_c = 0;
		$facture_c = 0;
		$tps_c = 0;
		$cout_c = 0;
		$achat_c = 0;
		$dep_c = 0;
		$frais_c = 0;
		$total_p_c=0;
		$tcd=$b['tcf']; 
		$avt=$b['avt']; 

		
		
		for($mois=1;$mois<13;$mois++) //Par mois
			{
			//echo $annee." - $mois - ".$tcd."<br>";
			//Calculs et données de base
				$data = array();
				if(is_array($data = $t[$id_affaire][$annee][$mois])) $data = $t[$id_affaire][$annee][$mois];
				
				$data['tcd'] = $tcd;
				
				
				//avt le premier du mois
				if(datotimestamp(date("Y-m-01"))<=datotimestamp($annee.'-'.format_0($mois,2).'-01')) $data['avt'] = nombre_de("select if(d_soldee='0000-00-00',((h_avt_c * h_mt_devis_c + achat_devis_c * achat_avt_c)/(h_mt_devis_c + achat_devis_c)),100) as avt from affaire where id='$id_affaire'");

				$data['facture_c_g'] += $facture_c+$b["facture_c_g"];
				
					

				if($calcul_ps==2)
					{
					$data['tcf'] = calcul_en_cour($annee,$mois,$id_affaire);

					$data['marche_c_g'] += $facture_c + $data['facture'] + $data['tcf'] - $data['tcd'];
					$data['marche_c_y'] += $facture_c + $data['facture'] + $data['tcf'] - $data['tcd'];
					}					
					else if($calcul_ps==1)
					{
					$data['tcf'] = calcul_en_cour($annee,$mois,$id_affaire);
					$data['marche_c_g'] += $marche_c+$b["marche_c_g"];
					$data['marche_c_y'] += $marche_c+$b["marche_c_g"];
					}
					else
					{
					$data['marche_c_g'] += $marche_c+$b["marche_c_g"];
					$data['marche_c_y'] += $marche_c+$b["marche_c_g"];
					$data['tcf'] = ($data['marche_c_g'] * $data['avt']/100)-$data['facture_c_g'];

					}

					
				$data['total_d'] = $data['cout']+$data['dep']+$data['frais']+$data['achat'];
				$data['total_p'] = $data['facture']+$data['tcf']-$data['tcd'];
				$data['resultat'] = $data['total_p']-$data['total_d'];
				
				$data['facture_c_y'] += $facture_c;
				$data['cout_c_y'] += $cout_c+$b["cout_c_y"];
				$data['tps_c_y'] += $tps_c+$b["tps_c_y"];
				$data['achat_c_y'] += $achat_c+$b["achat_c_y"];
				$data['dep_c_y'] += $dep_c+$b["dep_c_y"];
				$data['frais_c_y'] += $frais_c+$b["frais_c_y"];
				$data['total_d_c_y'] = $data['cout_c_y']+$data['dep_c_y']+$data['frais_c_y']+$data['achat_c_y'];
				$data['total_p_c_y'] = $total_p_c+$data['total_p'];
				$data['resultat_y'] = $data['total_p_c_y']-$data['total_d_c_y'];
				$data['cout_c_g'] += $cout_c+$b["cout_c_g"];
				$data['tps_c_g'] += $tps_c+$b["tps_c_g"];
				$data['achat_c_g'] += $achat_c+$b["achat_c_g"];
				$data['dep_c_g'] += $dep_c+$b["dep_c_g"];
				$data['frais_c_g'] += $frais_c+$b["frais_c_g"];
				$data['total_d_c_g'] += $data['cout_c_g']+$data['dep_c_g']+$data['frais_c_g']+$data['achat_c_g'];
				$data['total_p_c_g'] = $b['total_p_c_g'] + $total_p_c+$data['total_p'];
				$data['resultat_g'] = $data['total_p_c_g']-$data['total_d_c_g'];
				$data['coef'] = div($data['total_p_c_g'],$data['total_d_c_g']);


				$tcd=$data['tcf'];
				$avt=$data['avt'];

				$marche_c += $data['marche'];
				$facture_c += $data['facture'];
				$tps_c += $data['tps'];
				$cout_c += $data['cout'];
				$achat_c += $data['achat'];
				$dep_c += $data['dep'];
				$frais_c += $data['frais'];
				$total_p_c +=$data['total_p'];
				
			//Enregistrement
			$vf = new valid_form() ;
			$vf->add("id_affaire", $id_affaire);
			$vf->add("annee", $annee);
			$vf->add("mois", $mois);
			$vf->add("marche", $data['marche']+0);
			$vf->add("avt", $data['avt']+0);
			$vf->add("tcd", $data['tcd']+0);
			$vf->add("tcf", $data['tcf']+0);
			$vf->add("facture", $data['facture']+0);
			$vf->add("cout", $data['cout']+0);
			$vf->add("tps", $data['tps']+0);
			$vf->add("achat", $data['achat']+0);
			$vf->add("dep", $data['dep']+0);
			$vf->add("frais", $data['frais']+0);
			$vf->add("total_d", $data['total_d']+0);
			$vf->add("total_p", $data['total_p']+0);
			$vf->add("resultat", $data['resultat']+0);
			$vf->add("marche_c_y", $data['marche_c_y']+0);
			$vf->add("facture_c_y", $data['facture_c_y']+0);
			$vf->add("cout_c_y", $data['cout_c_y']+0);
			$vf->add("tps_c_y", $data['tps_c_y']+0);
			$vf->add("achat_c_y", $data['achat_c_y']+0);
			$vf->add("dep_c_y", $data['dep_c_y']+0);
			$vf->add("frais_c_y", $data['frais_c_y']+0);
			$vf->add("total_d_c_y", $data['total_d_c_y']+0);
			$vf->add("total_p_c_y", $data['total_p_c_y']+0);
			$vf->add("resultat_y", $data['resultat_y']+0);
			$vf->add("marche_c_g", $data['marche_c_g']+0);
			$vf->add("facture_c_g", $data['facture_c_g']+0);
			$vf->add("cout_c_g", $data['cout_c_g']+0);
			$vf->add("tps_c_g", $data['tps_c_g']+0);
			$vf->add("achat_c_g", $data['achat_c_g']+0);
			$vf->add("dep_c_g", $data['dep_c_g']+0);
			$vf->add("frais_c_g", $data['frais_c_g']+0);
			$vf->add("total_d_c_g", $data['total_d_c_g']+0);
			$vf->add("total_p_c_g", $data['total_p_c_g']+0);
			$vf->add("resultat_g", $data['resultat_g']+0);
			$vf->add("coef", $data['coef']+0);
			$vf->add("dstamp", ($annee*12)+$mois);


			if($data['id']>0)
				{
				$vf->update("affaire_etatc".$cac," where id = '".$data['id']."'",'',1);
				}
				else
				{
				//if(($cac=="_cac")and($id_affaire==16224 or $id_affaire == 16226))continue;//delete FROM `affaire_etatc_cac` WHERE id_affaire in (16224,16226)
				$vf->insert("affaire_etatc".$cac);
				}
			//echo $vf->req;

			}
		}

	//return (getmicrotime()-$temps_depart);
	}



function affaire_etat_cumul_control($util=0,$cac=""){
	/*
	Recherche les années vides dans une affaire et la crée
	Exemples : il existe des données pour les années 2006 et 2008.
	On crée donc 2007 pour assurer la continuité des données.
	*/

	//$temps_depart=getmicrotime();

	if($util>0) $sql_extend = "and id_affaire=".$util;
	$sql = "select * from affaire_etatc".$cac." where mois=12 $sql_extend order by id_affaire asc, annee asc";
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		$t[ $l['id_affaire'] ][ $l['annee'] ] = $l;
		}

	if(is_array($t)) foreach($t as $id_affaire=>$t_annee)
		{
		$annee_prec=0;
		ksort($t_annee);
		foreach($t_annee as $annee=>$l)
			{
			//echo "prec $annee_prec <br>";
			if($annee_prec!=0 && $annee!=$annee_prec+1)
				{
				$data = $t[$id_affaire][$annee_prec];
				$annee_prec++;
				while($annee_prec<$annee)
					{
					for($mois=1;$mois<13;$mois++)
						{
						//Enregistrement
						$vf = new valid_form();
						$vf->add("id_affaire", $id_affaire);
						$vf->add("annee", $annee_prec);
						$vf->add("mois", $mois);
						$vf->add("marche", 0);
						$vf->add("avt", $data['avt']);
						$vf->add("tcd", $data['tcf']);
						$vf->add("tcf", $data['tcf']);
						$vf->add("facture", 0);
						$vf->add("cout", 0);
						$vf->add("tps", 0);
						$vf->add("achat", 0);
						$vf->add("dep", 0);
						$vf->add("total_d", 0);
						$vf->add("total_p", 0);
						$vf->add("resultat", 0);
						$vf->add("marche_c_y", 0);
						$vf->add("facture_c_y", 0);
						$vf->add("cout_c_y", 0);
						$vf->add("tps_c_y", 0);
						$vf->add("achat_c_y", 0);
						$vf->add("dep_c_y", 0);
						$vf->add("total_d_c_y", 0);
						$vf->add("total_p_c_y", 0);
						$vf->add("resultat_y", 0);
						$vf->add("marche_c_g", $data['marche_c_g']);
						$vf->add("facture_c_g", $data['facture_c_g']);
						$vf->add("cout_c_g", $data['cout_c_g']);
						$vf->add("tps_c_g", $data['tps_c_g']);
						$vf->add("achat_c_g", $data['achat_c_g']);
						$vf->add("dep_c_g", $data['dep_c_g']);
						$vf->add("total_d_c_g", $data['total_d_c_g']);
						$vf->add("total_p_c_g", $data['total_p_c_g']);
						$vf->add("resultat_g", $data['resultat_g']);
						$vf->add("coef", $data['coef']);
						$vf->add("dstamp", ($annee*12)+$mois);

						$vf->insert("affaire_etatc".$cac);
						}

						$annee_prec++;
					}
					$annee_prec--; //Récupération de la dernière année enregistrée
					affaire_etat_cumul($annee,$id_affaire,$cac);
				}
				else
				{
				$annee_prec=$annee;
				}
			}
		}

	//return (getmicrotime()-$temps_depart);
}



function affaire_etat_cumul_aff($r_affaire,$r_annee=0,$r_mois=0,$r_nb_mois=0,$i_tr=0,$cac='')
	{
	global $f_mois;
	
	if($r_annee==0)$r_annee=nombre_de("select min(annee) from affaire_etatc".$cac." where id_affaire = $r_affaire");
	if($r_annee==0)$r_annee=nombre_de("select year(date) from affaire where id = $r_affaire");;
	if(($r_mois==0)or($r_nb_mois==0))
		{
		$r_mois=1;
		$r_nb_mois=nombre_de("select count(*) from affaire_etatc".$cac." where id_affaire = $r_affaire");
		}
	
	$r_annee_fin = date('Y',datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois));
	//$r_mois_fin = date('n',datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois));
	
	/*if(datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois)>datotimestamp(date("Y-m-d")))
		{
		$r_annee_fin=date('Y');
		$r_mois_fin = date('n');
		}
*/
	$d_stamp_deb = ($r_annee * 12) + $r_mois;
	$d_stamp_fin = $d_stamp_deb + $r_nb_mois-1;
	$d_stamp_prec = ($r_annee * 12);

	$t=array();

	$sql = "
	select aec.*, a.date ,  a.d_soldee from affaire_etatc".$cac." aec
	left join affaire a on aec.id_affaire = a.id
	where id_affaire=$r_affaire
	having dstamp between $d_stamp_deb and $d_stamp_fin or dstamp = $d_stamp_prec order by annee asc , mois asc";

	
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		//grise apres soldee
		if((datotimestamp($l['d_soldee'])<datotimestamp($l['annee'].'-'.format_0($l['mois'],2).'-01'))and($l['d_soldee']<>'0000-00-00'))
			{
			$soldee=1;
			}
			else
			{
			$soldee=0;
			}
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['id'] = $l['id'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['marche'] = $l['marche'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['avt'] = $l['avt'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['facture'] = $l['facture'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['facture_c_g'] = $l['facture_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tcd'] = $l['tcd'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tcf'] = $l['tcf'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['cout'] = $l['cout'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tps'] = $l['tps'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['cout_c'] = $l['cout_c'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['achat'] = $l['achat'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['dep'] = $l['dep'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['frais'] = $l['frais'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_d'] = $l['total_d'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_d_c_g'] = $l['total_d_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_p'] = $l['total_p'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['resultat'] = $l['resultat'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['soldee'] = $soldee;
		
		//if( ($l['annee']==$r_annee && $l['mois']<$r_mois) || ($l['annee']==$r_annee_fin && $l['mois']>$r_mois_fin)) continue;

		
		$t['etatc_y'][ $l['annee'] ]['facture_c'] = $l['facture_c_y'];
		$t['etatc_y'][ $l['annee'] ]['tot_c'] = $l['cout_c_y'];
		$t['etatc_y'][ $l['annee'] ]['tps_c'] = $l['tps_c_y'];
		$t['etatc_y'][ $l['annee'] ]['achat_c'] = $l['achat_c_y'];
		$t['etatc_y'][ $l['annee'] ]['dep_c'] = $l['dep_c_y'];
		$t['etatc_y'][ $l['annee'] ]['frais_c'] = $l['frais_c_y'];
		$t['etatc_y'][ $l['annee'] ]['total_d_c'] = $l['total_d_c_y'];
		$t['etatc_y'][ $l['annee'] ]['total_d_c_g'] = $l['total_d_c_g'];
		$t['etatc_y'][ $l['annee'] ]['total_p_c'] = $l['total_p_c_y'];
		$t['etatc_y'][ $l['annee'] ]['resultat'] = $l['resultat_y'];
		$t['etatc_y'][ $l['annee'] ]['tcd'] = $l['tcd'];
		$t['etatc_y'][ $l['annee'] ]['tcf'] = $l['tcf'];
		$t['etatc_y'][ $l['annee'] ]['marche_c_y'] = $l['marche_c_y'];
		$t['etatc_y'][ $l['annee'] ]['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_y'][ $l['annee'] ]['avt'] = $l['avt'];
		$t['etatc_y'][ $l['annee'] ]['date'] = $l['date'];

		$t['etatc_g'][ $l['annee'] ]['facture_c'] = $l['facture_c_g'];
		$t['etatc_g'][ $l['annee'] ]['tot_c'] = $l['cout_c_g'];
		$t['etatc_g'][ $l['annee'] ]['tps_c'] = $l['tps_c_g'];
		$t['etatc_g'][ $l['annee'] ]['achat_c'] = $l['achat_c_g'];
		$t['etatc_g'][ $l['annee'] ]['dep_c'] = $l['dep_c_g'];
		$t['etatc_g'][ $l['annee'] ]['frais_c'] = $l['frais_c_g'];
		$t['etatc_g'][ $l['annee'] ]['total_d_c'] = $l['total_d_c_g'];
		$t['etatc_g'][ $l['annee'] ]['total_p_c'] = $l['total_p_c_g'];
		$t['etatc_g'][ $l['annee'] ]['resultat'] = $l['resultat_g'];
		$t['etatc_g'][ $l['annee'] ]['tcd'] = $l['tcd'];
		$t['etatc_g'][ $l['annee'] ]['tcf'] = $l['tcf'];
		$t['etatc_g'][ $l['annee'] ]['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_g'][ $l['annee'] ]['avt'] = $l['avt'];
		$t['etatc_g'][ $l['annee'] ]['date'] = $l['date'];
		}

	if(count($t)<1)
		{
		echo 'Aucun résultat';
		}
		else
		{
		$entete = '	<tr class=m3>
					<td width="6%" nowrap colspan=2><A href="affaire.php?id_affaire='.$r_affaire.'&parent='.urlencode('affaire_etat_cumul.php?r_ouvrir='.$r_affaire.'#affaire_'.$r_affaire).'">'.$r_affaire.'</A></td>
					<td width="8%" nowrap>Marché</td>
					<td width="3%" nowrap>Avt</td>
					<td width="3%" nowrap>Théo.</td>
					<td width="8%" nowrap>Facture</td>
					<td width="7%" nowrap>TC début</td>
					<td width="7%" nowrap>TC fin</td>
					<td width="5%" nowrap>H</td>
					<td width="8%" nowrap>Coût</td>
					<td width="6%" nowrap>Moy TH</td>
					<td width="8%" nowrap>Achats</td>
					<td width="5%" nowrap>Déplacements</td>
					<td width="8%" nowrap>T. Dépenses</td>
					<td width="8%" nowrap>Résultat</td>
					<td width="8%" nowrap>T. Produit</td>
				</tr>';
		?>
		<td colspan=14 style="padding-left:10px;" class=cel3>
		<table class=forumline width=100% cellspacing=0>
			
			
		<?
		
		$class1='class=cel1';
		$class2='class=cel2';
		$class3='class=cel3';
		
		$aff_y_prec=1;
		ksort($t['etatc_n']); //Tri par années
		foreach($t['etatc_n'] as $annee=>$t2)
			{
			if($aff_y_prec==0)
				{
				echo '<tr><td colspan=8>&nbsp;</td></tr>';
				}
				
			if($annee<$r_annee || $annee>$r_annee_fin) continue;
			
			echo $entete;
				//Ligne de total année n-1
				$t_tot_y_prec = $t['etatc_g'][$annee-1];

			if($aff_y_prec==1)
				{
				
				
				?>
				<tr class=m3 >
					<td colspan=2 nowrap align=center >Total <? echo $annee-1; ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['marche_c_g'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['avt'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat(div($t_tot_y_prec['total_d_c_g']*100,$t_tot_y_prec['marche_c_g']),'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['facture_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcd'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tps_c'],'',1,2); ?></td>
					<td nowrap align=center <? echo 'onclick="derouler_section(\'d_section_y_'.$r_affaire.'_'.($annee-1).'\','.$r_affaire.','.($annee-1).',\'y\');"'; ?> ><A class=b><? echo nformat($t_tot_y_prec['tot_c'],'',1,2); ?></A></td>
					<td align=center ><? echo nformat(div($t_tot_y_prec['tot_c'],$t_tot_y_prec['tps_c']),'',1,2); ?></td>
					<td nowrap align=center ><A class=b href="javascript:aff_select_achat(<? echo "'01/01/".($annee-1)."','31/12/".($annee-1)."','$r_affaire'"; ?>);"><? echo nformat($t_tot_y_prec['achat_c'],'',1,2); ?></A></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['dep_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['total_d_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['resultat'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['total_p_c'],'',1,2); ?></td>
				</tr>
				<tr id="d_section_y_<?echo $r_affaire.'_'.($annee-1);?>" style="display:none;" >
					<td class=cel1 colspan="16" style="padding-left:10px;">*Section*</td>
				</tr>
				<?
				$aff_y_prec=0;
				}
				
			ksort($t2); //Tri par mois
			foreach($t2 as $mois=>$t3)
				{
				//if( ($annee==$r_annee && $mois<$r_mois) || ($annee==$r_annee_fin && $mois>$r_mois_fin)) continue;
				$r_date="01/".format_0($mois,2)."/$annee";
				$data = $t3['data'];
				if($data['soldee']==1)
					{
					$class1='class=cel3';
					$class2='class=cel3';
					$class3='class=cel3';
					}
				?>
				
				<tr class=m2 >
					<td <? echo $class3;?> nowrap align=center ><? echo $annee; ?></td>
					<td <? echo $class3;?> nowrap align=center ><? echo $f_mois[$mois]; ?></td>
					<td  <? echo $class1;?> nowrap align=center title="<? echo $f_mois[$mois].' : '.nformat($data['marche'],'',1,2); ?>" ><? echo nformat($data['marche_c_g'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ondblclick=avancement(this,<? echo $data['id'].','.$i_tr.','.$r_affaire.','.$annee; ?>); ><? echo nformat($data['avt'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat(div($data['total_d_c_g']*100,$data['marche_c_g']),'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['facture'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['tcd'],'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['tcf'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><A class=b href="javascript:aff_select_pointage('','<? echo tabtosql(ma_descendance($r_affaire,array($r_affaire))); ?>','<? echo $r_date; ?>','<? echo aff_ec_dfm($r_date); ?>');"><? echo nformat($data['tps'],'',1,2); ?></A></td>
					<td  <? echo $class1;?> nowrap align=center <? echo 'onclick="derouler_section(\'d_section_'.$r_affaire.'_'.$annee.'_'.$mois.'\','.$r_affaire.','.$annee.','.$mois.');"'; ?> ><A class=b><? echo nformat($data['cout'],'',1,2); ?></A></td>
					<td  <? echo $class2;?> align=center ><? echo nformat(div($data['cout'],$data['tps']),'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><A class=b href="javascript:aff_select_achat(<? echo "'".$r_date."','".aff_ec_dfm($r_date)."','$r_affaire'"; ?>);"><? echo nformat($data['achat'],'',1,2); ?></A></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['dep'],'',1,2); ?></td>
					<td  <? echo $class3;?> nowrap align=center ><? echo nformat($data['total_d'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['resultat'],'',1,2); ?></td>
					<td  <? echo $class3;?> nowrap align=center ><? echo nformat($data['total_p'],'',1,2); ?></td>
				</tr>
				<tr id="d_section_<?echo $r_affaire.'_'.$annee.'_'.$mois;?>" style="display:none;" >
					<td class=cel1 colspan="16" style="padding-left:10px;">*Section*</td>
				</tr>
				<?
				$i++; //Indice de la ligne pour le déroulage
				$mois_precedent=$mois;
				}
				//Lignes de total
			$t_tot_y = $t['etatc_y'][$annee];
			$t_tot_g = $t['etatc_g'][$annee];
			?>
			<tr class=m3 >
				<td nowrap align=center colspan=2>Cumul <? echo $annee; ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['marche_c_y'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['avt'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat(div($t_tot_y['total_d_c_g']*100,$t_tot_y['marche_c_g']),'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['facture_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['tcf'],'',1,2); ?></td>
				<td nowrap align=center ><A href="javascript:aff_select_pointage('','<? echo tabtosql(ma_descendance($r_affaire,array($r_affaire))); ?>','01/01/<? echo $annee; ?>','31/12/<? echo $annee; ?>');"><? echo nformat($t_tot_y['tps_c'],'',1,2); ?></A></td>
				<td nowrap align=center <? echo 'onclick="derouler_section(\'d_section_y_'.$r_affaire.'_'.$annee.'\','.$r_affaire.','.$annee.',\'y\');"'; ?> ><A><? echo nformat($t_tot_y['tot_c'],'',1,2); ?></A></td>
				<td align=center ><? echo nformat(div($t_tot_y['tot_c'],$t_tot_y['tps_c']),'',1,2); ?></td>
				<td nowrap align=center ><A href="javascript:aff_select_achat(<? echo "'01/01/".$annee."','"."31/12/".$annee."','$r_affaire'"; ?>);"><? echo nformat($t_tot_y['achat_c'],'',1,2); ?></A></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['dep_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_d_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_p_c']-$t_tot_y['total_d_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_p_c'],'',1,2); ?></td>
			</tr>
			<tr id="d_section_y_<?echo $r_affaire.'_'.$annee;?>" style="display:none;" >
				<td class=cel1 colspan="16" style="padding-left:10px;">*Section*</td>
			</tr>
			<tr class=m3 >
				<td nowrap colspan=2>Total affaire</td>
				<td nowrap ><? echo nformat($t_tot_g['marche_c_g'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['avt'],'',1,2); ?></td>
				<td nowrap ><? echo nformat(div($t_tot_g['total_d_c']*100,$t_tot_g['marche_c_g']),'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['facture_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['tcf'],'',1,2); ?></td>
				<td nowrap ><A href="javascript:aff_select_pointage('','<? echo tabtosql(ma_descendance($r_affaire,array($r_affaire))); ?>','','');"><? echo nformat($t_tot_g['tps_c'],'',1,2); ?></A></td>
				<td nowrap <? echo 'onclick="derouler_section(\'d_section_g_'.$r_affaire.'_'.$annee.'\','.$r_affaire.','.$annee.',\'g\');"'; ?> ><A><? echo nformat($t_tot_g['tot_c'],'',1,2); ?></A></td>
				<td ><? echo nformat(div($t_tot_g['tot_c'],$t_tot_g['tps_c']),'',1,2); ?></td>
				<td nowrap ><A href="javascript:aff_select_achat(<? echo "'".datodf($t_tot_g['date'])."','31/12/".$annee."','$r_affaire'"; ?>);"><? echo nformat($t_tot_g['achat_c'],'',1,2); ?></A></td>
				<td nowrap ><? echo nformat($t_tot_g['dep_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_d_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_p_c']-$t_tot_g['total_d_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_p_c'],'',1,2); ?></td>
			</tr>
			<tr id="d_section_g_<?echo $r_affaire.'_'.$annee;?>" style="display:none;" >
				<td class=cel1 colspan="16" style="padding-left:10px;">*Section*</td>
			</tr>
			<?
			}
		echo '</table></td>';
		}

	}

	
function affaire_etat_cumul_aff2export($r_affaire,$r_annee=0,$r_mois=0,$r_nb_mois=0,$i_tr=0,$cac='')
	{
	global $f_mois,$j_affaire_etat;
	
	if($r_annee==0)$r_annee=nombre_de("select min(annee) from affaire_etatc".$cac." where id_affaire = $r_affaire");
	if($r_annee==0)$r_annee=nombre_de("select year(date) from affaire where id = $r_affaire");;
	if(($r_mois==0)or($r_nb_mois==0))
		{
		$r_mois=1;
		$r_nb_mois=nombre_de("select count(*) from affaire_etatc".$cac." where id_affaire = $r_affaire");
		}
	
	$r_annee_fin = date('Y',datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois));
	//$r_mois_fin = date('n',datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois));
	
	/*if(datotimestamp("$r_annee-".format_0($r_mois,2)."-01",0,$r_nb_mois)>datotimestamp(date("Y-m-d")))
		{
		$r_annee_fin=date('Y');
		$r_mois_fin = date('n');
		}
*/
	$d_stamp_deb = ($r_annee * 12) + $r_mois;
	$d_stamp_fin = $d_stamp_deb + $r_nb_mois-1;
	$d_stamp_prec = ($r_annee * 12);

	$t=array();

	$sql = "
	select aec.*, a.date ,  a.d_soldee , a.etat , a.designation1 , a.client, concat(SUBSTRING(i.prenom,1,1),'. ',i.nom) as preparateur  
	from affaire_etatc".$cac." aec
	left join affaire a on aec.id_affaire = a.id
	left join interne i on a.r_real=i.id
	where id_affaire=$r_affaire
	having dstamp between $d_stamp_deb and $d_stamp_fin or dstamp = $d_stamp_prec order by annee asc , mois asc";

	
	$r=my_query($sql);
	while($l=mysql_fetch_array($r))
		{
		//grise apres soldee
		if((datotimestamp($l['d_soldee'])<datotimestamp($l['annee'].'-'.format_0($l['mois'],2).'-01'))and($l['d_soldee']<>'0000-00-00'))
			{
			$soldee=1;
			}
			else
			{
			$soldee=0;
			}
			
		$a['etat'] = $j_affaire_etat[$l['etat']];
		$a['designation1'] = $l['designation1'];
		$a['client'] = $l['client'];
		$a['preparateur'] = $l['preparateur'];
			
			
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['id'] = $l['id'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['marche'] = $l['marche'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['avt'] = $l['avt'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['facture'] = $l['facture'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['facture_c_g'] = $l['facture_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tcd'] = $l['tcd'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tcf'] = $l['tcf'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['cout'] = $l['cout'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['tps'] = $l['tps'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['cout_c'] = $l['cout_c'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['achat'] = $l['achat'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['dep'] = $l['dep'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['frais'] = $l['frais'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_d'] = $l['total_d'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_d_c_g'] = $l['total_d_c_g'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['total_p'] = $l['total_p'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['resultat'] = $l['resultat'];
		$t['etatc_n'][ $l['annee'] ][ $l['mois'] ]['data']['soldee'] = $soldee;
		
		//if( ($l['annee']==$r_annee && $l['mois']<$r_mois) || ($l['annee']==$r_annee_fin && $l['mois']>$r_mois_fin)) continue;

		
		$t['etatc_y'][ $l['annee'] ]['facture_c'] = $l['facture_c_y'];
		$t['etatc_y'][ $l['annee'] ]['tot_c'] = $l['cout_c_y'];
		$t['etatc_y'][ $l['annee'] ]['tps_c'] = $l['tps_c_y'];
		$t['etatc_y'][ $l['annee'] ]['achat_c'] = $l['achat_c_y'];
		$t['etatc_y'][ $l['annee'] ]['dep_c'] = $l['dep_c_y'];
		$t['etatc_y'][ $l['annee'] ]['frais_c'] = $l['frais_c_y'];
		$t['etatc_y'][ $l['annee'] ]['total_d_c'] = $l['total_d_c_y'];
		$t['etatc_y'][ $l['annee'] ]['total_d_c_g'] = $l['total_d_c_g'];
		$t['etatc_y'][ $l['annee'] ]['total_p_c'] = $l['total_p_c_y'];
		$t['etatc_y'][ $l['annee'] ]['resultat'] = $l['resultat_y'];
		$t['etatc_y'][ $l['annee'] ]['tcd'] = $l['tcd'];
		$t['etatc_y'][ $l['annee'] ]['tcf'] = $l['tcf'];
		$t['etatc_y'][ $l['annee'] ]['marche_c_y'] = $l['marche_c_y'];
		$t['etatc_y'][ $l['annee'] ]['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_y'][ $l['annee'] ]['avt'] = $l['avt'];
		$t['etatc_y'][ $l['annee'] ]['date'] = $l['date'];

		$t['etatc_g'][ $l['annee'] ]['facture_c'] = $l['facture_c_g'];
		$t['etatc_g'][ $l['annee'] ]['tot_c'] = $l['cout_c_g'];
		$t['etatc_g'][ $l['annee'] ]['tps_c'] = $l['tps_c_g'];
		$t['etatc_g'][ $l['annee'] ]['achat_c'] = $l['achat_c_g'];
		$t['etatc_g'][ $l['annee'] ]['dep_c'] = $l['dep_c_g'];
		$t['etatc_g'][ $l['annee'] ]['frais_c'] = $l['frais_c_g'];
		$t['etatc_g'][ $l['annee'] ]['total_d_c'] = $l['total_d_c_g'];
		$t['etatc_g'][ $l['annee'] ]['total_p_c'] = $l['total_p_c_g'];
		$t['etatc_g'][ $l['annee'] ]['resultat'] = $l['resultat_g'];
		$t['etatc_g'][ $l['annee'] ]['tcd'] = $l['tcd'];
		$t['etatc_g'][ $l['annee'] ]['tcf'] = $l['tcf'];
		$t['etatc_g'][ $l['annee'] ]['marche_c_g'] = $l['marche_c_g'];
		$t['etatc_g'][ $l['annee'] ]['avt'] = $l['avt'];
		$t['etatc_g'][ $l['annee'] ]['date'] = $l['date'];
		}

	if(count($t)<1)
		{
		echo 'Aucun résultat';
		}
		else
		{
		/*$entete = '	<tr class=m3>
					<td width="8%" nowrap>Affaire</td>
					<td width="8%" nowrap>Designation</td>
					<td width="8%" nowrap>Client</td>
					<td width="8%" nowrap>Etat</td>
					<td width="8%" nowrap>Période</td>
					<td width="8%" nowrap>Marché</td>
					<td width="3%" nowrap>Avt</td>
					<td width="3%" nowrap>Théo.</td>
					<td width="8%" nowrap>Facture</td>
					<td width="7%" nowrap>TC début</td>
					<td width="7%" nowrap>TC fin</td>
					<td width="5%" nowrap>H</td>
					<td width="8%" nowrap>Coût</td>
					<td width="6%" nowrap>Moy TH</td>
					<td width="8%" nowrap>Achats</td>
					<td width="5%" nowrap>Déplacements</td>
					<td width="8%" nowrap>T. Dépenses</td>
					<td width="8%" nowrap>Résultat</td>
					<td width="8%" nowrap>T. Produit</td>
				</tr>';*/
	
	
		$class1='class=cel1';
		$class2='class=cel2';
		$class3='class=cel3';
		
		$aff_y_prec=1;
		ksort($t['etatc_n']); //Tri par années
		foreach($t['etatc_n'] as $annee=>$t2)
			{
			if($aff_y_prec==0)
				{
				//echo '<tr><td colspan=8>&nbsp;</td></tr>';
				}
				
			if($annee<$r_annee || $annee>$r_annee_fin) continue;
			
			echo $entete;
				//Ligne de total année n-1
				$t_tot_y_prec = $t['etatc_g'][$annee-1];

			if($aff_y_prec==1)
				{
				
				?>
				<tr class=m3 >
					<td nowrap align=center ><? echo $r_affaire; ?></td>
					<td nowrap align=center ><? echo $a['designation1']; ?></td>
					<td nowrap align=center ><? echo $a['client']; ?></td>
					<td nowrap align=center ><? echo $a['etat']; ?></td>
					<td nowrap align=center ><? echo $a['preparateur']; ?></td>

					<td nowrap align=center >Total <? echo $annee-1; ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['marche_c_g'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['avt'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['facture_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcd'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tps_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['tot_c'],'',1,2); ?></td>
					<td align=center ><? echo nformat(div($t_tot_y_prec['tot_c'],$t_tot_y_prec['tps_c']),'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['achat_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['dep_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['total_d_c'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['resultat'],'',1,2); ?></td>
					<td nowrap align=center ><? echo nformat($t_tot_y_prec['total_p_c'],'',1,2); ?></td>
				</tr>
	
				<?
				$aff_y_prec=0;
				}
				
			ksort($t2); //Tri par mois
			foreach($t2 as $mois=>$t3)
				{
				//if( ($annee==$r_annee && $mois<$r_mois) || ($annee==$r_annee_fin && $mois>$r_mois_fin)) continue;
				$r_date="01/".format_0($mois,2)."/$annee";
				$data = $t3['data'];
				if($data['soldee']==1)
					{
					$class1='class=cel3';
					$class2='class=cel3';
					$class3='class=cel3';
					}
				?>
				
				<tr class=m2 >
					<td <? echo $class2;?> nowrap align=center ><? echo $r_affaire; ?></td>
					<td <? echo $class2;?> nowrap align=center ><? echo $a['designation1']; ?></td>
					<td <? echo $class2;?> nowrap align=center ><? echo $a['client']; ?></td>
					<td <? echo $class2;?> nowrap align=center ><? echo $a['etat']; ?></td>
					<td <? echo $class2;?> nowrap align=center ><? echo $a['preparateur']; ?></td>

					<td <? echo $class3;?> nowrap align=center ><? echo $f_mois[$mois]; ?></td>
					<td  <? echo $class1;?> nowrap align=center title="<? echo $f_mois[$mois].' : '.nformat($data['marche'],'',1,2); ?>" ><? echo nformat($data['marche_c_g'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['avt'],'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['facture'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['tcd'],'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['tcf'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['tps'],'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['cout'],'',1,2); ?></td>
					<td  <? echo $class2;?> align=center ><? echo nformat(div($data['cout'],$data['tps']),'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['achat'],'',1,2); ?></td>
					<td  <? echo $class1;?> nowrap align=center ><? echo nformat($data['dep'],'',1,2); ?></td>
					<td  <? echo $class3;?> nowrap align=center ><? echo nformat($data['total_d'],'',1,2); ?></td>
					<td  <? echo $class2;?> nowrap align=center ><? echo nformat($data['resultat'],'',1,2); ?></td>
					<td  <? echo $class3;?> nowrap align=center ><? echo nformat($data['total_p'],'',1,2); ?></td>
				</tr>
				
				<?
				$i++; //Indice de la ligne pour le déroulage
				$mois_precedent=$mois;
				}
				//Lignes de total
			$t_tot_y = $t['etatc_y'][$annee];
			$t_tot_g = $t['etatc_g'][$annee];
			?>
			<tr class=m3 >
				<td nowrap align=center ><? echo $r_affaire; ?></td>
				<td nowrap align=center ><? echo $a['designation1']; ?></td>
				<td nowrap align=center ><? echo $a['client']; ?></td>
				<td nowrap align=center ><? echo $a['etat']; ?></td>
				<td nowrap align=center ><? echo $a['preparateur']; ?></td>

				<td nowrap align=center >Cumul <? echo $annee; ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['marche_c_y'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['avt'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['facture_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['tcf'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['tps_c'],'',1,2); ?></td>
				<td nowrap align=center <? echo 'onclick="derouler_section(\'d_section_y_'.$r_affaire.'_'.$annee.'\','.$r_affaire.','.$annee.',\'y\');"'; ?> ><A><? echo nformat($t_tot_y['tot_c'],'',1,2); ?></A></td>
				<td align=center ><? echo nformat(div($t_tot_y['tot_c'],$t_tot_y['tps_c']),'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['achat_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['dep_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_d_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_p_c']-$t_tot_y['total_d_c'],'',1,2); ?></td>
				<td nowrap align=center ><? echo nformat($t_tot_y['total_p_c'],'',1,2); ?></td>
			</tr>
		
			<tr class=m3 >
				<td nowrap align=center ><? echo $r_affaire; ?></td>
				<td nowrap align=center ><? echo $a['designation1']; ?></td>
				<td nowrap align=center ><? echo $a['client']; ?></td>
				<td nowrap align=center ><? echo $a['etat']; ?></td>
				<td nowrap align=center ><? echo $a['preparateur']; ?></td>

				<td nowrap >Total affaire</td>
				<td nowrap ><? echo nformat($t_tot_g['marche_c_g'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['avt'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['facture_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_y_prec['tcf'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['tcf'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['tps_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['tot_c'],'',1,2); ?></td>
				<td ><? echo nformat(div($t_tot_g['tot_c'],$t_tot_g['tps_c']),'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['achat_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['dep_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_d_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_p_c']-$t_tot_g['total_d_c'],'',1,2); ?></td>
				<td nowrap ><? echo nformat($t_tot_g['total_p_c'],'',1,2); ?></td>
			</tr>
		
			<?
			}
		//echo '</table></td>';
		}

	}

///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////         impression des bons        ////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////


function bon_imprimer($of,$testing=0,$head=1,$phase_pp=0,$id_gamme=0,$break=0)
{
global $j_meta,$j_net,$j_net_n,$imprime;
if($head==1)$t = '
<html>
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
function printpage()
{
window.print();
}
-->
</script>
'.$j_meta.'
</head>
<body onload="printpage();">
<STYLE type=text/css>

table.entete{background-color: #ffffff;
        color:#000000;
	font-size: 12px;
	font-family: Arial;}

table{  border-style:solid;
	border-bottom-width:0;
	border-left-width:1;bon_imprimer
	border-right-width:0;
	border-top-width:1;
	border-color:000000;
	font-family: Arial;}

td{     border-style:solid;
	border-bottom-width:1;
	border-left-width:0;
	border-right-width:1;
	border-top-width:0;
	border-style:solid;
	border-color:000000;
	font-family: Arial;}

.titre{
   background-color: #ffffff;
   font-weight:bolder;
   color:#000000;
   font-family: Arial;
   font-size: 16;}

.print2{page-break-before:always;height:auto;page:auto;}
</style>';


$page=0;
$nb_page = 0;
$nb_phase_pp=5;

if(($testing ==1)and($id_gamme>0))
{
$req="select numero , id , tps_obj_u, gt_code , designation , notice , commentaire , '0' as clos,code_barre,code_barre_decoup from phase where id_gamme = '".$id_gamme."' order by numero asc";

}
elseif ($of>0)
{
if($imprime<>'')$req_plus = " and id in (".implode($imprime,',').")";

$req="select numero , id , tps_obj, gt_code , designation , notice , commentaire , clos ,code_barre,code_barre_decoup from phase_i where id_of = '".$of."' $req_plus order by numero asc";
}
else
{
echo "Aucun enregistrement trouvés";exit;
}

$ic=0;
$res2=my_query($req);
$nb_ligne=mysql_num_rows($res2);


//if(isset($phase_pp)){$nb_page = $nb_ligne;}else{$nb_page = ceil($nb_ligne / $nb_phase_pp);}

if ($nb_ligne==0){$t .= "<br>Aucun enregistrement trouvé<br>";exit;}

$barre_indication = '
  <table align=center class=entete border=1 cellspacing=0 cellpadding=3  width="700">
  <tr align=center>
  <td width=56>Phase</td>
  <td width=86>Poste</td>
  <td width=254>Intitulé</td>
  <td width=105>Visa / Nom</td>
  <td>Bon</td>
  </tr>
  </table><br>';

  
// entete sur page vierge pour supplychain ....
$corps[$nb_page] .= '';
$nb_page++;



while ($ligne=mysql_fetch_array($res2))
{

$code_barre_decoup=$ligne['code_barre_decoup'];
	
  if(($phase_pp>0)or(($ic % $nb_phase_pp)==0)or($code_barre_decoup == 1) )
  {
  $nb_page++;
  $ic=0;
  $corps[$nb_page] .= $barre_indication;

  }
 // if(isset($aff_tps_alloue))
   $tps_obj = $ligne["tps_obj"] ;

 // modif jallais pour pioch :
  if(($j_net_n == 7)or($j_net_n == 10)and(nombre_de("select id_cat from of where id = '".$of."'")==25))$tps_obj='';
  
  if($ligne["code_barre"]>0){$cb='<img src="pi_barcode.php?n=1&txt='.format_0($ligne["id"],8).'&visi=1">';}else{$cb='&nbsp;';}
  $corps_avant_notice = '<table align=center class=entete border=1 cellspacing=0 cellpadding=3  width="700">
	<tr align=center>
	<td rowspan=2 bgcolor="#dddddd" width=60><font size=4><b>'.$ligne["numero"].'</b></font></td>
	<td width=90  ><font size=4><b>'.$ligne["gt_code"].'</b></font></td>
	<td width=290  ><font size=4><b>'.$ligne["designation"].'</b></font>&nbsp;</td>
	<td width=130  >&nbsp;</td>
	<td width=130 >'.$cb.'</td>
	</tr>
	<tr align=center>
	<td align=left colspan=4><font size=2><b>';
  $corps_avant_notice_sans_des = '<table align=center class=entete border=1 cellspacing=0 cellpadding=3  width="700">
	<tr align=center>
	<td rowspan=2 bgcolor="#dddddd" width=60><font size=4><b>'.$ligne["numero"].'</b></font></td>
	</tr>
	<tr align=center>
	<td align=left colspan=4><font size=2><b>';
  $corps_apres_notice = '</b></font>&nbsp;</td>
	</tr>
	<tr align=center >
	<td><b>'.$tps_obj.'</b>&nbsp;</td>
	<td colspan="4" align=left><b>'.$ligne["commentaire"].'</b>&nbsp;</td>
	</tr>
	</table><br>';
	
	
	$motif = '__entete__';
	$motif2 = '__saut__';
	//$corps[$nb_page] .= $corps_avant_notice.str_replace($motif,$corps_apres_notice.bon_imprimer_entete($of,$page,$nb_page ,$qte,$testing,$j_net,1).$l.$barre_indication.$corps_avant_notice_sans_des,$ligne["notice"]).str_replace($motif2,$corps_apres_notice.'</p><p class=print2>'.$l.$corps_avant_notice_sans_des,$ligne["notice"]).$corps_apres_notice;
	$corps[$nb_page] .= $corps_avant_notice.str_replace($motif,$corps_apres_notice.bon_imprimer_entete($of,$page,'' ,$qte,$testing,$j_net,1).$l.$barre_indication.$corps_avant_notice_sans_des,$ligne["notice"]).$corps_apres_notice;

  if(((isset($phase_pp))or((($ic + 1) % $nb_phase_pp)==0)or($code_barre_decoup == 1))and($page > 1)){$corps[$nb_page] .= '</p>';}
  $ic++;
  $page+=1;
}
$page=1;
foreach($corps as $l){$t .= bon_imprimer_entete($of,$page,$nb_page ,$qte,$testing,$j_net,$break).$l;$page +=1;}
$t .= pied_page();
return $t;
}

function bon_imprimer_entete($of,$page,$nb_page ,$qte,$testing,$j_net,$break)
{
$j_entite = dbtodata("select id , nom from entite; ");

$sql="select of.poste ,of.id_entite, of.d_lancement, of.d_besoin , of.quantite , of.msn ,of.nt,of.i_n, of.numero_client , of.of_client ,  of.etat , of.dedoublement  , of.ref  , of.indice , of.designation , a.nom as avion , af.id , af.designation1 , af.type , gtd.nom as gt_delai
from of left join gamme as g on of.id_gamme = g.id 
left join piece_avion as a on of.id_avion = a.id
left join affaire as af on af.id = of.id_affaire 
left join gt_delai as gtd on gtd.id = g.id_gt_delai 
where of.id = '".$of."' ";

if(!($testing ==1))$res=my_query($sql);

if(!($testing>0))
	{
	$nb_ligne=mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	}
	
if (($page >1)or($break))$t= '<p class=print2>';
$qte = $row["quantite"];
if ($row["msn"] <> '')if($row["nt"] <> ''){$msn = ' ('.$row["msn"].'/'.$row["nt"].')';}else{$msn = ' ('.$row["msn"].')';}
if ($row["i_n"] <> ''){$i_n = 'IN : '.$row["i_n"]."<br>";}

if($nb_page>0){$nbp='Page '.$page."/".$nb_page;}else{$nbp='Suite page '.$page;}

return $t.'<TABLE align=center class=entete border=1 cellspacing=0 cellpadding=2 width="700">
	<TR align=center valign="center" >
		<TD colspan=2 height=80  ><img border=0 width=300 src="images/logo/'.$j_net.'/logo1.jpg"><br><b><font size=4>DOSSIER DE PRODUCTION</font></b></TD>
		<TD ><b><font size=6>Affaire '.$row["id"].'</font></b><br>'.$row["designation1"].'</td>
		<TD><div class=titre>OF <br><img src="pi_barcode.php?n=1&txt='.format_0($of,6).'&visi=1"></div></td>
	</TR>
	<TR height=50 align=center valign="center" >
		<TD>Avion : <div class=titre>'.$i_n.$row["avion"].$msn.'<br>'.$row["gt_delai"].'<br>'.$j_entite[$row["id_entite"]].'</div></td>
		<TD>Date de lancement : <div class=titre>'.datodf($row["d_lancement"]).'</div><br>Date de besoin : <div class=titre>'.datodf($row["d_besoin"]).'</div></td>
		<TD>Qte initiale : <b>'.$row["quantite"].'</b><br>Gamme : <div class=titre>'.$row["ref"].'</div><img src="pi_barcode.php?n=8&txt='.$row["ref"].'"></td>
		<TD>n° poste : <div class=titre>'.format_0($row["poste"],4).'</div><br>indice : <div class=titre>'.$row["indice"].'</div></td>
	</TR>
	<TR height=50 align=center valign="center" >
		<TD>OF client : <br><img src="pi_barcode.php?n=7&txt='.$row["of_client"].'&visi=1"></td>
		<TD colspan=2 align=left>Désignation : <div class=titre>'.$row["designation"].'</div></td>
		<TD>'.$nbp.'</td>
	</TR>
</table>
<br>';


}



///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////         rapprochement              ////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

function rapprochement_ps()
{
//clean
$req="SELECT ora.*
	FROM of_rapprochement ora
	LEFT JOIN of ON of.id=ora.id_of
	LEFT JOIN affaire_prev ap ON ap.id=ora.id_affaire_prev
	WHERE ((of.numero_client <> ap.n_cde_c OR of.poste <> ap.poste) AND of.numero_client <> '' AND of.poste <> '0') OR of.of_client <> ap.of_client";
$res=my_query($req);
while($ligne=mysql_fetch_array($res))
	{
	my_query('update of set is_rapprochement = is_rapprochement - '.$ligne["qte"].' where id = '.$ligne["id_of"]);
	my_query('update affaire_prev set is_rapprochement = is_rapprochement - '.$ligne["qte"].' where id = '.$ligne["id_affaire_prev"]);
	my_query('delete from of_rapprochement  where id = '.$ligne["id"]);
	}
	
	
$req="SELECT ora.* , sum(of.quantite_ok) as qte_of, sum(ora.qte)as qte_ora , sum(of.is_rapprochement)as qte_ofr
	FROM of_rapprochement ora
	LEFT JOIN of ON of.id=ora.id_of
	group by of.id
	having qte_of < qte_ora ";
	
$res=my_query($req);
while($ligne=mysql_fetch_array($res))
	{
	my_query('update of set is_rapprochement = 0 where id = '.$ligne["id_of"]);
	my_query('update affaire_prev set is_rapprochement = is_rapprochement - '.$ligne["qte"].' where id = '.$ligne["id_affaire_prev"]);
	my_query('delete from of_rapprochement  where id = '.$ligne["id"]);
	}
//fin clean

	$req="SELECT of.id,of.ref,of.of_client,of.numero_client,of.poste,of.quantite_ok,of.id_fab,of.is_rapprochement
		FROM of
		WHERE of.id_affaire_type=1 AND of.etat=2 AND of.is_rapprochement<>of.quantite_ok AND (of.of_client>0 OR (of.poste>0 and of.numero_client<>'')) AND quantite_ok>0
		ORDER BY of.d_fin_sap";
	$res=my_query($req);
	$nb_ligne=mysql_num_rows($res);
	//if($nb_ligne==0) {echo "<BR>Aucun enregistrement trouvé<BR>";exit;}
	while($ligne=mysql_fetch_array($res))
		{
		$t[$ligne["id"]]["id"]=$ligne["id"];
		$t[$ligne["id"]]["ref"]=$ligne["ref"];
		$t[$ligne["id"]]["id_fab"]=$ligne["id_fab"];
		$t[$ligne["id"]]["of_client"]=$ligne["of_client"];
		$t[$ligne["id"]]["numero_client"]=$ligne["numero_client"];
		$t[$ligne["id"]]["poste"]=$ligne["poste"];
		$t[$ligne["id"]]["quantite_ok"]=floor($ligne["quantite_ok"]);
		$t[$ligne["id"]]["is_rapprochement"]=$ligne["is_rapprochement"];
		$t[$ligne["id"]]["qte_reste"]=floor($ligne["quantite_ok"]-$ligne["is_rapprochement"]);
		$t[$ligne["id"]]["maj"]=0;
		}
	$req="SELECT ap.id,ap.qte,ap.n_cde_c,ap.poste,ap.of_client as of_client_ap,ap.is_rapprochement
		FROM affaire_prev ap
		WHERE ap.is_rapprochement<>ap.qte AND ap.prev_ps = 1 and year(d_cde) >= 2014
		ORDER BY ap.d_cde";
	$res=my_query($req);
	$nb_ligne=mysql_num_rows($res);
	//if($nb_ligne==0) {echo "<BR>Aucun enregistrement trouvé<BR>";exit;}
	while($ligne=mysql_fetch_array($res))
		{
		$tap[$ligne["id"]]["id"]=$ligne["id"];
		$tap[$ligne["id"]]["qte"]=floor($ligne["qte"]);
		$tap[$ligne["id"]]["n_cde_c"]=$ligne["n_cde_c"];
		$tap[$ligne["id"]]["poste"]=$ligne["poste"];
		$tap[$ligne["id"]]["of_client_ap"]=$ligne["of_client_ap"];
		$tap[$ligne["id"]]["is_rapprochement"]=$ligne["is_rapprochement"];
		$tap[$ligne["id"]]["qte_dispo"]=floor($ligne["qte"]-$ligne["is_rapprochement"]);
		$tap[$ligne["id"]]["maj"]=0;
		// tab indice "a la phase"
		if($ligne["of_client_ap"]>0)$t_ofc[$ligne["of_client_ap"]][$ligne["id"]]=$ligne["id"];
		// tab indice FC
		if($ligne["n_cde_c"]>0)$t_cdec[$ligne["n_cde_c"].'#'.$ligne["poste"]][$ligne["id"]]=$ligne["id"];
		}
	$i=0;
	
	// t : tableau des OF
	foreach($t as $k => $l)
		{
		//echo 'L => un of';
		//print_r2($l);
		$t2='';
		// a la phase
		if($l["id_fab"]==2)
			{
			$t2 = $t_ofc[$l["of_client"]];	
			}
			else //facon complete
			{
			$t2 = $t_cdec[$l["numero_client"].'#'.$l["poste"]];	
			}
		//echo 't2 => correspondance dans tap (t2)';
		//print_r2($t2);
		foreach($t2 as $k2 => $id_tap)
			{
			//echo 'tap L2';
			//print_r2($tap[$id_tap]);
			//si il me reste (of) de la qte_reste
			if($t[$k]["qte_reste"]>0)
				{
				//si ap.qte >= of.qte_ok
				if($tap[$id_tap]["qte_dispo"]>=$t[$k]["qte_reste"])
					{
					$ofr[$i]["cde_client"]=$t[$k]["numero_client"];
					$ofr[$i]["poste"]=$t[$k]["poste"];
					$ofr[$i]["of_client"]=$t[$k]["of_client"];
					$ofr[$i]["qte"]=$t[$k]["qte_reste"];
					$ofr[$i]["id_of"]=$t[$k]["id"];
					$ofr[$i]["id_affaire_prev"]=$id_tap;
					
					//echo "A ".$tap[$id_tap]["qte_dispo"]." - ".$id_tap."<br>";
					$tap[$id_tap]["qte_dispo"]-=$t[$k]["qte_reste"];
					
					$t[$k]["is_rapprochement"]+=$t[$k]["qte_reste"];
					$t[$k]["maj"]+=1;
					$tap[$id_tap]["is_rapprochement"]+=$t[$k]["qte_reste"];
					$tap[$id_tap]["maj"]+=1;
					
					$t[$k]["qte_reste"]=0;
					}
				// sinon si ap.qte > 
				elseif($tap[$id_tap]["qte_dispo"]>0)
					{
					$ofr[$i]["cde_client"]=$t[$k]["numero_client"];
					$ofr[$i]["poste"]=$t[$k]["poste"];
					$ofr[$i]["of_client"]=$t[$k]["of_client"];
					$ofr[$i]["qte"]=$tap[$id_tap]["qte_dispo"];
					$ofr[$i]["id_of"]=$t[$k]["id"];
					$ofr[$i]["id_affaire_prev"]=$id_tap;
					
					//echo "B ".$tap[$id_tap]["qte_dispo"]." - ".$id_tap."<br>";
					$t[$k]["qte_reste"]-=$tap[$id_tap]["qte_dispo"];
					
					$t[$k]["is_rapprochement"]+=$tap[$id_tap]["qte_dispo"];
					$t[$k]["maj"]+=1;
					$tap[$id_tap]["is_rapprochement"]+=$tap[$id_tap]["qte_dispo"];
					$tap[$id_tap]["maj"]+=1;
					$tap[$id_tap]["qte_dispo"]=0;
					
					if(($ofr[$i]["qte"]<1)and($ofr[$i]["qte"]>0)){
					print_r2($tap[$id_tap]);
					print_r2($t[$k]);
					print_r2($ofr[$i]);
					exit;}
					}
				}
			$i++;
			}
		//if($i>20)break;
		}
		//print_r2($t_ofc);
		//print_r2($t_cdec);
	foreach($ofr as $l)
		{
		//print_r2($ofr);
		$vf = new valid_form ;
		$vf->add("cde_client", $l["cde_client"]);
		$vf->add("poste", $l["poste"]);
		$vf->add("of_client", $l["of_client"]);
		$vf->add("qte", $l["qte"]);
		$vf->add("id_of", $l["id_of"]);
		$vf->add("id_affaire_prev", $l["id_affaire_prev"]);
		$vf->insert("of_rapprochement");			
		}
	// maj des of
	foreach($t as $l)
		{
		if($l["maj"]>0)my_query('update of set is_rapprochement = '.$l["is_rapprochement"].' where id = '.$l["id"]);
		}
	// maj des affaire_prev
	foreach($tap as $l)
		{
		if($l["maj"]>0)my_query('update affaire_prev set is_rapprochement = '.$l["is_rapprochement"].' where id = '.$l["id"]);
		}
}
?>
