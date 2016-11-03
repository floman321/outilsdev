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



/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */

/* Fonction appelé pour mettre l'affichage du tableau des commandes de votre eqLogic
 * _cmd: les détails de votre commande
 */
/* global jeedom */

/*$(document).ready(function() {
    $('#elfinder').elfinder({
        url : 'plugins/outilsdev/3rparty/elfinder/php/connector.minimal.php'  // connector URL (REQUIRED)
        // , lang: 'ru'                    // language (OPTIONAL)
    });
});*/


editor = null;

$().ready(function() {
    var elf = $('#elfinder').elfinder({
        url : 'plugins/outilsdev/3rparty/elfinder/php/connector.minimal.php',
		lang:'fr',
		
		
		contextmenu : {
			// navbarfolder menu
			navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

			// current directory menu
			cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

			// current directory file menu
			files  : [
				 '|','edit', 'open', 'quicklook',  'rename' ,'|', 'getfile' , 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
				'rm', '|', 'resize', '|', 'archive', 'extract', '|', 'info'
			]
		 },
		
        commandsOptions: {

            getFileCallback: function(file) {
              console.log('getFilCallBack');
              console.log(file);
            },
			
			

            edit : {
                // list of allowed mimetypes to edit
                // if empty - any text files can be edited
                mimes : ['text/plain', 'text/html', 'text/javascript', 'text/css', 'text/x-php'],

                // you can have a different editor for different mimes
                editors : [{
                    mimes : ['text/plain', 'text/html', 'text/javascript', 'text/css', 'text/x-php'],
                    load : function(textarea) {
												
                        this.myCodeMirror = CodeMirror.fromTextArea(textarea, {
                            lineNumbers: true,
                            mode: 'text/x-php',
                            matchBrackets: true
                        });
                        $(".cm-s-default").height('100%');
                        $(".cm-s-default").parent().height('300px'); 
                    },
					

                    close : function(textarea, instance) {
                        this.myCodeMirror = null;
                    },


                    save : function(textarea, editor) {                                      
                        textarea.value = this.myCodeMirror.getValue();
                        this.myCodeMirror = null;
                    }

                },
                {
                    mimes : ['text/x-python'],
                    load : function(textarea) {
                        this.myCodeMirror = CodeMirror.fromTextArea(textarea, {
                            lineNumbers: true,
                            mode: 'text/x-python',
                            matchBrackets: true
                        });
                        $(".cm-s-default").height('100%');
                    },

                    close : function(textarea, instance) {
                        this.myCodeMirror = null;
                    },


                    save : function(textarea, editor) {                                      
                        textarea.value = this.myCodeMirror.getValue();
                        this.myCodeMirror = null;
                    }

                },
                {
                    mimes : ['shell'],
                    load : function(textarea) {
						
						
						
                        this.myCodeMirror = CodeMirror.fromTextArea(textarea, {
                            lineNumbers: true,
                            mode: 'shell',
                            matchBrackets: true
                        });
                        $(".cm-s-default").height('100%');
                    },

                    close : function(textarea, instance) {
                        this.myCodeMirror = null;
                    },


                    save : function(textarea, editor) {                                      
                        textarea.value = this.myCodeMirror.getValue();
                        this.myCodeMirror = null;
                    }

                },
                {
                    mimes : ['text/x-ruby'],
                    load : function(textarea) {
                        this.myCodeMirror = CodeMirror.fromTextArea(textarea, {
                            lineNumbers: true,
                            mode: 'text/x-ruby',
                            matchBrackets: true
                        });
                        $(".cm-s-default").height('100%');
                    },

                    close : function(textarea, instance) {
                        this.myCodeMirror = null;
                    },


                    save : function(textarea, editor) {                                      
                        textarea.value = this.myCodeMirror.getValue();
                        this.myCodeMirror = null;
                    }

                } ] //editors
            } //edit

        } //commandsoptions
    }).elfinder('instance');

    $('#bt_uploadImage').fileupload({
        replaceFileInput: false,
        url: 'plugins/outilsdev/core/ajax/outilsdev.ajax.php?action=uploadImage',
        dataType: 'json',
        done: function (e, data) {
            if (data.result.state != 'ok') {
                $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
                return;
            }
            $("#plugin_maker_visuel").load(function() {
                $(this).hide();
                $(this).fadeIn('slow');
            }).attr('src', 'plugins/outilsdev/tmp/image.png?' + new Date().getTime());
        }
    });

    
});

 $("#md_editFile").dialog({
    autoOpen: false,
    modal: true,
    height: (jQuery(window).height() - 150),
    width: (jQuery(window).width() - 150)
});

