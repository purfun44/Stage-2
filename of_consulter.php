<?
include("fonction.php");
include("fonction_affaire.php");

if (!d_ok(201)) {header("location: accueil.php");exit;}

$_SESSION['en_cour']="of_consulter.php";

$j_entite=dbtodata("SELECT id,nom FROM entite");

if($parent_id>0) {$_SESSION[$_SESSION['en_cour']]=id2url($parent_id);}
else if(($parent_ori==1)or($_SESSION[$_SESSION['en_cour']]=="")) {$_SESSION[$_SESSION['en_cour']]=parent(201);}

if($id_affaire>0)$_SESSION['affaire_en_cour']=$id_affaire;

if(url2id($_SESSION[$_SESSION['en_cour']])==950) $_SESSION['affaire_en_cour']=0;

$d_aff=d_ok(201);

if($del_id>0)
	{
	supprime_of($del_id);
	}

if(($prev>0) or ($next>0))
	{
	$util=$_SESSION['affaire_en_cour'];
	if($prev>0)$cl=" asc ";
	if($next>0)$cl=" desc ";
	$sql="SELECT id FROM affaire WHERE 1 ".$_SESSION["affaire_afficher_req"]." ORDER BY ".$_SESSION['affaire_trier']." ".$cl ;
	$util_0=$util;
	if($res=my_query($sql))
		{
		while($row2=mysql_fetch_array($res))
			{
			if($util==$row2[0])
				{
				$util=$util_0;
				break;
				}
			$util_0=$row2[0];
			}
		}
	$_SESSION['affaire_en_cour']=$util;
	}
if(isset($r_tout)) $r_tout=1;

if($mode=="9")
	{
	$r_aff="";
	$r_of="";
	$r_avion="";
	$r_ref="";
	$r_des="";
	$r_nc="";
	$r_of_client="";
	$r_date="";
	$r_date2="";
	$r_sap="";
	$r_sap2="";
	$r_etat="0";
	$r_red="0";
	$r_msn="0";
	$r_id_cat=0;
	$r_id_famille=0;
	$r_urgence=null;
	$r_n_lot='';
	$r_tout=0;
	$r_entite=0;
	$mode="1";
	$r_fab="0";
	}

if($mode=="1")
	{
	$_SESSION['of_consulter_aff']=$r_aff;
	$_SESSION['of_consulter_of']=$r_of;
	$_SESSION['of_consulter_avion']=$r_avion;
	$_SESSION['of_consulter_ref']=$r_ref;
	$_SESSION['of_consulter_des']=$r_des;
	$_SESSION['of_consulter_nc']=$r_nc;
	$_SESSION['of_consulter_of_client']=$r_of_client;
	$_SESSION['of_consulter_date']=$r_date;
	$_SESSION['of_consulter_date2']=$r_date2;
	$_SESSION['of_consulter_sap']=$r_sap;
	$_SESSION['of_consulter_sap2']=$r_sap2;
	$_SESSION['of_consulter_etat']=$r_etat;
	$_SESSION['of_consulter_red']=$r_red;
	$_SESSION['of_consulter_msn']=$r_msn;
	$_SESSION['of_consulter_id_cat']=$r_id_cat;
	$_SESSION['of_consulter_id_famille']=$r_id_famille;
	$_SESSION['of_consulter_poste']=$r_poste;
	$_SESSION['of_consulter_urgence']=$r_urgence;
	$_SESSION['of_consulter_n_lot']=$r_n_lot;
	$_SESSION['of_consulter_tout'] = $r_tout;
	$_SESSION['of_consulter_entite']=$r_entite;
	$_SESSION['of_consulter_fab']=$r_fab;
	$p_en=1;
	}

if($p_en>0) $_SESSION["of_consulter_p_en"]=$p_en;
if($mode>0) {$p_en=1;} else {$p_en=$_SESSION["of_consulter_p_en"];}
if(!($p_en>0)) $p_en=1;
$_SESSION["of_consulter_p_en"]=$p_en;

$of_consulter_req="";

