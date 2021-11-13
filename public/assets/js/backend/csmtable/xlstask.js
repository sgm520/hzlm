define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmtable/xlstask/index' + location.search,
                    add_url: 'csmtable/xlstask/add',
                    edit_url: 'csmtable/xlstask/edit',
                    del_url: 'csmtable/xlstask/del',
                    multi_url: 'csmtable/xlstask/multi',
                    table: 'csmtable_xlstask',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'filesource', title: __('Filesource')},
                        {field: 'filename', title: __('Filename')},
                        {field: 'progress', title: __('Progress')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'b1', title: __('B1')},
                        {field: 'b2', title: __('B2')},
                        {field: 'b3', title: __('B3')},
                        {field: 'b4', title: __('B4')},
                        {field: 'b5', title: __('B5')},
                        {field: 'b6', title: __('B6')},
                        {field: 'b7', title: __('B7')},
                        {field: 'b8', title: __('B8')},
                        {field: 'b9', title: __('B9')},
                        //v2.1.8 增加日志和查询日志调试功能,便于排查问题
                        {
                        	field: 'operate', 
                        	title: __('Operate'), 
                        	table: table, events: Table.api.events.operate, 
                        	formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'submitauditreturn',
                                    text: __('测试生成Excel'),
                                    classname: 'btn btn-xs btn-danger btn-dialog',
                                    icon: 'fa fa-file',
                                    url: 'csmtable/csmgenerate/index',                                   
                                },                                                           
                             ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});