$('#bt_chooseIcon').on('click', function () {
    chooseIcon(function (_icon) {
        $('.pluginAttr[data-l1key=pluginmaker][data-l2key=plugin_icon]').empty().append(_icon);
    });
});

$('#btn-create-plugin').on('click',function(){
    bootbox.confirm('{{Etes-vous sûr de vouloir créer un nouveau plugin?}}', function (result) {
        if (result) {
            var params = {};
            $('.pluginAttr[data-l1key=pluginmaker]').each(function(){
                params[$(this).attr('data-l2key')] = $(this).value();
            });
            params['plugin_icon'] = $('.pluginAttr[data-l1key=pluginmaker][data-l2key=plugin_icon]').children().attr("class");

            $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // méthode de transmission des données au fichier php
                    url: "plugins/outilsdev/core/ajax/outilsdev.ajax.php", // url du fichier php
                    data: {
                        action: "createPlugin",
                        params: json_encode(params),
                    },
                    dataType: 'json',
                    global: false,
                    error: function (request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function (data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        $('#div_alert').showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    $('#div_alert').showAlert({message: '{{Opération réalisée avec succès. Aller dans la section "Gestion des plugins" pour activer votre plugin.}}', level: 'success'});
                }
            });
        }
    });
});

$('#btn-test-condition').on('click',function(){
    var params = {};
    $('.pluginAttr[data-l1key=pluginmaker]').each(function(){
        params[$(this).attr('data-l2key')] = $(this).value();
    });
    params['plugin_icon'] = $('.pluginAttr[data-l1key=pluginmaker][data-l2key=plugin_icon]').children().attr("class");

    $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // méthode de transmission des données au fichier php
            url: "plugins/outilsdev/core/ajax/outilsdev.ajax.php", // url du fichier php
            data: {
                action: "testCondition",
                condition: $('#conditionTest').value(),
            },
            dataType: 'json',
            global: false,
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            //$('#pre_logScenarioDisplay').empty();
            $('#pre_logScenarioDisplay').prepend(data.result);
        }
    });
});

$('#btn-reset-results').on('click',function(){
	$('#pre_logScenarioDisplay').empty();
});

$('body').delegate('.bt_selectScenarioExpression', 'click', function (event) {
  var expression = $(this).closest('.expression');
  jeedom.scenario.getSelectModal({}, function (result) {
      expression.find('.expressionAttr[data-l1key=expression]').atCaret('insert', result.human);
  });
});

$('body').delegate('.bt_selectEqLogicExpression', 'click', function (event) {
  var expression = $(this).closest('.expression');
  jeedom.eqLogic.getSelectModal({}, function (result) {
      expression.find('.expressionAttr[data-l1key=expression]').atCaret('insert', result.human);
  });
});