$r_aff=$_SESSION['of_consulter_aff'];
$r_of=$_SESSION['of_consulter_of'];
$r_avion=$_SESSION['of_consulter_avion'];
$r_ref=$_SESSION['of_consulter_ref'];
$r_des=$_SESSION['of_consulter_des'];
$r_nc=$_SESSION['of_consulter_nc'];
$r_of_client=$_SESSION['of_consulter_of_client'];
$r_date=$_SESSION['of_consulter_date'];
$r_date2=$_SESSION['of_consulter_date2'];
$r_sap=$_SESSION['of_consulter_sap'];
$r_sap2=$_SESSION['of_consulter_sap2'];
$r_etat=$_SESSION['of_consulter_etat'];
$r_red=$_SESSION['of_consulter_red'];
$r_msn=$_SESSION['of_consulter_msn'];
$r_id_cat=$_SESSION['of_consulter_id_cat'];
$r_id_famille=$_SESSION['of_consulter_id_famille'];
$r_poste=$_SESSION['of_consulter_poste'];
$r_urgence=$_SESSION['of_consulter_urgence'];
$r_n_lot=$_SESSION['of_consulter_n_lot'];
$r_tout=$_SESSION['of_consulter_tout'];
$r_entite=$_SESSION['of_consulter_entite'];
$r_fab=$_SESSION['of_consulter_fab'];

if($_SESSION['affaire_en_cour']>0) {$of_consulter_req.=" AND id_affaire = ".$_SESSION['affaire_en_cour'];}
else
	{
	$of_consulter_req.=" AND of.id_affaire_type = 1 ";
	if ($r_aff > 0) $of_consulter_req .= " AND of.id_affaire in ($r_aff) ";
	}

if($r_of>0) $of_consulter_req.=" AND of.id in ($r_of) ";
if(is_array($r_entite)) $of_consulter_req.=" AND of.id_entite in ('".tabtosql($r_entite)."') ";
if(is_array($r_avion)) $of_consulter_req.=" AND of.id_avion in ('".tabtosql($r_avion)."') ";
if($r_ref!="") $of_consulter_req.=" AND of.ref like '$r_ref%' ";
if($r_des!="") $of_consulter_req.=" AND of.designation like '%$r_des%' ";
if($r_nc!="") $of_consulter_req.=" AND of.numero_client like '$r_nc%' ";
if($r_of_client!="") $of_consulter_req.=" AND of.of_client like '$r_of_client%' ";
//if($r_etat!="")$of_consulter_req.=" AND of.etat = '$r_etat' ";
if(is_array($r_etat)) $of_consulter_req.=" AND of.etat in ('".tabtosql($r_etat)."') ";
if($r_red>0) $of_consulter_req.=" AND of.id_redacteur = '$r_red' ";
if($r_msn>0) $of_consulter_req.=" AND of.msn like '$r_msn%' ";
if($r_id_cat>0) $of_consulter_req.=" AND of.id_cat = '$r_id_cat' ";
if($r_id_famille>0) $of_consulter_req.=" AND of.id_famille = '$r_id_famille' ";
if($r_poste!='') $of_consulter_req.=" AND of.poste like '%$r_poste%' ";
if($r_urgence!=null) $of_consulter_req.=" AND of.urgence != '0000-00-00' ";
if($r_n_lot!='') $of_consulter_req.=" AND of.n_lot like '%$r_n_lot%' ";
if((isdf($r_date)) and (isdf($r_date2))) {$of_consulter_req.=" AND ((of.d_lancement>='".dftoda($r_date)."') AND (of.d_lancement<='".dftoda($r_date2)."')) ";}
else if(isdf($r_date)) {$of_consulter_req.=" AND of.d_lancement='".dftoda($r_date)."' ";}

if((isdf($r_sap)) and (isdf($r_sap2))) {$of_consulter_req.=" AND (( of.d_fin_sap>='".dftoda($r_sap)."') AND ( of.d_fin_sap<='".dftoda($r_sap2)."')) ";}
else if(isdf($r_sap)) {$of_consulter_req.=" AND of.d_fin_sap='".dftoda($r_sape)."' ";}
if(is_array($r_fab))$of_consulter_req.=" AND of.id_fab in (".tabtosql($r_fab).") ";

if($_SESSION['of_consulter_trier']=='') {$_SESSION['of_consulter_trier']='of.id ';}
if(isset($trier)) {$_SESSION['of_consulter_trier']=$trier;}

if($_SESSION['of_consulter_ordre']=='') {$_SESSION['of_consulter_ordre']='desc ';}
if(isset($ordre)) {$_SESSION['of_consulter_ordre']=$ordre;}

