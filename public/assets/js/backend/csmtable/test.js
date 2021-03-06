define(['jquery', 'bootstrap', 'backend', 'csmtable', 'form','fixedcolumns','tablestickyheader','tablereorderrows','tabletreegrid','xeditable2'], 
function ($, undefined, Backend, Table, Form,fixedcolumns,tablestickyheader,tablereorderrows,tabletreegrid,xeditable2) {
    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'csmtable/test/index' + location.search,
                    add_url: 'csmtable/test/add',
                    edit_url: 'csmtable/test/edit',
                    del_url: 'csmtable/test/del',
                    multi_url: 'csmtable/test/multi',
                    table: 'test',
                }
            });
            var table = $("#table");
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                fixedColumns: true, 
                fixedNumber: 3,
                stickyHeader: true,
                clickToSelect: true,
                treeGrid:false,
                treeShowField: 'title',
                parentIdField: 'pid',
                asyndownload:true,
 
                columns: [
                    [
                        {field: 'state2',checkbox: true},
                        {field: 'id', title: __('Id')},
						//{field: 'pid', title: __('pid')},
                        {field: 'title', title: __('Title'),align: 'left'},      
                        {field: 'content', title: __('Content')},      
                        {field: 'admin_id', title: __('Admin_id')
                        	,editable:true,editabletype:'remote_select',datasource:"auth/admin",datafield:"nickname"
                        },
                        {field: 'category_id', title: __('Category_id'),datasource:"category",visible:false},
                        {field: 'category_ids',title: __('Category_ids'),datasource:"category",visible:false},
                        {field: 'week', title: __('Week'),editable:true,editabletype:'dict_select', searchList: {"monday":__('Week monday'),"tuesday":__('Week tuesday'),"wednesday":__('Week wednesday')}},
                        {field: 'flag', title: __('Flag'), searchList: {"hot":__('Flag hot'),"index":__('Flag index'),"recommend":__('Flag recommend')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'genderdata',editable:true,editabletype:'dict_select', title: __('Genderdata'), searchList: {"male":__('Genderdata male'),"female":__('Genderdata female')}/*, formatter: Table.api.formatter.normal*/},
                        {field: 'hobbydata', title: __('Hobbydata'), searchList: {"music":__('Hobbydata music'),"reading":__('Hobbydata reading'),"swimming":__('Hobbydata swimming')}, operate:'FIND_IN_SET', formatter: Table.api.formatter.label},
                        {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'attachfile', title: __('Attachfile')},
                        {field: 'keywords', title: __('Keywords')},
                        {field: 'description', title: __('Description'), editable: true},
                        {field: 'city', title: __('City'), editable: true},
                        {field: 'price', title: __('Price'), operate:'BETWEEN',showsum:true,sortable:true, editable: true},
                        {field: 'views', title: __('Views'),showsum:true,sumfield:"totalviews",sortable:true},
                        {field: 'startdate', title: __('Startdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'activitytime', title: __('Activitytime'), operate:'RANGE', addclass:'datetimerange', editable: true},
                        {field: 'year', title: __('Year')},
                        {field: 'times', title: __('Times')},
                        {field: 'refreshtime', title: __('Refreshtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'switch', title: __('Switch'), searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'state', title: __('State'), searchList: {"0":__('State 0'),"1":__('State 1'),"2":__('State 2')}, formatter: Table.api.formatter.normal},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
 

            // ?????????????????????
            Table.api.bindevent(table);
 
        },
        recyclebin: function () {
            // ???????????????????????????
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // ???????????????
            table.bootstrapTable({
                url: 'csmtable/test/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'csmtable/test/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'csmtable/test/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // ?????????????????????
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