<?php
global $JEEDOM_INTERNAL_CONFIG;

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

include_file('3rdparty', 'codemirror/lib/codemirror', 'js');
include_file('3rdparty', 'codemirror/lib/codemirror', 'css');
include_file('3rdparty', 'codemirror/addon/edit/matchbrackets', 'js');
include_file('3rdparty', 'codemirror/mode/htmlmixed/htmlmixed', 'js');
include_file('3rdparty', 'codemirror/mode/clike/clike', 'js');
include_file('3rdparty', 'codemirror/mode/php/php', 'js');
include_file('3rdparty', 'codemirror/mode/shell/shell', 'js');
include_file('3rdparty', 'codemirror/mode/python/python', 'js');
include_file('3rdparty', 'codemirror/mode/ruby/ruby', 'js');
include_file('3rdparty', 'codemirror/mode/perl/perl', 'js');


include_file('3rparty/elfinder', 'elfinder.min', 'js', 'outilsdev');

$themes = config::byKey('theme', 'outilsdev','');
if ($themes != ''){
    include_file('3rparty/elfinder', $themes.'/theme', 'css', 'outilsdev');
}else{
    include_file('3rparty/elfinder', 'theme', 'css', 'outilsdev');
}
    



// STYLE

include_file('3rparty/elfinder', 'elfinder.min', 'css', 'outilsdev');

$fontsize_editor = config::byKey('fontsize_editor', 'outilsdev','12').'px';
echo "<style>
.CodeMirror pre {
  font-size: $fontsize_editor;
}
</style>";

?>
<style>
</style>

<script src="/plugins/outilsdev/3rparty/elfinder/js/i18n/elfinder.fr.js"></script>


<div class="alert alert-danger">
    L'utilisation de ce plugin est à vos risques et périls. Avant de réaliser une action, assurez-vous de savoir ce que vous faites.<br>
    N'oubliez pas de faire une sauvegarde!
</div>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#finder" role="tab" data-toggle="tab">{{Navigateur et éditeur de fichier}}</a></li>
    <li role="presentation" ><a href="#blibliotheque" role="tab" data-toggle="tab">{{Blibliothèque}}</a></li>
    <li role="presentation" ><a href="#pluginmaker" role="tab" data-toggle="tab">{{Création de plugin}}</a></li>
    <li role="presentation" ><a href="#testexpression" role="tab" data-toggle="tab">{{Testeur d'expressions}}</a></li>
        
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="finder">
        <div id="elfinder"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="pluginmaker">
    </br>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Nom du plugin}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control tooltips pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ID du plugin}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control tooltips pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_id"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Icône}}</label>
                            <div class="col-sm-4">
                                <a class="btn btn-default" id="bt_chooseIcon"><i class="fa fa-flag"></i> {{Choisir}}</a>
                            </div>
                            <div class="col-sm-2">
                                <div class="pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_icon" style="font-size : 1.5em;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Visuel}}</label>
                            <div class="col-sm-4">
                                <span class="btn btn-default btn-file">
                                    <i class="fa fa-cloud-upload"></i> {{Envoyer l'image}}<input id="bt_uploadImage" type="file" name="file" style="display: inline-block;">
                                </span>
                            </div>
                            <div class="col-sm-2">
                                <img src="/plugins/outilsdev/tmp/image.png" style="width: 100px; display: none;" id="plugin_maker_visuel">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Version du core requise}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control tooltips pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_core_require" value="<?php echo jeedom::version();?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Catégorie}}</label>
                            <div class="col-sm-6">
                                <select class="form-control pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_category">
                                    <?php 
                                    foreach ($JEEDOM_INTERNAL_CONFIG['plugin']['category'] as $catName => $cat){
                                        echo "<option value=\"" . $catName . "\">" . $cat['name'] . "</option>\n";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-actions">
                            <a class="btn btn-success" id="btn-create-plugin"><i class="fa fa-check-circle"></i> {{Créer mon plugin}}</a>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Nom de l'auteur}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control tooltips pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_author" value="<?php echo config::byKey('market::username'); ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Description}}</label>
                            <div class="col-sm-6">
                                <textarea class="form-control pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Descriptif pour l'installation}}</label>
                            <div class="col-sm-6">
                                <textarea class="form-control pluginAttr" data-l1key="pluginmaker" data-l2key="plugin_installation"></textarea>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="testexpression">
        <div class="row">
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Condition}}</legend>
                        <div class="expression input-group input-group-sm" style="width: 100%;">
                            <textarea class="expressionAttr form-control" data-l1key="expression" style="resize:vertical;" id="conditionTest"></textarea>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default cursor bt_selectCmdExpression tooltips" title="Rechercher une commande"><i class="fa fa-list-alt"></i></button>
                                <button type="button" class="btn btn-default cursor bt_selectScenarioExpression tooltips" title="Rechercher un scenario"><i class="fa fa-history"></i></button>
                                <button type="button" class="btn btn-default cursor bt_selectEqLogicExpression tooltips" title="Rechercher d'une fonction"><i class="fa fa-cube"></i></button>
                            </span>
                        </div>
                    </br>
                    <a class="btn btn-success" id="btn-test-condition"><i class="fa fa-check-circle"></i> {{Tester}}</a> <a class="btn btn-warning" id="btn-reset-results"><i class="fa fa-times"></i> {{Vider les résultats}}</a>
                    </fieldset>
                </form>
            </div>
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Résultat}}</legend>
                        <div style="overflow: scroll; height: 250px;">
                        <pre id="pre_logScenarioDisplay"></pre>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

        
    </div>
                                
                                
                                
                                
                                <div role="tabpanel" class="tab-pane" id="blibliotheque">
                                <div class="row">
                                <div class="col-sm-6">
                                
                                <form class="form-horizontal">
                                
                                <fieldset>
                                <legend>{{Trucs a savoir}}</legend>
                                
                                Un plugin est composé :<br>
                                d'un objet principal (ex outilsdev) et de commandes (ex outilsdev_CMD).<br>
                                <br>
                                L'utilisateur pourra créer autant de objet principal.<br>
                                <br>
                                <h3>Dossier Core : Contient de coeur du plugin</h3>
                                
                                <p>
                                Ici, on trouvera la création des commandes (bas du document)<br>
                                Mais surtout la gestion de l'objet principale et la mise à jour de celui ci (haut du document)<br>
                                <br>
                                <br>
                                Mise à jour d'un objet :<br>
                                - Soit via un cron (facile et rapide a mettre en oeuvre)<br>
                                - Mettre du code dans la procédure dans "cron" toutes les minutes (déconseillés)<br>
                                - Mettre du code dans la procédure dans "cronHourly" toutes les heures<br>
                                - Mettre du code dans la procédure dans "cronDayly" tous les jours<br>
                                <br>
                                <br>
                                - Soit via un deamon (+ compliqué mais plus précis)<br>
                                a compléter
                                </p>
                                
                                <h3>Dossier Desktop : Contient la partie affichage (paramètrage)</h3>
                                
                                
                                </fieldset>
                                
                                </form>
                                
                                
                                </div>
                                </div>
                                </div>
                                
                                
                                
</div>

<div id="md_editFile" title="Editer..." >
    <div style="display: none;" id="div_editFileAlert"></div>
    <textarea id="ta_editFile" class="form-control" style="height: 100%;"></textarea>
</div>

<?php include_file('desktop', 'outilsdev', 'js', 'outilsdev'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