$nav=1;
if($_SESSION['affaire_en_cour']>0)
	{
	$res=my_query("select * from affaire where id=".$_SESSION['affaire_en_cour']);
	$row=mysql_fetch_array($res);
	$affaire_type= $row["type"];
	$affaire_etat= $row["etat"];
	$affaire_regroupement=$row["regroupement"];
	$txt_head=" de l'affaire n° ".format_0($_SESSION['affaire_en_cour'],5);
	$nav=0;
	}

$tf=new tri;
$tf->tri_ec=$_SESSION['of_consulter_trier'];
$tf->ordre_ec=$_SESSION['of_consulter_ordre'];
$tf->page_ec=$_SESSION['en_cour'];
$parents=201;
//if (($id_util==7) or ($id_util==10)) $parents=131;
$page=new page;
$page->head("Liste des OF".$txt_head);
$page->body();
$page->entete("Liste des OF".$txt_head);
$page->add_button(1,$nav,'of_consulter.php?util='.$_SESSION['affaire_en_cour'].'&prev=1');
$page->add_button(2,1,$_SESSION[$_SESSION['en_cour']]);
$page->add_button(3,$nav,'of_consulter.php?util='.$_SESSION['affaire_en_cour'].'&next=1');
$page->add_button(0,2);
$page->add_button(125,1,"of_import_cmd.php", "Importer une commande");

if($_SESSION['affaire_en_cour']>0)
	{
	if(d_ok(202) and $affaire_etat<4 and $affaire_regroupement==0) $page->add_button(5,1,"of_ajouter.php?new=1&parent_id=201&type=".$affaire_type,"Ajouter un OF");
	$page->add_button(0,2);
	if(d_ok(133)) $page->add_button(61,1,"affaire.php","Consulter le détail de l'affaire n° ".$_SESSION['affaire_en_cour']);
	$page->add_button(0,2);
	if((d_ok(235)) and (($affaire_type)>1) and $affaire_etat<4) $page->add_button(11,1,"affaire_avt.php?parent_ori=1&mode=1&num_af=".$_SESSION['affaire_en_cour'],"Avancement de l'affaire n° ".$_SESSION['affaire_en_cour']);
	if((d_ok(236)) and (($affaire_type)>1) and $affaire_etat<4) $page->add_button(29,1,"affaire_avt_achat.php","Avancement des achats de l'affaire n° ".$_SESSION['affaire_en_cour']);
	}
	$page->add_button(0,2);
	$page->add_button(23,1,'of_import_urgence.php',"Importer la liste des OF urgents");
$page->fin_entete();
$page->datescript();

