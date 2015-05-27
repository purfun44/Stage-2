<?
include("fonction.php");
if (!d_ok(950)){header("location: accueil.php");exit;}
$_SESSION['en_cour']="gestion_piece_serie.php";

$page = new page;
$page->head("Gestion des Pièces Série");
$page->body("");
$page->entete("Gestion des Pièces Série");
$page->add_button(1,0);
$page->add_button(2,1,parent(950));
$page->add_button(3,0);
$page->add_button(0,2);
if(d_ok(101))$page->add_button(17,1,"piece_consulter.php","Gestion des références");

$page->fin_entete();

function aff_sep($name,$id=0)
	{
	echo'<tr>
		<td colspan=2>
			<table  class=m3 cellSpacing=1 cellPadding=2 width="100%" align=center border=0 >
				<tr>
					<td onclick="javascript:DivStatus( \'t_aff_'.$id.'\' );" width=5% class ="m3"><img id="imgt_aff_'.$id.'" src="images/bas.gif"></td>
					<td align=center class ="m3" onclick="javascript:DivStatus( \'t_aff_'.$id.'\' );" >'.$name.'</td>
				</tr>
			</table>
		</td>
	</tr>';
	}
?>
<style type="text/css" media="all">
	.cachediv {
		display: none;
	}
</style>

<script src="/js/ajax.js" type="text/javascript"></script>

<script LANGUAGE="JavaScript">
	function DivStatus(divID)
		{
		Pdiv=document.getElementById( divID );
		img=document.getElementById('img'+divID );
		Pdiv.className=( Pdiv.className=='cachediv') ? '' : 'cachediv';
		if(img)
			{
			var im=img.src.substr(img.src.length-14,img.src.length);
			if((im=='ode_Closed.gif') || (im=='/Node_Open.gif'))
			img.src=(im=='ode_Closed.gif') ? 'images/menu/Node_Open.gif' : 'images/menu/Node_Closed.gif';
			
			if((im=='ast_Closed.gif') || (im=='_Last_Open.gif'))
			img.src=(im=='ast_Closed.gif') ? 'images/menu/Node_Last_Open.gif' : 'images/menu/Node_Last_Closed.gif';
			if((im=='images/bas.gif') || (im=='mages/haut.gif'))
				{
				if(im=='images/bas.gif')
					{
					img.src='images/haut.gif';
					EcrireCookie(divID,1)
					}
				else
					{
					EcrireCookie(divID,0)
					img.src='images/bas.gif';
					}
				}

			}

		}
	<!--
	function EcrireCookie(nom, valeur)
		{
		var argv=EcrireCookie.arguments;
		var argc=EcrireCookie.arguments.length;
		var expires=(argc > 2) ? argv[2] : null;
		var path=(argc > 3) ? argv[3] : null;
		var domain=(argc > 4) ? argv[4] : null;
		var secure=(argc > 5) ? argv[5] : false;
		document.cookie=nom+"="+escape(valeur)+
		((expires==null) ? "" : ("; expires="+expires.toGMTString()))+
		((path==null) ? "" : ("; path="+path))+
		((domain==null) ? "" : ("; domain="+domain))+
		((secure==true) ? "; secure" : "");
		}
	function getCookieVal(offset)
		{
		var endstr=document.cookie.indexOf (";", offset);
		if (endstr==-1) endstr=document.cookie.length;
		return unescape(document.cookie.substring(offset, endstr));
		}
	function LireCookie(nom)
		{
		var arg=nom+"=";
		var alen=arg.length;
		var clen=document.cookie.length;
		var i=0;
		while (i<clen)
			{
			var j=i+alen;
			//alert(arg+' ---- '+getCookieVal(j));
			if (document.cookie.substring(i, j)==arg) return getCookieVal(j);
			i=document.cookie.indexOf(" ",i)+1;
			if (i==0) break;
			}
		return null;
		}
</script>

<center>
<br>
<table width=80%>

<?

