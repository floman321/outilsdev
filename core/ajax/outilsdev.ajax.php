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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    // action qui permet d'obtenir l'ensemble des eqLogic
    if (init('action') == 'getAll') {
        $eqLogics = eqLogic::byType('outilsdev');
        // la liste des équipements
        foreach ($eqLogics as $eqLogic) {
            $data['id'] = $eqLogic->getId();
            $data['humanSidebar'] = $eqLogic->getHumanName(true, false);
            $data['humanContainer'] = $eqLogic->getHumanName(true, true);
            $return[] = $data;
        }
        ajax::success($return);
    }

    // action qui permet d'effectuer la sauvegarde des donéée en asynchrone
    if (init('action') == 'saveStack') {
        $params = init('params');
        ajax::success(outilsdev::saveStack($params));
    }

    // action qui permet la création d'un nouveau plugin
    if (init('action') == 'createPlugin') {
        $params = init('params');
        ajax::success(outilsdev::createPlugin($params));
    }

    if (init('action') == 'testCondition') {
        $condition = init('condition');
        ajax::success(outilsdev::testCondition($condition));
    }

    if (init('action') == 'getContent') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception(__('Aucun fichier trouvé : ', __FILE__) . $path);
        }
        if (!is_readable($path)) {
            throw new Exception(__('Impossible de lire : ', __FILE__) . $path);
        }
        if (is_dir($path)) {
            throw new Exception(__('Impossible de lire un dossier : ', __FILE__) . $path);
        }
        $pathinfo = pathinfo($path);
        $return = array(
            'content' => file_get_contents($path),
            'extension' => $pathinfo['extension']
        );
        ajax::success($return);
    }

    if (init('action') == 'saveContent') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception(__('Aucun fichier trouvé : ', __FILE__) . $path);
        }
        if (!is_writable($path)) {
            throw new Exception(__('Impossible d\'écrire dans : ', __FILE__) . $path);
        }
        if (is_dir($path)) {
            throw new Exception(__('Impossible d\'écrire un dossier : ', __FILE__) . $path);
        }
        file_put_contents($path, init('content'));
        chmod($path, 0770);
        ajax::success();
    }

    if (init('action') == 'uploadImage') {
        if (!isset($_FILES['file'])) {
            throw new Exception(__('Aucun fichier trouvé. Vérifier parametre PHP (post size limit)', __FILE__));
        }
        $extension = strtolower(strrchr($_FILES['file']['name'], '.'));
        if (!in_array($extension, array('.png'))) {
            throw new Exception('Extension du fichier non valide (autorisé .png) : ' . $extension);
        }
        if (filesize($_FILES['file']['tmp_name']) > 1000000) {
            throw new Exception(__('Le fichier est trop gros (maximum 1mo)', __FILE__));
        }
        
        $filepath = dirname(__FILE__) . '/../../tmp/image.png';
                
        file_put_contents($filepath, file_get_contents($_FILES['file']['tmp_name']));

        ajax::success();
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
3