if($print<>1)
	{
	?>
	<script LANGUAGE="JavaScript">
		menu=new Array()
		<?
		$corr="menu_corr=new Array()\n\n";
		$i=0;
		$corr.='menu_corr[0]='.$i.";\n";

		echo "menu[$i]=new Array()\n\n";
		$j=0;
		echo 'menu['.$i.']['.$j.']=new Option("","")'."\n";
		$j++;
		$res2=my_query("SELECT id,nom FROM gamme_cat ORDER BY nom ");
		while($row2=mysql_fetch_array($res2))
			{
			if($row2[0]==$r_id_famille) {$select_cat='<script LANGUAGE="JavaScript">changecat(); document.formulaire.r_id_famille.options['.$j.'].selected=true;</script>';}
			echo 'menu['.$i.']['.$j.']=new Option("'.$row2[1].'","'.$row2[0].'")'."\n";
			$j++;
			}
		echo "\n\n\n";
		$s.='<OPTION '.$selected.' VALUE=""></OPTION>'."\n";
		$i++;
		$res=my_query("SELECT id,nom FROM gamme_cat ORDER BY nom");
		while($row=mysql_fetch_array($res))
			{
			$corr.='menu_corr['.$row[0].']='.$i.";\n";
			if($r_id_cat==$row[0]) {$selected=' selected ';} else {$selected='';}
			$s.='<OPTION '.$selected.' VALUE="'.$row[0].'">'.$row[1].'</OPTION>'."\n";
			echo "menu[$i]=new Array()\n\n";
			$j=0;
			echo 'menu['.$i.']['.$j.']=new Option("","")'."\n";
			$j++;
			$res2=my_query("SELECT id, nom FROM gamme_famille WHERE id_cat='".$row[0]."' ORDER BY nom ");
			while($row2=mysql_fetch_array($res2))
				{
				if($row2[0]==$r_id_famille) {$select_cat = '<script LANGUAGE="JavaScript">changecat(); document.formulaire1.r_id_famille.options['.$j.'].selected=true;</script>';}
				echo 'menu['.$i.']['.$j.']=new Option("'.$row2[1].'","'.$row2[0].'")'."\n";
				$j++;
				}
			echo "\n\n\n";
			$i++;
			}
		echo $corr;
		?>
		function changecat()
			{
			numeroMenu=menu_corr[document.formulaire1.r_id_cat.options[document.formulaire1.r_id_cat.selectedIndex].value];
			f=document.formulaire1.r_id_famille;
			for(i=f.options.length-1;i>0;i--)
				{
				f.options[i]=null
				}
			for(i=0;i<menu[numeroMenu].length;i++)
				{
				f.options[i]=new Option(menu[numeroMenu][i].text,menu[numeroMenu][i].value)
				}
			f.selectedIndex=0
			}
	</script>
	<CENTER>
	<form style="position:relative;z-index:1;" method="post" name="formulaire1" action="of_consulter.php?mode=1"  target="principal">

		<table  class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
			<tr >
				<td class= "m3"  >

				<? if($_SESSION['affaire_en_cour']==0){?>
				Affaire :
				<input type=text name="r_aff" size=6 maxlength=6 value="<? echo $r_aff;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;
				<?}?>

				OF :
				<input type=text name="r_of" size=6 maxlength=6 value="<? echo $r_of;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp; 

				Ref. :
				<input type=text name="r_ref" size=20 maxlength=50 value="<? echo $r_ref;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;

				Des. :
				<input type=text name="r_des" size=20 maxlength=50 value="<? echo $r_des;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;

				N°Lot. :
				<input type=text name="r_n_lot" size=20 maxlength=50 value="<? echo $r_n_lot;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp; 

				Cde Client :
				<input type=text name="r_nc" size=10 maxlength=10 value="<? echo $r_nc;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;

				OF client :
				<input type=text name="r_of_client" size=10 maxlength=10 value="<? echo $r_of_client;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;

				Poste :
				<input type=text name="r_poste" size=4 maxlength=4 value="<? echo $r_poste;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp;

				MSN :
				<input type=text name="r_msn" size=10 maxlength=10 value="<? echo $r_msn;?>" onchange="document.formulaire1.submit();" >
				&nbsp; &nbsp; 


				<?
				echo liste_ms($j_of_etat,$r_etat,"r_etat", "Etat");
				?>
				&nbsp; &nbsp; &nbsp;

				<label><input class=m3 type=checkbox name="r_tout" <? if ($r_tout == 1)echo 'checked';?> > Sur 1 page</label> &nbsp; &nbsp;

				</td>
				<td rowspan=2  class= "m3">
				<input type=submit id=button3 value="Go"><br />
				<input id=button3 type=button value="Clear" onclick="document.location.href='of_consulter.php?mode=9';">
				</td>
			</tr>
			<tr>
				<td  class= "m3"  >
					<? 
					echo liste_ms("select id , nom from entite order by nom asc ",$r_entite,"r_entite" ,"UAP Gamme");
					?>
					&nbsp; &nbsp; &nbsp;
					Catégorie : <SELECT id=button  style="width:100;" NAME="r_id_cat"  SIZE=1 onChange="changecat()"><? echo $s;?></SELECT>
					&nbsp; &nbsp; &nbsp;
					Famille : <SELECT  id=button style="width:100;" NAME="r_id_famille" onchange="document.formulaire1.submit();"><OPTION VALUE="0"></OPTION></SELECT><? echo $select_cat;?>
					&nbsp; &nbsp; &nbsp;
					<?
					echo liste_ms("select id , nom from piece_avion order by nom asc",$r_avion,"r_avion","Avion");
					?>
					&nbsp; &nbsp; &nbsp;
					Date &nbsp;
					<INPUT onclick="return showCalendar('sel3','%d/%m/%Y');"  id=sel3 size=11 type="text"  name="r_date" value="<? echo $r_date; ?>"><? echo calendar('sel3');?>
					&nbsp; &nbsp; à &nbsp; &nbsp;
					<INPUT onclick="return showCalendar('sel4','%d/%m/%Y');"  id=sel4 size=11 type="text"  name="r_date2" value="<? echo $r_date2; ?>"><? echo calendar('sel4');?>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
					Date fin SAP&nbsp;
					<INPUT onclick="return showCalendar('sel5','%d/%m/%Y');"  id=sel5 size=11 type="text"  name="r_sap" value="<? echo $r_sap; ?>"><? echo calendar('sel5');?>
					&nbsp; &nbsp; à &nbsp; &nbsp;
					<INPUT onclick="return showCalendar('sel6','%d/%m/%Y');"  id=sel6 size=11 type="text"  name="r_sap2" value="<? echo $r_sap2; ?>"><? echo calendar('sel6');?>
					&nbsp; &nbsp;
					<INPUT type="checkbox" id="r_urgence" name="r_urgence" <? if($r_urgence != null){echo 'checked';}?> /><label for="r_urgence">Urgence</label>&nbsp; &nbsp;
					<?
					echo liste_ms("SELECT id,nom FROM gamme_fab GROUP BY id",$r_fab,"r_fab","Par fabrication");
					//echo "Rédacteur : ".liste_db("select of.id_redacteur , concat(i.nom,' ',i.prenom) from of left join interne as i on of.id_redacteur = i.id group by of.id_redacteur order by i.nom asc",$r_red,"r_red" ,' onchange="formulaire1.submit();"');
					?>
				</td>
			</tr>
		</table>
	</form>
	<?if($r_id_famille==0){?>
		<script LANGUAGE = "JavaScript">
			changecat();
		</script>
	<?}?>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	<script language="JavaScript" src="js/overlib.js"></script>
	<?
	}
