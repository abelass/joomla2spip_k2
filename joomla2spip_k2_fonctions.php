<?php
/**
 * Fonctions utiles au plugin Joomla2spip K2
 *
 * @plugin     Joomla2spip K2
 * @copyright  2015
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Joomla2spip_k2\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function article_import_k2($mon_article) {
	include_spip('joomla2spip_fonctions');
	include_spip('plugins/installer');
	$err = '';
	
	// chercher si l'article n'a pas deja ete importe
	$ancien_id = $mon_article['id_article'];
	$result = sql_fetsel('id_article','spip_articles','id_article='.intval($ancien_id)) ;
	if($result)
	       return;
	
        
	// chercher la rubrique
	$titre_rub = $mon_article['rubrique'];
  $result = sql_fetsel('id_rubrique','spip_rubriques','titre='.sql_quote($titre_rub)) ; 
  if($result){
     $id_rubrique = $result['id_rubrique'] ;
  }
    
    
	// creer article vide
	include_spip('action/editer_article');
	$id_article = insert_article($id_rubrique);	
	$sql = "UPDATE spip_articles SET id_article = '$ancien_id' WHERE id_article = '$id_article'";
	spip_query($sql);

	if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0', '>=')){
		include_spip('action/editer_article');
		$sql = "UPDATE spip_auteurs_liens SET id_objet = '$ancien_id' WHERE id_objet = '$id_article' AND objet = 'article'";
	}
	else{
		include_spip('inc/modifier');
		$sql = "UPDATE spip_auteurs_articles SET id_article = '$ancien_id' WHERE id_article = '$id_article'";
	}
	spip_query($sql);
	$id_article = $ancien_id ;
	// le remplir
	$c = array();
	foreach (array(
		'surtitre', 'titre', 'soustitre', 'descriptif',
		'nom_site', 'url_site', 'chapo', 'texte', 'maj', 'ps','visites'
	) as $champ)
		$c[$champ] = $mon_article[$champ];

	revisions_articles($id_article, $c);

	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'date', 'statut', 'id_parent'
	) as $champ)
		$c[$champ] = $mon_article[$champ];
	$c['id_parent'] = $id_rubrique ;
	$err .= instituer_article($id_article, $c);

	// Un lien de trad a prendre en compte
	if (!spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0', '>=')) $err .= article_referent($id_article, array('lier_trad' => _request('lier_trad')));
	
	// ajouter les extras
	
	// les documents attachées
	foreach($mon_article['document'] AS $document){
		images/
	}
	
	
	return $err; 
}

?>