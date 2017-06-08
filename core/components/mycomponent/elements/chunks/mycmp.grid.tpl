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

[[+packageName]].[[+Object]]Window = function(config){
	config = config || {};
	var [[+Object]]Window = Ext.getCmp('[[+object]]-window');
	config.nWin = false;
	if([[+Object]]Window !== undefined){
		[[+Object]]Window.destroy();
	}
	config.nWin = true;
	[[+Object]]Window = new MODx.Window.[[+Object]](config);

	if(config.winaction === 'Update'){
	    MODx.Ajax.request({
	        url: [[+packageName]].config.connector_url
	        ,params: {
	            action: 'mgr/[[+object]]/get'
	            ,id: config.recid
	        }
	        ,listeners: {
	            'success': {fn:function(r) {
					[[+Object]]Window.reset();
	                [[+Object]]Window.setValues(r.object);
					[[+Object]]Window.show();
	            },scope:this}
	        }
	    });
	} else {
		[[+Object]]Window.reset();
		[[+Object]]Window.show();
	}
};

[[+packageName]].grid.[[+Object]] = function (config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        url: [[+packageName]].config.connector_url
        , baseParams: {
          action: 'mgr/[[+object]]/getlist'
          ,thread: config.thread
        }
        , pageSize: 20
        , fields: [
            {name:'id', sortType: Ext.data.SortTypes.asInt}
            , {name: 'name', sortType: Ext.data.SortTypes.asUCString}
         ]
        , paging: true
        , autosave: false
        , remoteSort: false
        , autoExpandColumn: 'description'
        , cls: '[[+packageNameLower]]-grid-[[+object]]'
        , sm: this.sm
        , columns: [this.sm, {
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 50
        }, {
            header: _('name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 100
        }]
        ,viewConfig: {
            forceFit: true,
            enableRowBody: true,
            showPreview: true,
            getRowClass: function (rec, ri, p) {
                var cls = '[[+packageNameLower]]-row';

                if (this.showPreview) {
                    return cls + ' [[+packageNameLower]]-resource-expanded';
                }
                return cls + ' [[+packageNameLower]]-resource-collapsed';
            }
        }
        , tbar: [{
                text: _('[[+object]]_new')
    			,id: 'btn-new-[[+object]]'
                ,handler: this.create[[+Object]]
                ,scope: this
                ,cls:'primary-button'
            }
        ]
    });
    [[+packageName]].grid.[[+Object]].superclass.constructor.call(this, config)
};
Ext.extend([[+packageName]].grid.[[+Object]], MODx.grid.Grid, {
    getMenu: function() {
        var r = this.getSelectionModel().getSelected();
		
        var p = r.json.cls;
        
        var m = [];
        if (this.getSelectionModel().getCount() > 1) {
            
            m.push({
                text: _('selected_remove')
                ,handler: this.remove[[+Object]]
                ,scope: this
            });
        } else {
            if (p.indexOf('pupdate') != -1) {
                m.push({
                    text: _('update')
                    ,handler: this.update[[+Object]]
                });
            }
            
            if (p.indexOf('premove') != -1) {
                if (m.length > 0) m.push('-');
                m.push({
                    text: _('remove')
                    ,handler: this.remove[[+Object]]
                });
            }
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    ,create[[+Object]]: function() {
 		[[+packageName]].[[+Object]]Window({winaction: 'Create',categoryid: this.config.categoryid});
    }
 
    ,remove[[+Object]]: function() {
        MODx.msg.confirm({
            title: _('remove')
            ,text: _('[[+object]]_confirm_remove')
            ,url: this.config.url
            ,params: {
                action: 'mgr/[[+object]]/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
 
    ,update[[+Object]]: function() {
        [[+packageName]].[[+Object]]Window({recid: this.menu.record.id, winaction: 'Update'}); 
    }
    
});
Ext.reg('[[+packageNameLower]]-grid-[[+object]]', [[+packageName]].grid.[[+Object]]);