function entete()
	{
	global $tf , $of_consulter_req;
	if($_SESSION['affaire_en_cour']>0)$qte_req=" and id_affaire = ".$_SESSION['affaire_en_cour']." ";
	$qte=" : ".nombre_de("select sum(quantite_ok) from of where 1 $qte_req $of_consulter_req ");
	?>
	<table class=forumline cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
	<tr >
	<td class= "m3" width=1% height=23><? $tf->aff("of.lu","LU");?></td>
	<td class= "m3" width=4% height=23><? $tf->aff("of.id_affaire","Affaire");?></td>
	<td class= "m3" width=4% height=23><? $tf->aff("of.id","OF");?></td>
	<td class= "m3" width=8% ><? $tf->aff("of.ref","Référence");?></td>
	<td class= "m3" width=3% ><? $tf->aff("of.indice","Ind");?></td>
	<td class= "m3" width=18% ><? $tf->aff("of.designation","Designation");?></td>
	<td class= "m3" width=4% ><? $tf->aff("of.quantite","Qté ini");?></td>
	<td class= "m3" width=4% ><? $tf->aff("of.quantite_ok","Qté ok".$qte );?></td>
	<td class= "m3" width=4% ><? $tf->aff("rebuts","Rebuts");?></td>
	<td class= "m3" width=5% ><? $tf->aff("of.numero_client","Cde Client");ajax(1);?></td>
	<td class= "m3" width=5% ><? $tf->aff("of.poste","Poste");ajax(1);?></td>
	<td class= "m3" width=5% ><? $tf->aff("of.of_client","OF client");ajax(1);?></td>
	<td class= "m3" width=5% ><? $tf->aff("of.msn","MSN");?></td>
	<td class= "m3" width=7% ><? $tf->aff("of.d_lancement","Début");?></td>
	<td class= "m3" width=7% ><? $tf->aff("of.d_fin_sap","Fin SAP");?></td>
	<td class= "m3" width=7% ><? $tf->aff("of.id_entite","UAP");?></td>
	<td class= "m3" width=2% ><img src="images/cell_layout.png"></td>
	<td class= "m3" width=2% ><? $tf->aff("of.etat","Etat");?></td>
	</tr>


	<script LANGUAGE="JavaScript" src="js/ajax.js"></script>
	<script LANGUAGE="JavaScript" src="js/Update.js"></script>
	<script LANGUAGE="JavaScript" >

	var upd_numero_client = new Update("upd_numero_client","of","numero_client","id");
	upd_numero_client.setParam(" maxlength=20");
	var upd_of_client = new Update("upd_of_client","of","of_client","id");
	upd_of_client.setParam(" maxlength=20");
	var upd_poste = new Update("upd_poste","of","poste","id");
	upd_poste.setParam("size=4 , maxlength=4");


	</script>
	<?
	}
