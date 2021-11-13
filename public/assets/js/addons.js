define([], function () {
    require.config({
    paths: {
    	'csmtable': '../addons/csmtable/js/csmtable',
        'fixedcolumns': '../addons/csmtable/js/bootstrap-table-fixed-columns',
        'tablereorderrows': '../addons/csmtable/js/bootstrap-table-reorder-rows',
        'tablestickyheader': '../addons/csmtable/js/bootstrap-table-sticky-header',
        'tabletreegrid': '../addons/csmtable/js/bootstrap-table-treegrid',
        'jquerytablednd': '../addons/csmtable/js/jquery.tablednd.min',
        'jquerytreegrid': '../addons/csmtable/js/jquery.treegrid.min',
        'xeditable2': '../addons/csmtable/js/select2',
    },
    shim: {
        'csmtable': {
            deps: ["css!../addons/csmtable/css/csmtable.css", 'bootstrap-table']
        },
        'fixedcolumns': {
            deps: ["css!../addons/csmtable/css/bootstrap-table-fixed-columns.css", 'bootstrap-table']
        },
        'tablereorderrows':{
        	deps: ["css!../addons/csmtable/css/bootstrap-table-reorder-rows.css",'jquerytablednd', 'bootstrap-table']
        },
        'tablestickyheader':{
        	deps: ["css!../addons/csmtable/css/bootstrap-table-sticky-header.css", 'bootstrap-table']
        },
        'tabletreegrid':{
        	deps: ["css!../addons/csmtable/css/jquery.treegrid.min.css",'jquerytreegrid', 'bootstrap-table']
        },
        'xeditable2':{
        	deps: ["css!../addons/csmtable/css/select2.css","css!../addons/csmtable/css/select2-bootstrap.css",'bootstrap-table','editable']
        },
    }
});
require.config({
    paths: {
        'editable': '../libs/bootstrap-table/dist/extensions/editable/bootstrap-table-editable.min',
        'x-editable': '../addons/editable/js/bootstrap-editable.min',
    },
    shim: {
        'editable': {
            deps: ['x-editable', 'bootstrap-table']
        },
        "x-editable": {
            deps: ["css!../addons/editable/css/bootstrap-editable.css"],
        }
    }
});
if ($("table.table").size() > 0) {
    require(['editable', 'table'], function (Editable, Table) {
        $.fn.bootstrapTable.defaults.onEditableSave = function (field, row, oldValue, $el) {
            var data = {};
            data["row[" + field + "]"] = row[field];
            Fast.api.ajax({
                url: this.extend.edit_url + "/ids/" + row[this.pk],
                data: data
            });
        };
    });
}
window.UMEDITOR_HOME_URL = Config.__CDN__ + "/assets/addons/umeditor/";
require.config({
    paths: {
        'umeditor': '../addons/umeditor/umeditor',
        'umeditor.config': '../addons/umeditor/umeditor.config',
        'umeditor.lang': '../addons/umeditor/lang/zh-cn/zh-cn',
    },
    shim: {
        'umeditor': {
            deps: [
                'umeditor.config',
                'css!../addons/umeditor/themes/default/css/umeditor.min.css'
            ],
            exports: 'UM',
        },
        'umeditor.lang': ['umeditor']
    }
});

require(['form', 'upload'], function (Form, Upload) {
    //监听上传文本框的事件
    $(document).on("edui.file.change", ".edui-image-file", function (e, up, me, input, callback) {
        for (var i = 0; i < this.files.length; i++) {
            Upload.api.send(this.files[i], function (data) {
                var url = data.url;
                me.uploadComplete(JSON.stringify({url: url, state: "SUCCESS"}));
            });
        }
        up.updateInput(input);
        me.toggleMask("Loading....");
        callback && callback();
    });
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        require(['umeditor', 'umeditor.lang'], function (UME, undefined) {

            //重写编辑器加载
            UME.plugins['autoupload'] = function () {
                var me = this;
                me.setOpt('pasteImageEnabled', true);
                me.setOpt('dropFileEnabled', true);
                var sendAndInsertImage = function (file, editor) {
                    try {
                        Upload.api.send(file, function (data) {
                            var url = Fast.api.cdnurl(data.url, true);
                            editor.execCommand('insertimage', {
                                src: url,
                                _src: url
                            });
                        });
                    } catch (er) {
                    }
                };

                function getPasteImage(e) {
                    return e.clipboardData && e.clipboardData.items && e.clipboardData.items.length == 1 && /^image\//.test(e.clipboardData.items[0].type) ? e.clipboardData.items : null;
                }

                function getDropImage(e) {
                    return e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
                }

                me.addListener('ready', function () {
                    if (window.FormData && window.FileReader) {
                        var autoUploadHandler = function (e) {
                            var hasImg = false,
                                items;
                            //获取粘贴板文件列表或者拖放文件列表
                            items = e.type == 'paste' ? getPasteImage(e.originalEvent) : getDropImage(e.originalEvent);
                            if (items) {
                                var len = items.length,
                                    file;
                                while (len--) {
                                    file = items[len];
                                    if (file.getAsFile)
                                        file = file.getAsFile();
                                    if (file && file.size > 0 && /image\/\w+/i.test(file.type)) {
                                        sendAndInsertImage(file, me);
                                        hasImg = true;
                                    }
                                }
                                if (hasImg)
                                    return false;
                            }

                        };
                        me.getOpt('pasteImageEnabled') && me.$body.on('paste', autoUploadHandler);
                        me.getOpt('dropFileEnabled') && me.$body.on('drop', autoUploadHandler);

                        //取消拖放图片时出现的文字光标位置提示
                        me.$body.on('dragover', function (e) {
                            if (e.originalEvent.dataTransfer.types[0] == 'Files') {
                                return false;
                            }
                        });
                    }
                });

            };
            $(".editor", form).each(function () {
                var id = $(this).attr("id");
                $(this).removeClass('form-control');
                UME.list[id] = UME.getEditor(id, {
                    serverUrl: Fast.api.fixurl('/addons/umeditor/api/'),
                    initialFrameWidth: '100%',
                    zIndex: 90,
                    xssFilterRules: false,
                    outputXssFilter: false,
                    inputXssFilter: false,
                    autoFloatEnabled: false,
                    imageUrl: '',
                    imagePath: Config.upload.cdnurl
                });
            });
        });
    }
});

});