<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/******************************* Includes *******************************/ 
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class outilsdev extends eqLogic {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*
    // Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {

    }
    */

    /*
    // Fonction exécutée automatiquement toutes les heures par Jeedom
    public static function cronHourly() {

    }
    */

    /*
    // Fonction exécutée automatiquement tous les jours par Jeedom
    public static function cronDayly() {

    }
    */

    public function createPlugin($params){
      $params = json_decode($params, true);
      log::add('outilsdev', 'debug', "Création d'un nouveau plugin:");
      log::add('outilsdev', 'debug', print_r($params,true));

      $pluginAlreadyExist = false;
      /*
      try{
        if(is_object(market::byLogicalId($params['plugin_id']))){
          log::add('outilsdev', 'debug', 'Plugin déjà éxistant sur le market');
          $pluginAlreadyExist = true;
        };
      } catch (Exception $e) {}
      */

      if($pluginAlreadyExist){
        throw new Exception(__('L\'ID du plugin existe déjà sur le market!', __FILE__));
      }

      if($params['plugin_id'] == '' or $params['plugin_name'] == '' or $params['plugin_description'] == '' or $params['plugin_icon'] == '' or $params['plugin_author'] == '' or $params['plugin_core_require'] == '' or $params['plugin_category'] == ''){
        throw new Exception(__('Vérifiez les paramétres renseignés. Certains sont manquants ou incorrects.', __FILE__));
      }

      $cibDir = "../../tmp/" . $params['plugin_id'] . '/';

      $templateFile = file_get_contents("https://github.com/jeedom/plugin-template/archive/master.zip");
      if($templateFile === FALSE){
        throw new Exception(__('Impossible de télécharger le fichier template.', __FILE__));
      }

      if(file_put_contents('../../tmp/template.zip', $templateFile) === FALSE){
        throw new Exception(__('Impossible d\'enregistrer le fichier téléchargé.', __FILE__));
      }      

      $zip = new ZipArchive;
      if ($zip->open('../../tmp/template.zip') === TRUE) {
        if (!$zip->extractTo('../../tmp/')) {
          throw new Exception(__('Impossible d\'extraire le template.', __FILE__));
        }
        $zip->close();
      } else {
        throw new Exception(__('Impossible de décompresser l\'archive zip.', __FILE__));
      }

      unlink('../../tmp/template.zip');

      if(!rename("../../tmp/plugin-template-master" , $cibDir)){
        throw new Exception(__('Impossible de renommer le dossier', __FILE__));
      }

      if(file_exists("../../tmp/image.png")){
        if(!rename("../../tmp/image.png" , $cibDir . 'doc/images/' . $params['plugin_id'] . '_icon.png')){
          throw new Exception(__('Impossible de renommer le visuel', __FILE__));
        }
      }

      $file_to_rename = array(
        $cibDir . 'desktop/js/template.js' => $cibDir . 'desktop/js/' . $params['plugin_id'] . '.js',
        $cibDir . 'desktop/modal/modal.template.php' => $cibDir . 'desktop/modal/modal.' . $params['plugin_id'] . '.php',
        $cibDir . 'desktop/php/template.php' => $cibDir . 'desktop/php/' . $params['plugin_id'] . '.php',
        $cibDir . 'core/ajax/template.ajax.php' => $cibDir . 'core/ajax/' . $params['plugin_id'] . '.ajax.php',
        $cibDir . 'core/class/template.class.php' => $cibDir . 'core/class/' . $params['plugin_id'] . '.class.php',
        $cibDir . 'core/php/template.inc.php' => $cibDir . 'core/php/' . $params['plugin_id'] . '.inc.php'
      );

      log::add('outilsdev', 'debug', print_r($file_to_rename,true));

      foreach($file_to_rename as $old => $new){
        log::add('outilsdev', 'debug', 'Renommage de: ' . $old . ' => ' . $new);
      
        if(!rename($old , $new)){
          throw new Exception(__('Impossible de renommer le fichier: ', __FILE__) . $old);
        }
      }
      
      unlink($cibDir . 'plugin_info/info.json');
      
      $file_to_replace = array(
        $cibDir . 'plugin_info/info.xml',
        $cibDir . 'plugin_info/install.php',
        $cibDir . 'plugin_info/configuration.php',
        $cibDir . 'doc/fr_FR/index.asciidoc',
        $cibDir . 'desktop/js/' . $params['plugin_id'] . '.js',
        $cibDir . 'desktop/modal/modal.' . $params['plugin_id'] . '.php',
        $cibDir . 'desktop/php/' . $params['plugin_id'] . '.php',
        $cibDir . 'core/ajax/' . $params['plugin_id'] . '.ajax.php',
        $cibDir . 'core/class/' . $params['plugin_id'] . '.class.php',
        $cibDir . 'core/php/' . $params['plugin_id'] . '.inc.php'
      );

      $replace = array();
      foreach($params as $param => $value){      
        $replace['#' . $param . '#'] = $value;
      }
	  
	  $replace['template'] = $params['plugin_id'];
	  $replace['Template'] = $params['plugin_name'];
	  $replace['<category>programming</category>'] = "<category>".$params['plugin_category']."</category>";
	  $replace['<author>Loïc</author>'] = "<author>".$params['plugin_author']."</author>";
	  $replace['<description>Plugin template pour la création de plugin</description>'] = "<description>".$params['plugin_description']."<description>";
      $replace['<installation>Aucune</installation>'] = "<installation>".$params['plugin_installation']."</installation>";

      log::add('outilsdev', 'debug', print_r($replace,true));

      foreach($file_to_replace as $file){
        $file_content = file_get_contents($file);
        
        $file_content = template_replace($replace, $file_content);
        
        $file_content = str_replace('plugin.'.$params['plugin_id'], 'plugin.template', $file_content);

        if(!file_put_contents($file, $file_content)){
          throw new Exception(__('Impossible d\'appliquer le template sur le fichier: ', __FILE__) . $file);
        }
      }

      if(!rename($cibDir , '../../../' . $params['plugin_id'])){
        throw new Exception(__('Impossible de déplacer le dossier dans le dossier plugin.', __FILE__));
      }
      
      update::findNewUpdateObject();

      return true;
    }

    public function testCondition($condition){
      $expression = scenarioExpression::setTags(jeedom::fromHumanReadable($condition));
      $message = __('Evaluation de la condition : ', __FILE__) . $condition . "\n";
	  $message .= __(' Résultat : [', __FILE__) . $expression .'] = ';
	  
      $result = evaluate($expression);
      if (is_bool($result)) {
        if ($result) {
          $message .= __('Vrai', __FILE__);
        } else {
          $message .= __('Faux', __FILE__);
        }
      } else {
        $message .= $result;
      }
	  
	  $message .= "\n\n";
	  
      return $message;
    }
 
    /*************************** Methode d'instance **************************/ 
 

    /************************** Pile de mise à jour **************************/ 
    
    /* fonction permettant d'initialiser la pile 
     * plugin: le nom de votre plugin
     * action: l'action qui sera utilisé dans le fichier ajax du pulgin 
     * callback: fonction appelé coté client(JS) pour mettre à jour l'affichage 
     */ 
    public function initStackData() {
        nodejs::pushUpdate('outilsdev::initStackDataEqLogic', array('plugin' => 'outilsdev', 'action' => 'saveStack', 'callback' => 'displayEqLogic'));
    }
    
    /* fonnction permettant d'envoyer un nouvel équipement pour sauvegarde et affichage, 
     * les données sont envoyé au client(JS) pour être traité de manière asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function stackData($params) {
        if(is_object($params)) {
            $paramsArray = utils::o2a($params);
        }
        nodejs::pushUpdate('outilsdev::stackDataEqLogic', $paramsArray);
    }
    
    /* fonction appelé pour la sauvegarde asynchrone
     * Entrée: 
     *      - $params: variable contenant les paramètres eqLogic
     */
    public function saveStack($params) {
        // inserer ici le traitement pour sauvegarde de vos données en asynchrone
        
    }

    /* fonction appelé avant le début de la séquence de sauvegarde */
    public function preSave() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function preUpdate() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une mise à jour d'une entrée */
    public function postUpdate() {
        
    }

    /* fonction appelé pendant la séquence de sauvegarde avant l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function preInsert() {

    }

    /* fonction appelé pendant la séquence de sauvegarde après l'insertion 
     * dans la base de données pour une nouvelle entrée */
    public function postInsert() {
        
    }

    /* fonction appelé après la fin de la séquence de sauvegarde */
    public function postSave() {
        
    }

    /* fonction appelé avant l'effacement d'une entrée */
    public function preRemove() {
        
    }

    /* fonnction appelé après l'effacement d'une entrée */
    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class outilsdevCmd extends cmd {
    /******************************* Attributs *******************************/ 
    /* Ajouter ici toutes vos variables propre à votre classe */

    /***************************** Methode static ****************************/ 

    /*************************** Methode d'instance **************************/ 

    /* Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
    public function dontRemoveCmd() {
        return true;
    }
    */

    public function execute($_options = array()) {
        
    }

    /***************************** Getteur/Setteur ***************************/ 

    
}

?>
