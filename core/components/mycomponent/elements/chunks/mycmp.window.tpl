/**
* JS file for [[+packageName]] extra
*
* Copyright [[+copyright]] by [[+author]] [[+email]]
* Created on [[+createdon]]
*
[[+license]]
* @package [[+packageNameLower]]
*/

/* These are for LexiconHelper:
 $modx->lexicon->load('[[+packageNameLower]]:default');
 include '[[+packageNameLower]].class.php'
 */
 
 MODx.Window.[[+Object]] = function(config) {
    config = config || {};
	var baseParams,wintitle;
	if(config.winaction == 'Create'){
		baseParams = {
			action: 'mgr/[[+object]]/create'
		};
		wintitle = _('[[+object]]_create');
	} else {
		baseParams = {
			action: 'mgr/[[+object]]/update'
			,id: config.recid
		};
		wintitle = _('[[+object]]_update');
	}
    Ext.applyIf(config,{
		title: wintitle
		,id: '[[+object]]-window'
		,url: [[+packageName]].config.connector_url
		,baseParams: baseParams
		,fields: this.getFields(config)
		,saveBtnText: _('save')
		,width: 600
		,height: 400
        ,listeners: {
        	'success': {fn:this.success,scope:this}
			//,'show': {fn:this.rte,scope:this}
        }
	});
	MODx.Window.[[+Object]].superclass.constructor.call(this,config);

};

Ext.extend(MODx.Window.[[+Object]],MODx.Window,{
	// rte: function(){
// 		if(MODx.loadRTE && this.config.nWin){
// 			MODx.loadRTE('window-testimonial');
// 		}
// 	}
	getFields: function(config){
		var f = [{
			name: 'id'
			,xtype: 'hidden'
		},{
			name: 'name'
			,xtype: 'textfield'
			,fieldLabel: _('name')
			,anchor: '100%'
		},{
			name: 'description'
			,xtype: 'textarea'
			,fieldLabel: _('description')
			,anchor: '100%'
		}];
		
		return f;
	}
    ,success: function(o) {
        var grid = Ext.getCmp('[[+packageNameLower]]-grid-[[+object]]');
		grid.getStore().reload({
        	callback: function(){
        		grid.getView().refresh();
        	}
        })
    }
});