$('body').delegate('.bt_selectCmdExpression', 'click', function (event) {
  var el = $(this);
  var expression = $(this).closest('.expression');
  var type = 'info';

  jeedom.cmd.getSelectModal({cmd: {type: type}}, function (result) {
      message = 'Aucun choix possible';
      if(result.cmd.subType == 'numeric'){
       message = '<div class="row">  ' +
       '<div class="col-md-12"> ' +
       '<form class="form-horizontal" onsubmit="return false;"> ' +
       '<div class="form-group"> ' +
       '<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
       '             <div class="col-xs-3">' +
       '                <select class="conditionAttr form-control" data-l1key="operator">' +
       '                    <option value="==">{{égal}}</option>' +
       '                  <option value=">">{{supérieur}}</option>' +
       '                  <option value="<">{{inférieur}}</option>' +
       '                 <option value="!=">{{différent}}</option>' +
       '            </select>' +
       '       </div>' +
       '      <div class="col-xs-4">' +
       '         <input type="number" class="conditionAttr form-control" data-l1key="operande" />' +
       '    </div>' +
       '</div>' +
       '<div class="form-group"> ' +
       '<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
       '             <div class="col-xs-3">' +
       '                <select class="conditionAttr form-control" data-l1key="next">' +
       '                    <option value="">rien</option>' +
       '                  <option value="ET">{{et}}</option>' +
       '                  <option value="OU">{{ou}}</option>' +
       '            </select>' +
       '       </div>' +
       '</div>' +
       '</div> </div>' +
       '</form> </div>  </div>';
        }
     if(result.cmd.subType == 'string'){
      message = '<div class="row">  ' +
      '<div class="col-md-12"> ' +
      '<form class="form-horizontal" onsubmit="return false;"> ' +
      '<div class="form-group"> ' +
      '<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
      '             <div class="col-xs-3">' +
      '                <select class="conditionAttr form-control" data-l1key="operator">' +
      '                    <option value="==">{{égale}}</option>' +
      '                  <option value="~">{{contient}}</option>' +
      '                  <option value="!~">{{ne contient pas}}</option>' +
      '                 <option value="!=">{{différent}}</option>' +
      '            </select>' +
      '       </div>' +
      '      <div class="col-xs-4">' +
      '         <input class="conditionAttr form-control" data-l1key="operande" />' +
      '    </div>' +
      '</div>' +
      '<div class="form-group"> ' +
      '<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
      '             <div class="col-xs-3">' +
      '                <select class="conditionAttr form-control" data-l1key="next">' +
      '                    <option value="">rien</option>' +
      '                  <option value="ET">{{et}}</option>' +
      '                  <option value="OU">{{ou}}</option>' +
      '            </select>' +
      '       </div>' +
      '</div>' +
      '</div> </div>' +
      '</form> </div>  </div>';
    }
    if(result.cmd.subType == 'binary'){
      message = '<div class="row">  ' +
      '<div class="col-md-12"> ' +
      '<form class="form-horizontal" onsubmit="return false;"> ' +
      '<div class="form-group"> ' +
      '<label class="col-xs-5 control-label" >'+result.human+' {{est}}</label>' +
      '            <div class="col-xs-7">' +
      '                 <input class="conditionAttr" data-l1key="operator" value="==" style="display : none;" />' +
      '                  <select class="conditionAttr form-control" data-l1key="operande">' +
      '                       <option value="1">{{Ouvert}}</option>' +
      '                       <option value="0">{{Fermé}}</option>' +
      '                       <option value="1">{{Allumé}}</option>' +
      '                       <option value="0">{{Eteint}}</option>' +
      '                       <option value="1">{{Déclenché}}</option>' +
      '                       <option value="0">{{Au repos}}</option>' +
      '                       </select>' +
      '                    </div>' +
      '                 </div>' +
      '<div class="form-group"> ' +
      '<label class="col-xs-5 control-label" >{{Ensuite}}</label>' +
      '             <div class="col-xs-3">' +
      '                <select class="conditionAttr form-control" data-l1key="next">' +
      '                  <option value="">rien</option>' +
      '                  <option value="ET">{{et}}</option>' +
      '                  <option value="OU">{{ou}}</option>' +
      '            </select>' +
      '       </div>' +
      '</div>' +
      '</div> </div>' +
      '</form> </div>  </div>';
    }

    bootbox.dialog({
      title: "Ajout d'un nouveau scénario",
      message: message,
      buttons: {
        "Ne rien mettre": {
          className: "btn-default",
          callback: function () {
            expression.find('.expressionAttr[data-l1key=expression]').atCaret('insert', result.human);
          }
        },
        success: {
          label: "Valider",
          className: "btn-primary",
          callback: function () {
           var condition = result.human;
           condition += ' ' + $('.conditionAttr[data-l1key=operator]').value();
           if(result.cmd.subType == 'string'){
             condition += ' "' + $('.conditionAttr[data-l1key=operande]').value()+'"';
           }else{
            condition += ' ' + $('.conditionAttr[data-l1key=operande]').value();
          }
          condition += ' ' + $('.conditionAttr[data-l1key=next]').value()+' ';
          expression.find('.expressionAttr[data-l1key=expression]').atCaret('insert', condition);
          if($('.conditionAttr[data-l1key=next]').value() != ''){
            el.click();
          }
        }
      },
    }
  });
});
});

