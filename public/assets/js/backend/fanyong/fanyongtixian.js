define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "fanyong/fanyongtixian/index",
                    "table": 'tixian'
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
                        {field: 'user_id', title: '代理id'},
                        {field: 'user_login', title: '代理电话',operate: 'LIKE'},
                        {field: 'al_pay_account', title: '支付宝账号',operate: 'LIKE'},
                        {field: 'al_pay_name', title: '支付宝名称',operate: 'LIKE'},
                        {field: 'money', title: '提现金额'},
                        {field: 'remark', title: '备注'},

                        {
                            field: 'state',
                            title: '状态',
                            formatter: Table.api.formatter.label,
                            searchList: {1:'已结算',2:'审核中',0:'未通过'}
                        },
                        {
                            field: 'tx_time',
                            title: '提现时间',
                            formatter: Table.api.formatter.datetime,
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            sortable: true
                        },
                        {
                            field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: 'agree',
                                    text: __('通过'),
                                    icon: 'fa fa-check',
                                    classname: 'btn btn-xs btn-success btn-ajax',
                                    url: 'tixian/agree',
                                    confirm: '确认通过',
                                    visible: function (row) {
                                        if(row.state  !=2){
                                            return false
                                        }else{
                                            return  true
                                        }
                                    },
                                    success:function(){
                                        table.bootstrapTable('refresh', {});
                                        return true;
                                    },
                                },
                                {
                                    name: 'refuse',
                                    text: __('拒绝'),
                                    icon: 'fa fa-close',
                                    classname: 'btn btn-xs btn-danger btn-ajax',
                                    url: 'tixian/refuse',
                                    visible: function (row) {
                                        if(row.state  !=2){
                                            return false
                                        }else{
                                            return  true
                                        }
                                    },
                                    success:function(){
                                        table.bootstrapTable('refresh', {});
                                        return true;
                                    },
                                },
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                commonSearch: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后


            // 给表单绑定事件
            Form.api.bindevent($("#update-form"), function () {

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {

            },
            formatter: {
                state: function (value, row, index) {
                    console.log(Table.api)
                }
            }
        }
    };
    return Controller;
});