$req="select * from of where 1 $of_consulter_req order by ".$_SESSION['of_consulter_trier']." ".$_SESSION['of_consulter_ordre']." , of.id desc ";
$_SESSION["of_sql"]=$req;
$ic=0;
$res=my_query($req);
$ligne_page=$lpp-8 ;                //ligne par page
$p_pf=20;  			     //page par feuille

$nb_ligne=mysql_num_rows($res);
if ($nb_ligne==0){echo "<br>Aucun enregistrement trouvé<br>";exit;}
$prem_ligne=(($p_en - 1) * $ligne_page);
mysql_data_seek($res,0);
mysql_data_seek($res,$prem_ligne);

while ($ligne=mysql_fetch_array($res))
	{
	$type_h=$ligne["type_h"];
	$id="<b>".format_0($ligne["id"],6)."</b>";
	if ($d_aff)$id='<a class="b" href="of_ajouter.php?util='.$ligne["id"].'&parent_id=201">'.format_0($ligne["id"],6).'</a>';
	if($ligne["etat"]==0){$etat_of=0;}else if($ligne["etat"]==1){$etat_of=2;}else if($ligne["etat"]==2){$etat_of=1;}

	if($ic == 0){entete();}
	
	$observation = "";
	if($ligne["observation"]<> "")$observation = '<img style="cursor:pointer;" onmouseover="return overlib(\''.addslashes(to_txt($ligne["observation"])).'\', CAPTION,\'&nbsp;<img src=images/info.png> Observations :\' );" onmouseout="return nd();" src="images/cell_layout.png" >';
	
	if($ligne["etat"] > 1){$rebuts=$ligne["quantite"] -  $ligne["quantite_ok"];}else{$rebuts=0;} 
	
	if($ligne["id_fab"]==2)
		{
		$facon_c="class=cel2";
		$phase="class=m3";
		}
	elseif($ligne["id_fab"]==1)
		{
		$facon_c="class=m3";
		$phase="class=cel2";
		}
	else
		{
		$facon_c="class=cel2";
		$phase="class=cel2";
		}
	?>
	<tr>
		<td align=center class=cel2 ><img src="images/drapo_<? echo $ligne["lu"];?>.gif"></td>
		<td align=center class=cel1 ><? echo $ligne["id_affaire"];?></td>
		<td align=center class=cel2 ><? echo $id;?></td>
		<td align=center class=cel1 ><? echo $ligne["ref"];?></td>
		<td align=center class=cel2 ><? echo $ligne["indice"];?></td>
		<td align=center class=cel1 ><? echo $ligne["designation"];?></td>
		<td align=center class=cel2 ><? echo $ligne["quantite"];?></td>
		<td align=center class=cel2 ><? echo $ligne["quantite_ok"];?></td>
		<td align=center class=cel2 ><? echo $rebuts;?></td>
		<td align=center <? echo $facon_c;?> ondblclick=upd_numero_client.form(this,<? echo $ligne['id'];?>)><? echo $ligne["numero_client"];?></td>
		<td align=center <? echo $facon_c;?> ondblclick=upd_poste.form(this,<? echo $ligne['id'];?>)><? echo $ligne["poste"];?></td>
		<td align=center <? echo $phase;?> ondblclick=upd_of_client.form(this,<? echo $ligne['id'];?>)><? echo $ligne["of_client"];?></td>
		<td align=center class=cel1 ><? echo $ligne["msn"];?></td>
		<td align=center class=cel2 ><? echo datodf($ligne["d_lancement"]);?></td>
		<td align=center class=cel2 ><? echo datodf($ligne["d_fin_sap"]);?></td>
		<td align=center class=cel1 ><? echo $j_entite[$ligne["id_entite"]];?></td>
		<td align=center class=cel2 ><? echo $observation; ?></td>
		<td align=center class=cel2 ><img title="<? echo $j_of_etat[$ligne["etat"]]; ?>" src="images/statut<? echo $etat_of;?>.gif"></td>
	</tr>
	<?
	$ic++;
	if(($r_tout == 0 ) and ($ic == $ligne_page)) {break;}
	}


if ($r_tout == 0 )echo bar("of_consulter.php",'',$p_en,$nb_ligne,$ligne_page);

?>
</table>
</center>
<br><br>
<?
echo pied_page();
?>