function editFile(path){
	var data = loadScriptFile(path);
	if (data === false) {
		return;
	}

	if (editor != null) {
		editor.getDoc().setValue(data.content);
		editor.setOption("mode", data.mode);
		setTimeout(function () {
			editor.refresh();
		}, 1);
	} else {
		$('#ta_editFile').val(data.content);
		setTimeout(function () {
			editor = CodeMirror.fromTextArea(document.getElementById("ta_editFile"), {
				lineNumbers: true,
				mode: data.mode,
				matchBrackets: true
			});
			editor.getWrapperElement().style.height = ($('#md_editFile').height()) + 'px';
			editor.refresh();
		}, 1);
	}

	$("#md_editFile").dialog('option', 'buttons', {
		"Annuler": function () {
			$(this).dialog("close");
		},
		"Enregistrer": function () {
			if (saveScriptFile(path, editor.getValue())) {
				$(this).dialog("close");
			}
		}
	});
	$("#md_editFile").dialog('open');
}

function loadScriptFile(_path) {
    $.hideAlert();
    var result = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/outilsdev/core/ajax/outilsdev.ajax.php", // url du fichier php
        data: {
            action: "getContent",
            path: _path,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return false;
			}
			result = data.result;
			switch (result.extension) {
				case 'php' :
				result.mode = 'text/x-php';
				break;
				case 'sh' :
				result.mode = 'shell';
				break;
				case 'pl' :
				result.mode = 'text/x-php';
				break;
				case 'py' :
				result.mode = 'text/x-python';
				break;
				case 'rb' :
				result.mode = 'text/x-ruby';
				break;
				default :
				result.mode = 'text/x-php';
				break;
			}
		}
	});
	return result;
}

function saveScriptFile(_path, _content) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/outilsdev/core/ajax/outilsdev.ajax.php", // url du fichier php
        data: {
            action: "saveContent",
            path: _path,
            content: _content,
        },
        dataType: 'json',
        async: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error, $('#div_editFileAlert'));
        },
        success: function (data) { // si l'appel a bien fonctionné
			if (data.state != 'ok') {
				$('#div_editFileAlert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			success = true;
			$('#div_alert').showAlert({message: 'Fichier sauvegardé', level: 'success'});
		}
	});
    return success;
}

function addCmdToTable(_cmd) {
    
}

/* Fonction appelé pour mettre l'affichage à jour pour la sauvegarde en temps réel
 * _data: les détails des informations à sauvegardé
 */
function displayEqLogic(_data) {
    
}

/* Fonction appelé pour mettre l'affichage à jour de la sidebar et du container 
 * en asynchrone, est appelé en début d'affichage de page, au moment de la sauvegarde,
 * de la suppression, de la création
 * _callback: obligatoire, permet d'appeler une fonction en fin de traitement
 */
function updateDisplayPlugin(_callback) {

}