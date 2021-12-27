define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload','editable'], function ($, undefined, Backend, Table, Form, Upload,editable) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "fanyong/fanyong/index",
                    "add_url": "fanyong/fanyong/add",
                    "edit_url": "fanyong/fanyong/edit",
                    "del_url": "fanyong/fanyong/del",
                    "multi_url": "fanyong/fanyong/multi",
                    "table": 'fanyong'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'fanyong.create_time',
                columns: [
                    [{
                        checkbox: true
                    },
                        {field: 'id', title: 'ID'},
                        {field: 'name', title: __('产品名称'),operate: 'LIKE'},
                        {field: 'xilie.name', title: __('产品系列'),searchList: $.getJSON("ajax/fanyongstyle")},
                        {field: 'back_money', title: __('结算佣金'), operate: false,editable:true},
                        {field: 'logo', title: __('logo'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},



                        {field: 'status', title: __('是否上架'), formatter: Table.api.formatter.toggle,searchList: {
                                0:'已下架',
                                1:'上架中'
                            }},
                        {field: 'list_order', title: __('排序'),operate: false},
                        {field: 'create_time', title: __('创建时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'update_time', title: __('更新时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                commonSearch: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后

            //一键审批

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
                $(document).on('click', "input[name='row[status]']", function () {
                    var name = $("input[name='row[status]']");
                    console.log(name)
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[status]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            formatter:{
                status: function (value, row, index) {
                    return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + row.url + '</a>';
                }
            }
        }
    };
    return Controller;
});
