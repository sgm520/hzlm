define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "article/gonggao/index",
                    "add_url": "article/gonggao/add",
                    "edit_url": "article/gonggao/edit",
                    "del_url": "article/gonggao/del",
                    "table": 'gonggao'
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
                        {field: 'id', title: 'ID'},
                        {field: 'title', title: __('Title')},
                        {field: 'context', title: __('内容')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