if(d_ok(970)or d_ok(201) or d_ok(968) or d_ok(995)or d_ok(960))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width=100%>';
	aff_sep('<img src="images/tools.png">&nbsp; Ordonnancement',1);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_1" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>';
	if(d_ok(970))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="tzar_consulter.php?parent_ori=1">Plan de mise en main</a>
						</td>
					</tr>
		';
		}

	if(d_ok(201))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="of_consulter.php?parent_id=950">Liste des OF</a>
						</td>
					</tr>
		';
		}

	if(d_ok(209))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="of_otd.php?parent_id=950">Indicateur OTD Façon complète</a>
						</td>
					</tr>
		';
		}
	
	if(d_ok(968))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="of_confirmation.php?parent_ori=1">Avancement des phases</a>
						</td>
					</tr>
		';
		}

	if(d_ok(995))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="of_suivi_qualite.php?parent_ori=1">Plan de mise en main qualité</a>
						</td>
					</tr>
		';
		}
	
	if(d_ok(965))
		{
		echo '
					<tr>
						<td align=center class="cel1" >
						<a class=b href="of_dashboard.php?parent_ori=1">Tableau de bord</a>
						</td>
					</tr>
		';
		}
		
			echo '</td>
			</tr></table>';
	}

	
if(d_ok(960) or d_ok(963)or d_ok(972) or d_ok(981) or d_ok(982))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width=100%>';
	aff_sep('<img src="images/money.png">&nbsp; Administration des ventes',2);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_2" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>';
	if(d_ok(960))
		{
		echo '<tr>
			<td align=center class="cel1">
				<a class=b href="affaire_prev_facture_ps.php?parent_ori=1">Carnet de commande PS</a>
			</td>
		</tr>';
		}

	if(d_ok(963))
		{
		echo '<tr>
			<td align=center class="cel1">
				<a class=b href="pointage_ps.php?parent_ori=1">Récap vente PS</a>
			</td>
		</tr>';
		}	
		
	if(d_ok(972))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="of_rapprochement.php?parent_ori=1">Rapprochement pour facturation PS (NEW)</a>
			</td>
		</tr>';
		}

	if(d_ok(972))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="of_rapprochement2.php?parent_ori=1">Rapprochement pour facturation PS</a>
			</td>
		</tr>';
		}

	if(d_ok(981))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="affaire_en_cours_piece.php?parent_ori=1">Montant des "en cours" (Pièce série)</a>
			</td>
		</tr>';
		}
		
		
	if(d_ok(981))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="affaire_en_cours_outillage.php?parent_ori=1">Montant des "en cours" (outillage)</a>
			</td>
		</tr>
		';
		}
		
	if(d_ok(10000))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="affaire_en_cours_piece2.php?parent_ori=1">Montant des "en cours" (prix Pièces)</a>
			</td>
		</tr>';
		}


	if(d_ok(982))
		{
		echo '<tr>
			<td align=center class="cel1" >
				<a class=b href="affaire_h_restante.php?parent_ori=1">Calcul des heures restantes</a>
			</td>
		</tr>';
		}
	echo '</td></tr></table>';
	}

if( d_ok(978) or d_ok(980) or d_ok(997) or d_ok(958) or d_ok(983))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/insertdate.gif">&nbsp; Prévisionnel',3);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_3" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>';
			


	if(d_ok(958))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_previsionnel_gt.php?parent_ori=1">Prévisionnel GT</a>
						</td>
					</tr>
		';
		}

	if(d_ok(978))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_previsionnel_charge.php?parent_ori=1">Prévisionnel charge</a>
						</td>
					</tr>
		';
		}

	if(d_ok(980))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_previsionnel_recap.php?parent_ori=1">Recap previsionnel charge</a>
						</td>
					</tr>
		';
		}
		
	if(d_ok(983))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_previsionnel_recap_global.php?parent_ori=1">Recap previsionnel charge globale</a>
						</td>
					</tr>
		';
		}

	if(d_ok(997))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="planif_serie.php?parent_ori=1">Planning pièces série</a>
						</td>
					</tr>

		';
		}
			echo '			     </td>
			</tr></table>';
	}

if(d_ok(973) or d_ok(974) or  d_ok(976) or d_ok(991) or d_ok(992) or d_ok(993))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/folder_sent_mail.png">&nbsp; Suivi de production',4);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_4" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>';
	
	if(d_ok(974))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_realisation.php?parent_ori=1">Réalisation par Gt</a>
						</td>
					</tr>
		';
		}


	if(d_ok(976))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_flux_entrant.php?parent_ori=1">Analyse flux entrées/sorties</a>
						</td>
					</tr>
		';
		}



	if(d_ok(991))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_reference.php?parent_ori=1">Analyse Référence</a>
						</td>
					</tr>
		';
		}

		
	if(d_ok(996))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_reference_global.php?parent_ori=1">Analyse Référence Global</a>
						</td>
					</tr>
		';
		}
		
	if(d_ok(992))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_piece_par_phase.php?parent_ori=1">Suivi des pièces en cours</a>
						</td>
					</tr>
		';
		}
	
	if(d_ok(973))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_qte_pieces_realisees.php?parent_ori=1">Pièces réalisées / livrées</a>
						</td>
					</tr>
		';
		}
		
	if(d_ok(973))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_qte_pieces_realisees_photo.php?parent_ori=1">Pièce photo</a>
						</td>
					</tr>
		';
		}

	if(d_ok(993))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_tps_piece_lire.php?parent_ori=1">Temps moyen par pièce</a>
						</td>
					</tr>

		';
		}
			echo '			     </td>
			</tr></table>';
	}

if( d_ok(988) or d_ok(990)or d_ok(989))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/poll.png">&nbsp; Efficience',5);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_5" class="cachediv">
			   <table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>
';
	
	if(d_ok(988))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_efficience_gt.php?parent_ori=1">Analyse efficience par GT</a>
						</td>
					</tr>
		';
		}
		
	if(d_ok(989))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_efficience_msn.php?parent_ori=1">Analyse efficience par MSN</a>
						</td>
					</tr>
		';
		}
		
	if(d_ok(990))
		{
	echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_efficience.php?parent_ori=1">Analyse efficience détaillée</a>
						</td>
					</tr>

		';
		}
			echo '</td></tr></table>';
	}

if(d_ok(985) or d_ok(984) or d_ok(986) )
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/preview.gif">&nbsp; Analyse des Cycles',6);
	echo '</table>
		</td>
	      </tr>	<tr>
			   <td id="t_aff_6" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>';

	if(d_ok(985))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_cycle_globale.php">Analyse Cycle global</a>
						</td>
					</tr>
		';
		}

	if(d_ok(984))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_analyse_cycle_detaille.php">Analyse Cycle Detaillé</a>
						</td>
					</tr>
		';
		}


	if(d_ok(986))
		{
		echo '
					<tr>
						<td align=center class="cel1">
						<a class=b href="of_recap_cycle_global_ref.php">Récap Cycle Global par reference</a>
						</td>
					</tr>

		';
		}
			echo '			     </td>
			</tr></table>';
	}

if(d_ok(1210))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/tools.png">&nbsp; Dossier d\'industrialisation',7);
	echo '</table>
		</td>
	      </tr>';
	
	if(d_ok(1210))
		{
		echo '	<tr>
			   <td id="t_aff_7" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>
					<tr>
						<td align=center class="cel1">
						<a class=b href="doc_gestion_rpa.php">Gestion RPA</a>
						</td>
					</tr>
			     </td>
			</tr>
		';
		}
			echo '</table>';
	}
if(d_ok(1251))
	{
	echo '<tr>
		<td>
		<table align=center style="cursor:pointer" class=forumline cellSpacing=1 cellPadding=0 width="100%">';
	aff_sep('<img src="images/tools.png">&nbsp; Outillage',8);
	echo '</table>
		</td>
	      </tr>';
			
	if(d_ok(1251))
		{
		echo '	<tr>
			   <td id="t_aff_8" class="cachediv">
				<table align=center class=forumline cellSpacing=1 cellPadding=2 width="100%" border=0>
					<tr>
						<td align=center class="cel1">
						<a class=b href="outillage_consulter.php">Gestion des outillages</a>
						</td>
					</tr>
			     </td>
			</tr>
		';
		}
			echo '</table>';
	}

echo '</table>';


?>

<script type="text/javascript" language="JavaScript">
	if(LireCookie("t_aff_1")==1)DivStatus( 't_aff_1' );
	if(LireCookie("t_aff_2")==1)DivStatus( 't_aff_2' );
	if(LireCookie("t_aff_3")==1)DivStatus( 't_aff_3' );
	if(LireCookie("t_aff_4")==1)DivStatus( 't_aff_4' );
	if(LireCookie("t_aff_5")==1)DivStatus( 't_aff_5' );
	if(LireCookie("t_aff_6")==1)DivStatus( 't_aff_6' );
	if(LireCookie("t_aff_7")==1)DivStatus( 't_aff_7' );
	if(LireCookie("t_aff_8")==1)DivStatus( 't_aff_8' );
</script>

</center>
</body>
</html>
